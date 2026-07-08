@extends('layouts.admin')
@section('page-title', 'Manajemen Menu')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
            <ion-icon name="restaurant-outline" style="color:var(--orange); margin-right:6px; vertical-align:middle;"></ion-icon>
            Daftar Menu
        </h2>
        <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Kelola semua item menu restoran</p>
    </div>
    <a href="/admin/menu/create" style="text-decoration:none;">
        <ion-button color="primary" style="--border-radius:12px; font-weight:700;">
            <ion-icon name="add-outline" slot="start"></ion-icon>
            Tambah Menu
        </ion-button>
    </a>
</div>

@if(session('success'))
<ion-card color="success" style="margin-bottom:1rem; --background:rgba(76,175,80,0.1); --color:#388E3C;">
    <ion-card-content style="padding:1rem; display:flex; align-items:center; gap:8px; font-weight:600; font-size:0.85rem;">
        <ion-icon name="checkmark-circle" style="font-size:1.2rem;"></ion-icon>
        {{ session('success') }}
    </ion-card-content>
</ion-card>
@endif

<div style="margin-bottom: 1.5rem;">
    <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        
        <div style="flex:1; min-width:200px;">
            <input type="text" id="adminSearchInput" placeholder="Cari nama menu..."
                style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; transition:border 0.2s; box-sizing:border-box;"
                onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">
        </div>

        <div style="min-width:180px;">
            <select id="adminKategoriFilter" style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; background:#fff; box-sizing:border-box; transition:border 0.2s; cursor:pointer;"
                onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">
                <option value="">Semua Kategori</option>
                <option value="makanan">Makanan</option>
                <option value="minuman">Minuman</option>
                <option value="tambahan">Tambahan</option>
            </select>
        </div>

    </div>
</div>

<ion-card style="margin:0;">
    <ion-card-content style="padding:0;">
        <div class="table-responsive">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#FAFAFA;">
                        <th style="padding:14px 16px; text-align:left; font-size:0.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:0.5px;">Item Menu</th>
                        <th style="padding:14px 16px; text-align:left; font-size:0.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:0.5px;">Kategori</th>
                        <th style="padding:14px 16px; text-align:left; font-size:0.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:0.5px;">Harga</th>
                        <th style="padding:14px 16px; text-align:left; font-size:0.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:0.5px;">Status</th>
                        <th style="padding:14px 16px; text-align:right; font-size:0.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:0.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="adminEmptyMsg" style="display:none;">
                        <td colspan="5" style="text-align:center; padding:3rem; color:#999;">
                            <ion-icon name="search-outline" style="font-size:3rem; opacity:0.3; display:block; margin:0 auto 1rem;"></ion-icon>
                            Menu yang dicari tidak ditemukan.
                        </td>
                    </tr>

                    @forelse($menus as $m)
                    <tr class="admin-menu-row" data-nama="{{ strtolower($m->nama) }}" data-kategori="{{ strtolower($m->kategori) }}" style="border-bottom:1px solid #f5f5f5; transition:background 0.2s;">
                        <td style="padding:14px 16px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:48px; height:48px; border-radius:12px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                                   @if($m->foto)
                                        <img src="{{ asset('storage/'.$m->foto) }}" style="width:48px; height:48px; object-fit:cover; border-radius:12px;">
                                    @else
                                        <i class="fad {{ $m->kategori == 'Minuman' ? 'fa-glass-citrus' : ($m->kategori == 'Tambahan' ? 'fa-layer-plus' : 'fa-utensils') }}" style="color:var(--orange); font-size:1.1rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:700; color:#2D3436; font-size:0.9rem;">{{ $m->nama }}</div>
                                    <div style="color:#999; font-size:0.75rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $m->deskripsi }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="background:var(--orange-light); color:var(--orange); padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700;">{{ $m->kategori }}</span>
                        </td>
                        <td style="padding:14px 16px; font-weight:800; color:var(--orange); font-size:0.9rem; white-space:nowrap;">
                            Rp {{ number_format($m->harga, 0, ',', '.') }}
                        </td>
                        <td style="padding:14px 16px;">
                            @if($m->stok > 0)
                                <span style="background:#E8F5E9; color:#388E3C; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                                    Stok {{ $m->stok }}
                                </span>
                            @else
                                <span style="background:#FFEBEE; color:#D32F2F; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                                    <i class="fas fa-times" style="margin-right:3px;"></i> Habis
                                </span>
                            @endif
                        </td>
                        <td style="padding:14px 16px; text-align:right;">
                            <div style="display:flex; justify-content:flex-end; gap:6px;">
                                <a href="/admin/menu/{{ $m->id }}/edit">
                                    <ion-button size="small" fill="outline" color="medium" style="--border-radius:10px; font-weight:700; font-size:0.7rem;">
                                        <ion-icon name="create-outline" slot="start"></ion-icon>
                                        Edit
                                    </ion-button>
                                </a>
                                <form action="/admin/menu/{{ $m->id }}" method="POST" id="deleteMenuForm-{{ $m->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDeleteMenu({{ $m->id }})" style="background:transparent; border:1px solid #F44336; color:#F44336; border-radius:10px; padding:6px 12px; font-weight:700; font-size:0.7rem; cursor:pointer; display:flex; align-items:center; gap:4px; transition:all 0.2s;">
                                        <ion-icon name="trash-outline"></ion-icon> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:3rem; color:#999;">
                            <ion-icon name="restaurant-outline" style="font-size:3rem; opacity:0.3; display:block; margin:0 auto 1rem;"></ion-icon>
                            Tidak ada data menu di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </ion-card-content>
</ion-card>

@endsection

@section('scripts')
<script>
// Fungsi Hapus Menu
function confirmDeleteMenu(id) {
    Swal.fire({
        title: 'Hapus Menu?',
        text: "Menu yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteMenuForm-' + id).submit();
        }
    });
}

// LOGIKA PENCARIAN & FILTER ADMIN REAL-TIME
const adminSearch = document.getElementById('adminSearchInput');
const adminFilter = document.getElementById('adminKategoriFilter');
const adminRows = document.querySelectorAll('.admin-menu-row');
const adminEmpty = document.getElementById('adminEmptyMsg');

function filterAdminMenu() {
    const query = adminSearch ? adminSearch.value.toLowerCase().trim() : '';
    const kategori = adminFilter ? adminFilter.value.toLowerCase() : '';
    let visibleCount = 0;

    adminRows.forEach(row => {
        // Mengambil data dari baris tabel
        const nama = row.getAttribute('data-nama');
        const rowKat = row.getAttribute('data-kategori');

        // Pengecekan kecocokan
        const matchNama = nama.includes(query);
        const matchKat = (kategori === "" || rowKat === kategori);

        if (matchNama && matchKat) {
            row.style.display = ''; // Munculkan baris
            visibleCount++;
        } else {
            row.style.display = 'none'; // Sembunyikan baris
        }
    });

    // Menampilkan pesan jika tidak ada baris yang sesuai kriteria pencarian
    if (visibleCount === 0 && adminRows.length > 0) {
        if(adminEmpty) adminEmpty.style.display = '';
    } else {
        if(adminEmpty) adminEmpty.style.display = 'none';
    }
}

// Memicu filter setiap kali mengetik atau mengubah opsi
adminSearch?.addEventListener('input', filterAdminMenu);
adminFilter?.addEventListener('change', filterAdminMenu);

</script>
@endsection