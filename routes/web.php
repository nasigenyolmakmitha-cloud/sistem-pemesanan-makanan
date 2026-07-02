<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Meja;
use App\Models\SesiPemesanan;
use App\Models\Pemesan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| PELANGGAN ROUTES (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/admin/login');
});

Route::prefix('pesan/{qr_token}')->group(function () {

    // H-01: Input Nama
    Route::get('/', function ($qr_token) {
        $meja = Meja::where('qr_token', $qr_token)->firstOrFail();
        $sesiAktif = SesiPemesanan::where('meja_id', $meja->id)->where('status', 'aktif')->first();

        if ($sesiAktif) {
            if (session('sesi_id') == $sesiAktif->id) {
                return redirect("/pesan/{$qr_token}/menu");
            } else {
                return view('pelanggan.meja_diambil', ['meja' => $meja]);
            }
        }
        return view('pelanggan.input-nama', ['meja' => $meja]);
    });

    // POST: Simpan Nama (First Person)
    Route::post('/nama', function (Request $request, $qr_token) {
        $request->validate(['nama' => 'required|string|min:2|max:100']);
        $meja = Meja::where('qr_token', $qr_token)->firstOrFail();

        // Ensure no other active session exists
        $sesi = SesiPemesanan::firstOrCreate(
            ['meja_id' => $meja->id, 'status' => 'aktif'],
            ['kode_sesi' => Str::uuid()->toString(), 'dibuka_pada' => now()]
        );

        $pemesan = Pemesan::create(['sesi_id' => $sesi->id, 'nama' => $request->nama]);
        
        session([
            'sesi_id'       => $sesi->id,
            'meja_id'       => $meja->id,
            'pemesan_id'    => $pemesan->id, // Default active pemesan
            'nama_pemesan'  => $request->nama,
            'qr_token'      => $qr_token,
            'keranjang'     => [],
        ]);

        return redirect("/pesan/{$qr_token}/menu");
    });

    // POST: Tambah Orang Baru
    Route::post('/tambah-orang', function (Request $request, $qr_token) {
        $request->validate(['nama' => 'required|string|min:2|max:100']);
        if (!session('sesi_id')) return redirect("/pesan/{$qr_token}");

        $pemesan = Pemesan::create([
            'sesi_id' => session('sesi_id'),
            'nama'    => $request->nama
        ]);
        
        // Switch to newly added person
        session(['pemesan_id' => $pemesan->id, 'nama_pemesan' => $pemesan->nama]);

        return back()->with('success', "{$request->nama} ditambahkan.");
    });

    // POST: Ganti Orang Aktif
    Route::post('/ganti-orang', function (Request $request, $qr_token) {
        $pemesan = Pemesan::findOrFail($request->pemesan_id);
        if ($pemesan->sesi_id != session('sesi_id')) abort(403);
        
        session(['pemesan_id' => $pemesan->id, 'nama_pemesan' => $pemesan->nama]);
        return back();
    });

    // POST: Hapus Orang
    Route::post('/hapus-orang', function (Request $request, $qr_token) {
        $pemesan_id = $request->pemesan_id;
        $sesi_id = session('sesi_id');
        
        $pemesan = Pemesan::where('id', $pemesan_id)->where('sesi_id', $sesi_id)->first();
        if ($pemesan) {
            $count = Pemesan::where('sesi_id', $sesi_id)->count();
            if ($count <= 1) {
                // Jika orang terakhir dihapus, batalkan seluruh sesi
                // Kembalikan stok untuk semua pesanan yang belum dibayar di sesi ini sebelum dihapus
                $sesi = SesiPemesanan::with('pesanan.detailPesanan')->find($sesi_id);
                if ($sesi) {
                    foreach ($sesi->pesanan as $pesanan) {
                        if ($pesanan->status !== 'dibayar') {
                            foreach ($pesanan->detailPesanan as $detail) {
                                if ($detail->menu_id) {
                                    $menu = Menu::find($detail->menu_id);
                                    if ($menu) {
                                        $menu->stok += $detail->jumlah;
                                        $menu->save();
                                    }
                                }
                            }
                        }
                    }
                    $sesi->delete();
                }
                session()->forget(['sesi_id', 'pemesan_id', 'nama_pemesan', 'last_pesanan_ids', 'keranjang']);
                return redirect("/pesan/{$qr_token}");
            } else {
                // Kembalikan stok pesanan orang tsb jika statusnya belum dibayar sebelum orang tsb dihapus
                $pesanans = Pesanan::with('detailPesanan')->where('pemesan_id', $pemesan_id)->get();
                foreach ($pesanans as $pesanan) {
                    if ($pesanan->status !== 'dibayar') {
                        foreach ($pesanan->detailPesanan as $detail) {
                            if ($detail->menu_id) {
                                $menu = Menu::find($detail->menu_id);
                                if ($menu) {
                                    $menu->stok += $detail->jumlah;
                                    $menu->save();
                                }
                            }
                        }
                    }
                }
                $pemesan->delete();
                if (session('pemesan_id') == $pemesan_id) {
                    $firstPerson = Pemesan::where('sesi_id', $sesi_id)->first();
                    session(['pemesan_id' => $firstPerson->id, 'nama_pemesan' => $firstPerson->nama]);
                }
                // Hapus keranjang orang tsb
                $keranjang = session('keranjang', []);
                foreach ($keranjang as $id => $item) {
                    if ($item['pemesan_id'] == $pemesan_id) {
                        unset($keranjang[$id]);
                    }
                }
                session(['keranjang' => $keranjang]);
            }
        }
        return back()->with('success', 'Anggota berhasil dihapus.');
    });

    // POST: Batal Sesi
    Route::post('/batal-sesi', function (Request $request, $qr_token) {
        $sesi_id = session('sesi_id');
        if ($sesi_id) {
            // KEAMANAN BARU: Cegah batal sesi jika sudah ada pesanan yang masuk ke kasir
            $adaPesanan = \App\Models\Pesanan::where('sesi_id', $sesi_id)->exists();
            if ($adaPesanan) {
                return back()->with('error', 'Tidak dapat membatalkan sesi karena pesanan sudah masuk ke kasir.');
            }

            $sesi = SesiPemesanan::with('pesanan.detailPesanan')->find($sesi_id);
            if ($sesi) {
                // Kembalikan stok (logika bawaan Anda)
                foreach ($sesi->pesanan as $pesanan) {
                    if ($pesanan->status !== 'dibayar') {
                        foreach ($pesanan->detailPesanan as $detail) {
                            if ($detail->menu_id) {
                                $menu = Menu::find($detail->menu_id);
                                if ($menu) {
                                    $menu->stok += $detail->jumlah;
                                    $menu->save();
                                }
                            }
                        }
                    }
                }
                $sesi->delete();
            }
            session()->forget(['sesi_id', 'pemesan_id', 'nama_pemesan', 'last_pesanan_ids', 'keranjang']);
        }
        return redirect("/pesan/{$qr_token}")->with('success', 'Sesi pesanan dibatalkan.');
    });

    // H-02: Menu
    Route::get('/menu', function ($qr_token) {
        if (!session('pemesan_id')) return redirect("/pesan/{$qr_token}");
        $meja  = Meja::where('qr_token', $qr_token)->firstOrFail();
        $menus = Menu::all();
        $keranjang = session('keranjang', []);
        $semuaPemesan = Pemesan::where('sesi_id', session('sesi_id'))->get();
        
        // CEK DATA: Apakah meja ini sudah pernah mengirim pesanan?
        $punyaPesanan = \App\Models\Pesanan::where('sesi_id', session('sesi_id'))->exists();
        
        // Kirim variabel $punyaPesanan ke view
        return view('pelanggan.menu', compact('menus', 'meja', 'keranjang', 'semuaPemesan', 'punyaPesanan'));
    });

    // Keranjang: Tambah item (Attributed to active person)
    Route::post('/keranjang/tambah', function (Request $request, $qr_token) {
        $request->validate(['menu_id' => 'required|exists:menus,id']);
        $menu = Menu::findOrFail($request->menu_id);
        $keranjang = session('keranjang', []);
        $pemesan_id = session('pemesan_id');
        $id = $request->menu_id . '_' . $pemesan_id;

        // Hitung total menu ini yang sudah ada di keranjang seluruh sesi meja
        $totalInCart = 0;
        foreach ($keranjang as $cartItem) {
            if ($cartItem['menu_id'] == $menu->id) {
                $totalInCart += $cartItem['jumlah'];
            }
        }

        // Cek apakah penambahan ini melebihi stok yang tersedia
        if (($totalInCart + 1) > $menu->stok) {
            $message = "{$menu->nama} tersedia {$menu->stok} porsi.<br>silahkan sesuaikan pesanan anda.";
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return back()->with('stok_error', $message);
        }

        if (isset($keranjang[$id])) {
            $keranjang[$id]['jumlah']++;
        } else {
            $keranjang[$id] = [
                'menu_id'    => $request->menu_id,
                'pemesan_id' => $pemesan_id,
                'nama'       => $menu->nama,
                'harga'      => $menu->harga,
                'jumlah'     => 1,
                'untuk'      => session('nama_pemesan')
            ];
        }
        session(['keranjang' => $keranjang]);
        if (request()->ajax()) {
            return response()->json(['success' => true, 'jumlah' => $keranjang[$id]['jumlah']]);
        }
        return back();
    });

    // Keranjang: Update jumlah
    Route::post('/keranjang/update', function (Request $request, $qr_token) {
        $keranjang = session('keranjang', []);
        $id = $request->cart_id; // Using cart_id which is menu_id + pemesan_id
        if (isset($keranjang[$id])) {
            $targetJumlah = max(1, (int) $request->jumlah);
            $menuId = $keranjang[$id]['menu_id'];
            $menu = Menu::findOrFail($menuId);

            // Hitung total menu ini tidak termasuk item yang sedang di-update
            $totalInCartExcludingItem = 0;
            foreach ($keranjang as $cartItemId => $cartItem) {
                if ($cartItem['menu_id'] == $menuId && $cartItemId !== $id) {
                    $totalInCartExcludingItem += $cartItem['jumlah'];
                }
            }

            if (($totalInCartExcludingItem + $targetJumlah) > $menu->stok) {
                $message = "{$menu->nama} tersedia {$menu->stok} porsi.<br>silahkan sesuaikan pesanan anda.";
                return back()->with('stok_error', $message);
            }

            $keranjang[$id]['jumlah'] = $targetJumlah;
        }
        session(['keranjang' => $keranjang]);
        return back();
    });

    // Keranjang: Update AJAX (No Refresh)
    Route::post('/keranjang/update-ajax', function (Request $request, $qr_token) {
        $keranjang = session('keranjang', []);
        $id = $request->cart_id;
        if (isset($keranjang[$id])) {
            $targetJumlah = max(1, (int) $request->jumlah);
            $menuId = $keranjang[$id]['menu_id'];
            $menu = Menu::findOrFail($menuId);

            // Hitung total menu ini tidak termasuk item yang sedang di-update
            $totalInCartExcludingItem = 0;
            foreach ($keranjang as $cartItemId => $cartItem) {
                if ($cartItem['menu_id'] == $menuId && $cartItemId !== $id) {
                    $totalInCartExcludingItem += $cartItem['jumlah'];
                }
            }

            if (($totalInCartExcludingItem + $targetJumlah) > $menu->stok) {
                $message = "{$menu->nama} tersedia {$menu->stok} porsi.<br>silahkan sesuaikan pesanan anda.";
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }

            $keranjang[$id]['jumlah'] = $targetJumlah;
        }
        session(['keranjang' => $keranjang]);
        
        $total = 0;
        foreach($keranjang as $item) {
            $total += $item['harga'] * $item['jumlah'];
        }

        return response()->json([
            'success' => true,
            'jumlah'  => $keranjang[$id]['jumlah'] ?? 0,
            'subtotal'=> number_format(($keranjang[$id]['harga'] ?? 0) * ($keranjang[$id]['jumlah'] ?? 0), 0, ',', '.'),
            'total'   => number_format($total, 0, ',', '.')
        ]);
    });

    // Keranjang: Hapus item
    Route::post('/keranjang/hapus', function (Request $request, $qr_token) {
        $keranjang = session('keranjang', []);
        unset($keranjang[$request->cart_id]);
        session(['keranjang' => $keranjang]);
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    });

// H-03: Halaman Keranjang (REVISI: Kirim data semua pemesan di meja ini)
Route::get('/keranjang', function ($qr_token) {
    if (!session('pemesan_id')) return redirect("/pesan/{$qr_token}");
    $meja         = Meja::where('qr_token', $qr_token)->firstOrFail();
    $semuaPemesan = Pemesan::where('sesi_id', session('sesi_id'))->get(); // Mengambil semua orang di meja tsb
    return view('pelanggan.keranjang', compact('meja', 'semuaPemesan'));
});

// POST: Submit Pesanan (REVISI: Menyimpan pesanan split bill massal sesuai pemiliknya)
Route::post('/pesan', function (Request $request, $qr_token) {
    $cartDataJson = $request->input('cart_data');
    if (empty($cartDataJson)) return back();
    
    $cartArray = json_decode($cartDataJson, true);
    if (empty($cartArray)) return back();
    
    if (!session('sesi_id')) return redirect("/pesan/{$qr_token}");

    $keranjang = [];
    foreach($cartArray as $item) {
        $keranjang[] = [
            'menu_id'    => $item['id'],
            'nama'       => $item['nama'],
            'harga'      => $item['harga'],
            'jumlah'     => $item['jumlah'],
            'pemesan_id' => $item['pemesan_id'], // Membaca pemilik asli dari item keranjang tersebut
        ];
    }

    // 1. Keamanan: Cek Ulang Stok Live di Database
    $menuIds = array_column($keranjang, 'menu_id');
    $menus = \App\Models\Menu::whereIn('id', $menuIds)->get()->keyBy('id');
    
    $unavailableItems = [];
    foreach ($keranjang as $id => $item) {
        $menu = $menus->get($item['menu_id']);
        if (!$menu || $menu->stok < $item['jumlah']) {
            $sisaStok = $menu ? $menu->stok : 0;
            $unavailableItems[] = "{$item['nama']} tersedia {$sisaStok} porsi.";
            unset($keranjang[$id]);
        }
    }

    if (count($unavailableItems) > 0) {
        $message = implode("<br>", $unavailableItems) . "<br>silahkan sesuaikan pesanan anda.";
        return back()->with('stok_error', $message);
    }

    // 2. Buat record Pesanan (Dikelompokkan otomatis berdasarkan ID Pemesan)
    $lastPesananIds = [];
    $groupedByPemesan = [];
    foreach ($keranjang as $item) {
        $groupedByPemesan[$item['pemesan_id']][] = $item;
    }

    // Ambil semua catatan dari form
    $catatanArray = $request->input('catatan', []);

    foreach ($groupedByPemesan as $pId => $items) {
        $pesanan = Pesanan::create([
            'pemesan_id' => $pId,
            'sesi_id'    => session('sesi_id'),
            'status'     => 'menunggu',
            'catatan'    => isset($catatanArray[$pId]) ? $catatanArray[$pId] : null, // Cocokkan catatan dengan pemiliknya
        ]);
        $lastPesananIds[] = $pesanan->id;

        foreach ($items as $item) {
            DetailPesanan::create([
                'pesanan_id'       => $pesanan->id,
                'menu_id'          => $item['menu_id'],
                'jumlah'           => $item['jumlah'],
                'harga_saat_pesan' => $item['harga'],
                'subtotal'         => $item['harga'] * $item['jumlah'],
            ]);

            // Kurangi stok menu di database secara otomatis
            $menu = $menus->get($item['menu_id']);
            if ($menu) {
                $menu->stok = max(0, $menu->stok - $item['jumlah']);
                $menu->save();
            }
        }
    }

    session(['last_pesanan_ids' => $lastPesananIds]);
    session()->flash('clear_cart', true);

    return redirect("/pesan/{$qr_token}/konfirmasi");
});

    // H-04: Konfirmasi (Tampilkan Semuanya)
    Route::get('/konfirmasi', function ($qr_token) {
        if (!session('pemesan_id')) return redirect("/pesan/{$qr_token}");
        $meja  = Meja::where('qr_token', $qr_token)->firstOrFail();
        
        // Tarik SEMUA pesanan milik sesi meja ini agar tagihan/riwayat tidak hilang
        $pesanans = Pesanan::with('detailPesanan.menu', 'pemesan')
            ->where('sesi_id', session('sesi_id'))
            ->orderBy('id', 'asc') // Urutkan dari pesanan awal ke terbaru
            ->get();
            
        return view('pelanggan.konfirmasi', compact('meja', 'pesanans'));
    });

    // AJAX: Cek Status Pesanan & Sesi
    Route::get('/status', function ($qr_token) {
        $sesiId = session('sesi_id');
        $sesi = SesiPemesanan::find($sesiId);
        
        if (!$sesi) {
            return response()->json(['success' => false, 'session_status' => 'selesai']);
        }

        // Ambil status dari SELURUH pesanan dalam sesi ini
        $pesanans = Pesanan::where('sesi_id', $sesiId)
            ->select('id', 'status')
            ->get();

        return response()->json([
            'success'  => true,
            'pesanans' => $pesanans,
            'session_status' => $sesi->status
        ]);
    });

    // H-05: Terima Kasih (Sesi Selesai)
    Route::get('/terimakasih', function ($qr_token) {
        $meja = Meja::where('qr_token', $qr_token)->firstOrFail();
        // Clear customer session data for this table/session
        session()->forget(['sesi_id', 'pemesan_id', 'nama_pemesan', 'last_pesanan_ids', 'keranjang']);
        return view('pelanggan.terimakasih', compact('meja'));
    });
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Login/Logout)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/login', function () {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect('/admin/dashboard') : redirect('/kasir/dashboard');
        }
        return view('admin.auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            // Redirect berdasarkan role
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->intended('/kasir/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau Password salah.']);
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (role: admin)
| Akses: Manajemen Meja, Manajemen Menu, Statistik Penjualan
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Admin (Statistik)
    Route::get('/dashboard', function () {
        // Statistik keseluruhan
        $totalMenu = Menu::count();
        $totalMeja = Meja::count();
        $sesiAktif = SesiPemesanan::where('status', 'aktif')->count();
        
        // Pendapatan hari ini
        $hariIni = now()->toDateString();
        $pendapatanHariIni = DetailPesanan::whereHas('pesanan', function ($q) use ($hariIni) {
            $q->whereHas('sesi', function ($q2) use ($hariIni) {
                $q2->where('status', 'selesai')->whereDate('ditutup_pada', $hariIni);
            });
        })->sum('subtotal');
        
        // Pendapatan bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $pendapatanBulanIni = DetailPesanan::whereHas('pesanan', function ($q) use ($bulanIni, $tahunIni) {
            $q->whereHas('sesi', function ($q2) use ($bulanIni, $tahunIni) {
                $q2->where('status', 'selesai')
                    ->whereMonth('ditutup_pada', $bulanIni)
                    ->whereYear('ditutup_pada', $tahunIni);
            });
        })->sum('subtotal');

        // Total pendapatan semua waktu
        $totalPendapatan = DetailPesanan::whereHas('pesanan', function ($q) {
            $q->whereHas('sesi', function ($q2) {
                $q2->where('status', 'selesai');
            });
        })->sum('subtotal');

        // Total sesi selesai
        $totalSesiSelesai = SesiPemesanan::where('status', 'selesai')->count();

        // Menu terlaris (top 5)
        $menuTerlaris = DetailPesanan::select('menu_id', \DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('pesanan', function ($q) {
                $q->whereHas('sesi', fn($q2) => $q2->where('status', 'selesai'));
            })
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->with('menu')
            ->get();

        // Pendapatan 7 hari terakhir (untuk chart)
        $pendapatan7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $total = DetailPesanan::whereHas('pesanan', function ($q) use ($tanggal) {
                $q->whereHas('sesi', function ($q2) use ($tanggal) {
                    $q2->where('status', 'selesai')->whereDate('ditutup_pada', $tanggal);
                });
            })->sum('subtotal');
            $pendapatan7Hari[] = [
                'tanggal' => now()->subDays($i)->format('d/m'),
                'label' => now()->subDays($i)->format('D'),
                'total' => (float)$total,
            ];
        }

        return view('admin.dashboard', compact(
            'totalMenu', 'totalMeja', 'sesiAktif', 
            'pendapatanHariIni', 'pendapatanBulanIni', 'totalPendapatan',
            'totalSesiSelesai', 'menuTerlaris', 'pendapatan7Hari'
        ));
    });

