@extends('layouts.kasir')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:1rem;" class="anim-fade-in">
    <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
            <i class="fad fa-boxes-stacked text-orange me-2"></i> Ketersediaan Stok Menu
        </h2>
        <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Atur jumlah stok menu, lalu klik Simpan untuk menyimpan perubahan.</p>
    </div>
</div>

<div class="anim-fade-in" style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; flex-wrap: wrap; background: #fff; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 5px 15px; border: 1px solid rgba(0,0,0,0.05); gap: 10px;">
        <div style="display: flex; align-items: center; flex: 1; min-width: 200px;">
            <i class="fad fa-search" style="color: #FF7043; font-size: 1.2rem; margin-right: 12px;"></i>
            <input type="text" id="searchInput" placeholder="Cari nama menu..." style="border: none; outline: none; width: 100%; padding: 12px 0; font-size: 0.95rem; font-weight: 600; color: #333; background: transparent;">
        </div>
        
        <div style="width: 1px; height: 30px; background: #eee; display: inline-block;"></div>

        <div style="min-width: 160px; display: flex; align-items: center;">
            <i class="fad fa-filter" style="color: #999; margin-right: 8px;"></i>
            <select id="kategoriFilter" style="border: none; outline: none; width: 100%; padding: 12px 0; font-size: 0.95rem; font-weight: 600; color: #333; background: transparent; cursor: pointer;">
                <option value="">Semua Kategori</option>
                <option value="makanan">Makanan</option>
                <option value="tambahan">Tambahan</option>
                <option value="minuman">Minuman</option>
            </select>
        </div>
    </div>
</div>

@if(session('success'))
<ion-card color="success" class="anim-fade-in" style="margin:0 0 1.5rem 0; --background:rgba(76,175,80,0.1); --color:#388E3C; border-radius:14px; box-shadow:none; border:1px solid rgba(76,175,80,0.3);">
    <ion-card-content style="padding:1rem 1.2rem; display:flex; align-items:center; gap:10px; font-weight:700; font-size:0.9rem;">
        <i class="fad fa-check-circle" style="font-size:1.4rem;"></i>
        {{ session('success') }}
    </ion-card-content>
</ion-card>
@endif

<form action="/kasir/stok" method="POST" id="form-stok" style="display:flex; flex-direction:column;">
    @csrf
    <ion-card style="margin:0; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border:none; background:#ffffff; overflow: hidden; flex:1;">
        <ion-list style="background:transparent; padding:0; padding-bottom: 160px;" class="anim-fade-in">
            @forelse($menus as $m)
            <ion-item class="menu-item-row" data-nama="{{ strtolower($m->nama) }}" data-kategori="{{ strtolower($m->kategori) }}" lines="full" style="--background:transparent; --padding-start:20px; --padding-end:20px; --min-height:90px; --border-color: rgba(0,0,0,0.05);">
                <ion-thumbnail slot="start" style="--border-radius:14px; width:65px; height:65px; background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
                    @if($m->foto)
                        <img src="{{ asset('storage/'.$m->foto) }}" style="width:100%; height:100%; object-fit:cover; border-radius:14px;" />
                    @else
                        <i class="fad {{ $m->kategori == 'Minuman' ? 'fa-glass-citrus' : 'fa-utensils' }}" style="color:var(--orange); font-size:1.6rem;"></i>
                    @endif
                </ion-thumbnail>

                <ion-label style="margin-left: 10px;">
                    <h3 style="font-weight:800; color:#1A1A2E; font-size:1.1rem; margin-bottom:4px;">{{ $m->nama }}</h3>
                    <span style="background: rgba(255, 126, 33, 0.1); color:var(--orange); font-size:0.7rem; font-weight:800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $m->kategori }}</span>
                </ion-label>

                <div slot="end" style="display:flex; align-items:center; gap:16px;">
                    <div style="min-width: 85px; text-align: center;" id="badge-{{ $m->id }}">
                        @if($m->stok > 0)
                            <ion-badge color="success" style="font-weight:700; padding:6px 12px; border-radius:10px; font-size: 0.7rem; letter-spacing: 0.3px;">
                                Stok {{ $m->stok }}
                            </ion-badge>
                        @else
                            <ion-badge color="danger" style="font-weight:700; padding:6px 12px; border-radius:10px; font-size: 0.7rem; letter-spacing: 0.3px;">
                                HABIS
                            </ion-badge>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:8px;">
                        <ion-button type="button" size="small" color="medium" fill="outline"
                            onclick="adjustStok({{ $m->id }}, -1)"
                            style="--border-radius:12px; font-weight:800; font-size:0.75rem; margin:0; height:38px; --box-shadow:none; min-width: 90px;">
                            -
                        </ion-button>

                        <input type="text"
                             name="stok[{{ $m->id }}]"
                                id="stok-{{ $m->id }}"
                                value="{{ $m->stok }}"
                                inputmode="numeric"
                                oninput="this.value=this.value.replace(/[^0-9]/g,''); updateBadge({{ $m->id }})"
                                style="width: 45px; height: 38px; padding: 0; border: 1px solid #ddd; border-radius: 12px; font-size: 0.9rem; font-weight: 600; text-align: center; outline: none; background: #fafafa;">

                        <ion-button type="button" size="small" color="primary" fill="solid"
                            onclick="adjustStok({{ $m->id }}, 1)"
                            style="--border-radius:12px; font-weight:800; font-size:0.75rem; margin:0; height:38px; --box-shadow:none; min-width: 90px;">
                            +
                        </ion-button>
                    </div>
                </div>
            </ion-item>
            @empty
            <div style="text-align:center; padding:4rem 2rem; color:#999;">
                <ion-icon name="restaurant-outline" style="font-size:4rem; opacity:0.2; display:block; margin:0 auto 1.5rem; color:var(--orange);"></ion-icon>
                <h3 style="font-weight:700; color:#1A1A2E; margin-bottom: 8px;">Belum ada data menu</h3>
                <p style="font-size:0.9rem; opacity: 0.7;">Menu yang ditambahkan oleh admin akan muncul di sini.</p>
            </div>
            @endforelse
        </ion-list>
    </ion-card>

    @if($menus->count() > 0)
    <div style="position: fixed; bottom: 90px; right: 20px; z-index: 9999;">
        <button type="submit" class="btn-simpan-stok">
            <i class="fad fa-save" style="margin-right: 8px; font-size: 1.1rem;"></i> Simpan Stok
        </button>
    </div>
    @endif