// Manajemen Menu
Route::get('/menu', function () {
    $query = Menu::query();

    // 1. Filter Pencarian Nama (Jika diisi)
    if (request()->filled('search')) {
        $query->where('nama', 'like', '%' . request('search') . '%');
    }

    // 2. Filter Kategori (Jika dipilih)
    if (request()->filled('kategori')) {
        $query->where('kategori', request('kategori'));
    }

    // 3. Ambil data dengan urutan terbaru
    $menus = $query->orderBy('id', 'desc')->get();

    return view('admin.menu.index', compact('menus'));
});

    Route::get('/menu/create', function () {
        return view('admin.menu.create');
    });

    Route::post('/menu', function (Request $request) {
        $data = $request->validate([
            'nama'      => 'required|string|max:150',
            'kategori'  => 'required|string|max:50',
            'harga'     => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto'      => 'required|image|max:2048',
            'stok'      => 'nullable|integer|min:0',
        ]);
        $data['stok'] = $request->input('stok', 0);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }
        Menu::create($data);
        return redirect('/admin/menu')->with('success', 'Menu berhasil ditambahkan!');
    });

    Route::get('/menu/{id}/edit', function ($id) {
        $menu = Menu::findOrFail($id);
        return view('admin.menu.edit', compact('menu'));
    });

    Route::put('/menu/{id}', function (Request $request, $id) {
        $menu = Menu::findOrFail($id);
        $data = $request->validate([
            'nama'      => 'required|string|max:150',
            'kategori'  => 'required|string|max:50',
            'harga'     => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|max:2048',
            'stok'      => 'nullable|integer|min:0',
        ]);
        $data['stok'] = $request->input('stok', 0);
        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }
        $menu->update($data);
        return redirect('/admin/menu')->with('success', 'Menu berhasil diperbarui!');
    });

    Route::delete('/menu/{id}', function ($id) {
        $menu = Menu::findOrFail($id);
        if ($menu->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->foto);
        }
        $menu->delete();
        return redirect('/admin/menu')->with('success', 'Menu berhasil dihapus!');
    });

    // Manajemen Meja
    Route::get('/meja', function () {
        $mejas = Meja::all();
        return view('admin.meja.index', compact('mejas'));
    });

    Route::post('/meja', function (Request $request) {
        $request->validate(['nomor_meja' => 'required|string|max:20']);
        Meja::create([
            'nomor_meja' => $request->nomor_meja,
            'qr_token'   => Str::uuid()->toString(),
        ]);
        return back()->with('success', 'Meja berhasil ditambahkan!');
    });

    Route::delete('/meja/{id}', function ($id) {
        $meja = Meja::findOrFail($id);
        $hasSesiAktif = SesiPemesanan::where('meja_id', $meja->id)->where('status', 'aktif')->exists();
        if ($hasSesiAktif) {
            return back()->with('error', 'Meja tidak bisa dihapus karena masih memiliki sesi aktif.');
        }
        $meja->delete();
        return back()->with('success', 'Meja berhasil dihapus!');
    });

    Route::get('/meja/{id}/qr', function ($id) {
        $meja = Meja::findOrFail($id);
        $url  = url("/pesan/{$meja->qr_token}");
        return view('admin.meja.qr', compact('meja', 'url'));
    });

    // Laporan Pendapatan
    Route::get('/pendapatan', function (Request $request) {
        $jenis = $request->get('jenis', 'harian'); // harian, bulanan, tahunan
        $tanggal = $request->get('tanggal', now()->toDateString());
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $tahun = $request->get('tahun', now()->year);

        $query = DetailPesanan::whereHas('pesanan', function ($q) {
            $q->whereHas('sesi', function ($q2) {
                $q2->where('status', 'selesai');
            });
        });

        $labelRentang = '';

        if ($jenis == 'harian') {
            $query->whereHas('pesanan.sesi', function ($q) use ($tanggal) {
                $q->whereDate('ditutup_pada', $tanggal);
            });
            $labelRentang = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
        } elseif ($jenis == 'bulanan') {
            $tahunBulan = explode('-', $bulan);
            if (count($tahunBulan) == 2) {
                $query->whereHas('pesanan.sesi', function ($q) use ($tahunBulan) {
                    $q->whereYear('ditutup_pada', $tahunBulan[0])
                      ->whereMonth('ditutup_pada', $tahunBulan[1]);
                });
                $labelRentang = \Carbon\Carbon::createFromDate($tahunBulan[0], $tahunBulan[1], 1)->translatedFormat('F Y');
            }
        } elseif ($jenis == 'tahunan') {
            $query->whereHas('pesanan.sesi', function ($q) use ($tahun) {
                $q->whereYear('ditutup_pada', $tahun);
            });
            $labelRentang = "Tahun " . $tahun;
        }

        $totalPendapatan = $query->sum('subtotal');

        // Menu Terjual (based on selected period)
        $queryMenu = DetailPesanan::whereHas('pesanan', function ($q) {
            $q->whereHas('sesi', function ($q2) {
                $q2->where('status', 'selesai');
            });
        });

        if ($jenis == 'harian') {
            $queryMenu->whereHas('pesanan.sesi', function ($q) use ($tanggal) {
                $q->whereDate('ditutup_pada', $tanggal);
            });
        } elseif ($jenis == 'bulanan') {
            $tahunBulan = explode('-', $bulan);
            if (count($tahunBulan) == 2) {
                $queryMenu->whereHas('pesanan.sesi', function ($q) use ($tahunBulan) {
                    $q->whereYear('ditutup_pada', $tahunBulan[0])
                      ->whereMonth('ditutup_pada', $tahunBulan[1]);
                });
            }
        } elseif ($jenis == 'tahunan') {
            $queryMenu->whereHas('pesanan.sesi', function ($q) use ($tahun) {
                $q->whereYear('ditutup_pada', $tahun);
            });
        }

        $menuTerjual = $queryMenu->selectRaw('menu_id, SUM(jumlah) as total_jumlah')
                                  ->with('menu')
                                  ->groupBy('menu_id')
                                  ->orderByRaw('SUM(jumlah) DESC')
                                  ->get();

        return view('admin.pendapatan.index', compact('totalPendapatan', 'jenis', 'tanggal', 'bulan', 'tahun', 'labelRentang', 'menuTerjual'));
    });
});