</form>

<style>
    /* Styling Tombol Melayang */
    .btn-simpan-stok {
        background: linear-gradient(135deg, #FF7043 0%, #F4511E 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 14px 28px;
        font-weight: 800;
        font-size: 1rem;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(244, 81, 30, 0.4);
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-simpan-stok:hover {
        box-shadow: 0 10px 30px rgba(244, 81, 30, 0.6);
        transform: translateY(-3px);
    }

    .btn-simpan-stok:active {
        transform: scale(0.96) translateY(0);
    }

    /* Style untuk pesan jika hasil pencarian kosong */
    #emptySearchMsg {
        text-align: center;
        padding: 3rem 1rem;
        color: #999;
        display: none;
    }
</style>

<div id="emptySearchMsg">
    <i class="fad fa-search-minus" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
    <h4 style="font-weight: 700; color: #555;">Menu tidak ditemukan</h4>
    <p style="font-size: 0.9rem;">Coba gunakan kata kunci nama menu yang lain.</p>
</div>

@endsection

@section('scripts')
<script>
    // LOGIKA PENCARIAN & FILTER KATEGORI SECARA REAL-TIME
    const searchInput = document.getElementById('searchInput');
    const kategoriFilter = document.getElementById('kategoriFilter');
    const menuItems = document.querySelectorAll('.menu-item-row');
    const emptyMsg = document.getElementById('emptySearchMsg');

    function filterMenu() {
        const query = searchInput.value.toLowerCase().trim();
        const kategori = kategoriFilter.value.toLowerCase();
        let visibleCount = 0;

        menuItems.forEach(item => {
            const nama = item.getAttribute('data-nama');
            const itemKategori = item.getAttribute('data-kategori');

            // Cek apakah nama cocok DAN kategori cocok (jika kategori tidak kosong)
            const matchNama = nama.includes(query);
            const matchKategori = (kategori === "" || itemKategori === kategori);

            if (matchNama && matchKategori) {
                item.style.display = ''; // Tampilkan
                visibleCount++;
            } else {
                item.style.display = 'none'; // Sembunyikan
            }
        });

        // Tampilkan pesan kosong jika tidak ada yang cocok
        if (visibleCount === 0 && menuItems.length > 0) {
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
        }
    }

    // Jalankan fungsi filter ketika diketik atau saat dropdown diubah
    searchInput?.addEventListener('input', filterMenu);
    kategoriFilter?.addEventListener('change', filterMenu);

    // LOGIKA STOK
    function updateBadge(menuId) {
        const input = document.getElementById('stok-' + menuId);
        const badge = document.getElementById('badge-' + menuId);
        const val = Math.max(0, parseInt(input.value) || 0);
        input.value = val;

        if (val > 0) {
            badge.innerHTML = `<ion-badge color="success" style="font-weight:700; padding:6px 12px; border-radius:10px; font-size: 0.7rem; letter-spacing: 0.3px;">Stok ${val}</ion-badge>`;
        } else {
            badge.innerHTML = `<ion-badge color="danger" style="font-weight:700; padding:6px 12px; border-radius:10px; font-size: 0.7rem; letter-spacing: 0.3px;">HABIS</ion-badge>`;
        }
    }

    function adjustStok(menuId, delta) {
        const input = document.getElementById('stok-' + menuId);
        const current = parseInt(input.value) || 0;
        input.value = Math.max(0, current + delta);
        updateBadge(menuId);
    }
    
    function validateNumber(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value === '' || parseInt(input.value) < 0) {
            input.value = 0;
        }
    }
</script>
@endsection