/*
|--------------------------------------------------------------------------
| KASIR ROUTES (role: kasir)
| Akses: Dashboard Pesanan, Kelola Pesanan, Manajemen Stok
|--------------------------------------------------------------------------
*/
Route::prefix('kasir')->middleware(['auth', 'role:kasir'])->group(function () {

    // API: Get Latest Order ID (For Audio Notification)
    Route::get('/api/latest-order', function () {
        return response()->json(['latest_id' => \App\Models\Pesanan::max('id') ?? 0]);
    });

    // Dashboard Kasir (Pesanan Masuk Real-time)
    Route::get('/dashboard', function () {
        $sesiAktif = SesiPemesanan::with(['meja', 'pemesan', 'pesanan'])
            ->where('status', 'aktif')->get();
        return view('kasir.dashboard', compact('sesiAktif'));
    });

    // Detail Sesi Meja
    Route::get('/sesi/{sesi_id}', function ($sesi_id) {
        $sesi = SesiPemesanan::with(['meja', 'pemesan.pesanan.detailPesanan.menu'])
            ->findOrFail($sesi_id);
        return view('kasir.sesi.detail', compact('sesi'));
    });

    // Update Status Pesanan (maju / mundur)
    Route::post('/pesanan/{pesanan_id}/status', function (Request $request, $pesanan_id) {
        $pesanan = Pesanan::findOrFail($pesanan_id);
        $statusOrder = ['menunggu', 'diproses', 'selesai', 'dibayar'];
        $currentIndex = array_search($pesanan->status, $statusOrder);
        if ($currentIndex === false) {
            return back();
        }

        $direction = $request->input('direction', 'next');
        if ($direction === 'prev' && $currentIndex > 0) {
            $pesanan->status = $statusOrder[$currentIndex - 1];
            $pesanan->save();
        } elseif ($direction === 'next' && $currentIndex < count($statusOrder) - 1) {
            $pesanan->status = $statusOrder[$currentIndex + 1];
            $pesanan->save();
        }

        return back();
    });

    // Tutup Sesi (kasir bisa tutup sesi)
    Route::post('/sesi/{sesi_id}/tutup', function ($sesi_id) {
        $sesi = SesiPemesanan::with('pesanan')->findOrFail($sesi_id);
        $allPaid = $sesi->pesanan->every(fn($p) => $p->status === 'dibayar');
        if (!$allPaid) {
            return back()->with('error', 'Semua pesanan harus berstatus "dibayar" sebelum menutup sesi.');
        }
        $sesi->update(['status' => 'selesai', 'ditutup_pada' => now()]);
        return redirect('/kasir/dashboard')->with('success', 'Sesi berhasil ditutup.');
    });

    // Tutup Sesi Paksa (kasir bisa tutup sesi paksa dengan menghapus pesanan yang belum dibayar)
    Route::post('/sesi/{sesi_id}/tutup-paksa', function ($sesi_id) {
        $sesi = SesiPemesanan::with('pesanan')->findOrFail($sesi_id);
        
        foreach ($sesi->pesanan as $pesanan) {
            if ($pesanan->status !== 'dibayar') {
                $pesanan->delete();
            }
        }
        
        $sesi->update(['status' => 'selesai', 'ditutup_pada' => now()]);
        return redirect('/kasir/dashboard')->with('success', 'Sesi berhasil ditutup paksa. Pesanan yang belum dibayar telah dibatalkan.');
    });

    // Manajemen Stok Kasir
    Route::get('/stok', function () {
        $menus = Menu::all();
        return view('kasir.stok', compact('menus'));
    });

    Route::post('/stok', function (Request $request) {
        $data = $request->validate([
            'stok'   => 'required|array',
            'stok.*' => 'required|integer|min:0',
        ]);

        foreach ($data['stok'] as $id => $stok) {
            Menu::where('id', $id)->update(['stok' => $stok]);
        }

        return back()->with('success', 'Stok menu berhasil disimpan!');
    });

    // Riwayat Pemesanan (Hari Ini - Reset 24 Jam)
    Route::get('/riwayat-pemesanan', function () {
        // Ambil data sesi yang selesai hari ini (status selesai)
        $riwayat = SesiPemesanan::with(['meja', 'pesanan.detailPesanan.menu', 'pemesan'])
            ->where('status', 'selesai')
            ->whereDate('ditutup_pada', today()) // Hanya hari ini
            ->orderBy('ditutup_pada', 'desc')
            ->get()
            ->map(function($sesi) {
                // Hitung total pembayaran per sesi
                $totalPembayaran = $sesi->pesanan->filter(fn($p) => $p->status === 'dibayar')->sum(function($p) {
                    return $p->detailPesanan->sum('subtotal');
                });
                $sesi->total_pembayaran = $totalPembayaran;
                return $sesi;
            });

        return view('kasir.riwayat-pemesanan', compact('riwayat'));
    });
});
