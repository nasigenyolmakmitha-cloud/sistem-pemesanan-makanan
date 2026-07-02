@extends('layouts.app')
@section('content')

<div class="card bg-white shadow-sm border-0 mb-4 rounded-4 overflow-hidden anim-fade-in">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-auto me-auto" style="min-width: 0;">
                <span class="text-uppercase small fw-bold text-muted letter-spacing-1 d-block mb-1">Selamat Datang</span>
                <h3 class="fw-extrabold text-dark mb-0 text-truncate" title="{{ session('nama_pemesan') }}">
                    {{ session('nama_pemesan') }} <span class="ms-1">👋</span>
                </h3>
            </div>
            
            <div class="col-12 col-md-auto">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if(isset($punyaPesanan) && $punyaPesanan)
                        <a href="/pesan/{{ $meja->qr_token }}/konfirmasi" class="btn btn-outline-info rounded-pill fw-bold border-2 bg-white d-flex align-items-center justify-content-center text-info flex-fill" style="font-size:0.85rem; height: 38px; padding: 0 16px; transition: all 0.2s;">
                            <i class="fad fa-receipt me-1"></i> Tagihan
                        </a>
                    @else
                        <form action="/pesan/{{ $meja->qr_token }}/batal-sesi" method="POST" class="m-0 flex-fill" id="formBatalSesi">
                            @csrf
                            <button type="button" onclick="confirmBatalSesi()" class="btn btn-outline-danger rounded-pill fw-bold border-2 bg-white d-flex align-items-center justify-content-center w-100" style="font-size:0.85rem; height: 38px; padding: 0 16px; transition: all 0.2s;">
                                <i class="fad fa-times-circle me-1"></i> Batal
                            </button>
                        </form>
                    @endif
                    <span class="badge bg-orange rounded-pill shadow-sm fw-bold d-flex align-items-center justify-content-center flex-fill" style="font-size:0.85rem; height: 38px; padding: 0 20px;">
                        <i class="fad fa-chair me-2"></i>{{ $meja->nomor_meja }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden anim-fade-in" style="animation-delay: 0.1s;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <i class="fad fa-users text-orange me-3" style="font-size: 2rem;"></i>
            <div>
                <h6 class="fw-bold m-0 text-dark">Pilih Pemesan</h6>
                <p class="text-muted small mb-0">Klik nama untuk mencatat pesanan ke orang tersebut</p>
            </div>
        </div>
        
            <div class="d-flex flex-nowrap gap-3 overflow-auto pb-3 pt-2 px-2 ms-n2 scrollbar-hidden" style="-webkit-overflow-scrolling: touch;">
            @foreach($semuaPemesan as $p)
            <div class="position-relative shrink-0">
                <form action="/pesan/{{ $meja->qr_token }}/ganti-orang" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="pemesan_id" value="{{ $p->id }}">
                    <button type="submit" class="btn {{ session('pemesan_id') == $p->id ? 'btn-primary shadow border-0' : 'btn-outline-secondary border-2 text-dark' }} rounded-4 px-4 py-3 fw-bold transition-all d-flex flex-column align-items-center justify-content-center hover-scale" style="width: 110px; height: 100px;">
                        @if(session('pemesan_id') == $p->id) 
                            <div class="position-absolute top-0 inset-s-0-0 mt-2 ms-2">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        @endif
                        <i class="fad fa-user-circle fs-2 mb-2 {{ session('pemesan_id') == $p->id ? 'text-white' : 'text-secondary opacity-50' }}"></i>
                        <span class="text-truncate w-100" style="max-width: 90px; font-size: 0.95rem;">{{ $p->nama }}</span>
                    </button>
                </form>
                
                @if(count($semuaPemesan) > 1)
                <form action="/pesan/{{ $meja->qr_token }}/hapus-orang" method="POST" class="position-absolute m-0" style="top: -5px; right: -5px; z-index:10;" id="formHapusOrang-{{ $p->id }}">
                    @csrf
                    <input type="hidden" name="pemesan_id" value="{{ $p->id }}">
                    <button type="button" onclick="confirmHapusOrang('{{ $p->id }}', '{{ $p->nama }}')" class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center shadow" style="width:26px !important; height:26px !important; min-width:26px !important; padding:0 !important; border:2px solid white; flex-shrink:0;">
                        <i class="fas fa-times" style="font-size:0.75rem; line-height:1;"></i>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
            
            <button class="btn btn-light border-2 text-orange rounded-4 px-4 py-3 fw-bold d-flex flex-column align-items-center justify-content-center transition-all hover-scale bg-white shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahOrang" style="width: 110px; height: 100px; border-style: dashed !important; border-color: var(--accent-orange) !important;">
                <i class="fad fa-plus-circle fs-2 mb-2 opacity-75"></i>
                <span class="text-truncate w-100" style="max-width: 90px; font-size: 0.95rem;">Tambah</span>
            </button>
        </div>
    </div>
</div>

@php
    $allKategori = $menus->pluck('kategori')->unique()->toArray();
    $kategoriOrder = ['Makanan', 'Tambahan', 'Minuman'];
    $kategoriList = array_unique(array_merge(array_intersect($kategoriOrder, $allKategori), $allKategori));
@endphp

<div class="mb-4">
    <div class="d-flex align-items-center mb-3">
        <i class="fad fa-utensils text-orange me-2 fs-5"></i>
        <h6 class="fw-bold m-0 text-dark">Kategori Menu</h6>
    </div>
    
    <div class="position-relative">
        <select id="kategoriFilter" class="w-100 border-0 shadow-sm fw-bold text-dark" style="background-color: #fff; height: 48px; border-radius: 14px; padding-left: 20px; padding-right: 40px; appearance: none; -webkit-appearance: none; -moz-appearance: none; cursor: pointer; font-size: 0.85rem; outline: none;">
            <option value="all">Semua Kategori</option>
            @foreach($kategoriList as $kat)
            <option value="{{ $kat }}">{{ $kat }}</option>
            @endforeach
        </select>
        <div class="position-absolute top-50 inset-e-0 translate-middle-y pe-4" style="pointer-events: none;">
            <i class="fas fa-chevron-down text-orange" style="font-size: 0.9rem;"></i>
        </div>
    </div>
</div>

@php 
    $groupedMenus = $menus->groupBy('kategori');
    $sortedGroups = [];
    
    foreach($kategoriOrder as $kat) {
        if($groupedMenus->has($kat)) {
            $sortedGroups[$kat] = $groupedMenus[$kat];
        }
    }
    foreach($groupedMenus as $kat => $items) {
        if(!in_array($kat, $kategoriOrder)) {
            $sortedGroups[$kat] = $items;
        }
    }
@endphp

<div id="menu-container">
    @foreach($sortedGroups as $kategori => $items)
    <div class="menu-category-section" data-kategori="{{ $kategori }}">
        <div class="d-flex align-items-center mb-4 mt-2 position-relative">
            <h4 class="fw-extrabold text-dark m-0 d-flex align-items-center pe-3 bg-white" style="z-index: 1;">
                @if($kategori == 'Makanan')
                    <i class="fad fa-burger-soda text-orange me-2 fs-3"></i>
                @elseif($kategori == 'Minuman')
                    <i class="fad fa-glass-citrus text-info me-2 fs-3"></i>
                @elseif($kategori == 'Tambahan')
                    <i class="fad fa-layer-plus text-success me-2 fs-3"></i>
                @else
                    <i class="fad fa-utensils text-secondary me-2 fs-3"></i>
                @endif
                {{ $kategori }}
            </h4>
            <div style="flex-grow: 1; height: 4px; background: repeating-linear-gradient(90deg, rgba(255, 112, 67, 0.4), rgba(255, 112, 67, 0.4) 6px, transparent 6px, transparent 12px); border-radius: 2px;"></div>
        </div>

        <div class="row g-4 mb-5">
            @foreach($items as $m)
            <div class="col-md-6 col-lg-4 menu-item" data-kategori="{{ $m->kategori }}">
                <div class="card h-100 border-0 shadow-sm overflow-hidden group">
                    <div class="position-relative overflow-hidden" style="height: 200px;">
                        @if($m->foto)
                            <img src="{{ asset('storage/'.$m->foto) }}" class="img-fluid h-100 w-100 transition-all group-hover-scale" style="object-fit: cover;">
                        @elseif($m->kategori == 'Minuman')
                            <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-light">
                                <i class="fad fa-glass-citrus text-info display-4 opacity-50"></i>
                            </div>
                        @elseif($m->kategori == 'Tambahan')
                            <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-light">
                                <i class="fad fa-layer-plus text-success display-4 opacity-50"></i>
                            </div>
                        @else
                            <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-light">
                                <i class="fad fa-burger-soda text-orange display-4 opacity-50"></i>
                            </div>
                        @endif
                        
                        <div id="habis-overlay-{{ $m->id }}" class="position-absolute top-0 inset-s-0 w-100 h-100 bg-dark bg-opacity-50 align-items-center justify-content-center {{ $m->stok <= 0 ? 'd-flex' : 'd-none' }}" style="z-index: 2;">
                            <span class="badge bg-white text-danger rounded-pill px-4 py-2 fw-extrabold shadow"><i class="fal fa-clock me-2"></i>HABIS</span>
                        </div>

                        <div class="position-absolute bottom-0 inset-s-0 p-3 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); z-index: 1;">
                            <span class="badge glass text-white rounded-pill px-3 py-1 small fw-bold">{{ $m->kategori }}</span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-0">{{ $m->nama }}</h5>
                            <span id="stok-badge-{{ $m->id }}" class="badge {{ $m->stok <= 0 ? 'bg-danger text-white' : 'bg-light text-dark' }} rounded-pill py-1 px-2 small ms-2" data-stok="{{ $m->stok }}" data-max-stok="{{ $m->stok }}">
                                Stok: {{ $m->stok }}
                            </span>
                        </div>
                        <p class="text-muted small mb-3 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">{{ $m->deskripsi }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block small text-muted">Harga</span>
                                <span class="fw-extrabold text-orange fs-5">Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
                            </div>
                            
                            <div style="z-index: 3; position: relative;" id="menu-action-{{ $m->id }}">
                                <div id="in-cart-{{ $m->id }}" class="align-items-center bg-light rounded-pill p-1 shadow-sm border shrink-0 d-none" style="min-width: 105px;">
                                    <button onclick="removeFromCart('{{ $m->id }}')" class="btn btn-sm btn-danger rounded-circle p-0 transition-all border-0 shadow-sm d-flex align-items-center justify-content-center shrink-0" style="width: 34px; height: 34px;"><i class="fas fa-minus small"></i></button>
                                    <span id="qty-{{ $m->id }}" class="fw-extrabold text-dark px-3 shrink-0 d-flex align-items-center justify-content-center" style="min-width: 30px;">0</span>
                                    <button onclick="addToCart('{{ $m->id }}', '{{ addslashes($m->nama) }}', {{ $m->harga }})" id="btn-plus-{{ $m->id }}" class="btn btn-sm {{ $m->stok <= 0 ? 'btn-secondary' : 'btn-primary' }} rounded-circle p-0 transition-all border-0 shadow-sm d-flex align-items-center justify-content-center shrink-0" style="width: 34px; height: 34px;" {{ $m->stok <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus small"></i>
                                    </button>
                                </div>
                                <div id="not-in-cart-{{ $m->id }}">
                                    <button onclick="addToCart('{{ $m->id }}', '{{ addslashes($m->nama) }}', {{ $m->harga }})" id="btn-tambah-{{ $m->id }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm align-items-center fw-bold transition-all border-0 {{ $m->stok <= 0 ? 'd-none' : 'd-flex' }}">
                                        <i class="fas fa-plus me-2"></i> Tambah
                                    </button>
                                    <button id="btn-habis-{{ $m->id }}" class="btn btn-secondary rounded-pill px-4 py-2 shadow-sm align-items-center fw-bold border-0 {{ $m->stok > 0 ? 'd-none' : 'd-flex' }}" disabled>
                                        Habis
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<div class="fixed-bottom p-4 d-md-none" style="z-index: 1050;">
    <a href="/pesan/{{ $meja->qr_token }}/keranjang" class="btn btn-primary w-100 py-3 rounded-4 shadow-lg d-flex justify-content-between align-items-center px-4 hover-scale transition-all">
        <div class="d-flex align-items-center">
            <i class="fad fa-shopping-basket fs-4 me-3"></i>
            <span class="fw-bold">Lihat Keranjang</span>
        </div>
        <span class="badge bg-white text-orange rounded-pill px-3 py-2 fs-6 fw-extrabold shadow-sm cart-count-badge">
            0
        </span>
    </a>
</div>

<div class="d-none d-md-block position-fixed" style="bottom: 40px; right: 40px; z-index: 1050;">
    <a href="/pesan/{{ $meja->qr_token }}/keranjang" class="btn btn-primary rounded-pill shadow-lg d-flex align-items-center p-0 overflow-hidden hover-scale transition-all" style="height: 65px; border: 2px solid rgba(255,255,255,0.2);">
        <div class="bg-white bg-opacity-25 h-100 d-flex align-items-center justify-content-center px-4">
            <i class="fad fa-shopping-basket fs-3"></i>
        </div>
        <div class="px-4 fw-extrabold fs-5 letter-spacing-1">
            KERANJANG
        </div>
        <div class="bg-white text-orange rounded-circle d-flex align-items-center justify-content-center me-2 fw-black shadow-sm cart-count-badge" style="width: 45px; height: 45px; font-size: 1.2rem;">
            0
        </div>
    </a>
</div>

@section('modals')
<div class="modal fade" id="modalTambahOrang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="fw-bold text-dark m-0"><i class="fad fa-user-plus text-orange me-2"></i> Tambah Orang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/pesan/{{ $meja->qr_token }}/tambah-orang" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-4">Tambahkan nama agar pesanan bisa dikelompokkan dengan rapi.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Panggilan</label>
                        <input type="text" name="nama" class="form-control form-control-lg border-2" placeholder="Contoh: Budi" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
.modal-backdrop { background-color: rgba(0, 0, 0, 0.2) !important; }
.modal-backdrop.show { opacity: 1 !important; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
.scrollbar-hidden::-webkit-scrollbar { display: none; }
.scrollbar-hidden { -ms-overflow-style: none; scrollbar-width: none; }
.btn-white { background: white; color: #4A4A4A; }
.transition-all { transition: all 0.3s ease; }
.group-hover-scale { transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.group:hover .group-hover-scale { transform: scale(1.1); }
.hover-scale { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
.hover-scale:hover { transform: scale(1.05); }
.fw-extrabold { font-weight: 800; }
.fw-black { font-weight: 900; }
.letter-spacing-1 { letter-spacing: 1px; }

/* Custom style untuk menghilangkan panah bawaan browser pada dropdown */
select#kategoriFilter::-ms-expand {
    display: none;
}

/* Mengecilkan ukuran huruf di dalam daftar pilihan yang terbuka */
#kategoriFilter option {
    font-size: 0.85rem;
    font-weight: 500;
    color: #333;
}

</style>

<script>
const currentQrToken = '{{ $meja->qr_token }}';
const currentPemesanId = '{{ session("pemesan_id") }}';
const cartKey = `cart_${currentQrToken}_${currentPemesanId}`;
let cart = JSON.parse(localStorage.getItem(cartKey)) || {};

function saveCart() {
    localStorage.setItem(cartKey, JSON.stringify(cart));
    updateGlobalCartCount();
}

function updateGlobalCartCount() {
    let totalItems = Object.keys(cart).length;
    document.querySelectorAll('.cart-count-badge').forEach(badge => {
        badge.innerText = totalItems;
    });
}

function getTotalQtyInTable(menuId) {
    let total = 0;
    for (let i = 0; i < localStorage.length; i++) {
        let key = localStorage.key(i);
        if (key && key.startsWith(`cart_${currentQrToken}_`)) {
            let tempCart = JSON.parse(localStorage.getItem(key)) || {};
            if (tempCart[menuId]) {
                total += tempCart[menuId].jumlah;
            }
        }
    }
    return total;
}

function updateStockUI(menuId) {
    let stokBadge = document.getElementById(`stok-badge-${menuId}`);
    if (!stokBadge) return;

    let maxStok = parseInt(stokBadge.getAttribute('data-max-stok'));
    let totalDiMeja = getTotalQtyInTable(menuId);
    let newStok = maxStok - totalDiMeja;

    stokBadge.setAttribute('data-stok', newStok);
    stokBadge.innerText = 'Stok: ' + newStok;

    let habisOverlay = document.getElementById(`habis-overlay-${menuId}`);
    let btnTambah = document.getElementById(`btn-tambah-${menuId}`);
    let btnHabis = document.getElementById(`btn-habis-${menuId}`);
    let btnPlus = document.getElementById(`btn-plus-${menuId}`);

    if (newStok <= 0) {
        stokBadge.classList.remove('bg-light', 'text-dark');
        stokBadge.classList.add('bg-danger', 'text-white');

        if (habisOverlay) { habisOverlay.classList.remove('d-none'); habisOverlay.classList.add('d-flex'); }
        if (btnTambah) { btnTambah.classList.remove('d-flex'); btnTambah.classList.add('d-none'); }
        if (btnHabis) { btnHabis.classList.remove('d-none'); btnHabis.classList.add('d-flex'); }
        if (btnPlus) {
            btnPlus.classList.remove('btn-primary');
            btnPlus.classList.add('btn-secondary');
            btnPlus.disabled = true;
        }
    } else {
        stokBadge.classList.remove('bg-danger', 'text-white');
        stokBadge.classList.add('bg-light', 'text-dark');

        if (habisOverlay) { habisOverlay.classList.remove('d-flex'); habisOverlay.classList.add('d-none'); }
        if (btnTambah) { btnTambah.classList.remove('d-none'); btnTambah.classList.add('d-flex'); }
        if (btnHabis) { btnHabis.classList.remove('d-flex'); btnHabis.classList.add('d-none'); }
        if (btnPlus) {
            btnPlus.classList.remove('btn-secondary');
            btnPlus.classList.add('btn-primary');
            btnPlus.disabled = false;
        }
    }
}

function hydrateUI() {
    let tableStocks = {}; 

    document.querySelectorAll('[id^="stok-badge-"]').forEach(badge => {
        let menuId = badge.id.replace('stok-badge-', '');
        let maxStok = parseInt(badge.getAttribute('data-max-stok'));
        
        tableStocks[menuId] = maxStok;
        updateStockUI(menuId);

        let currentUserQty = cart[menuId] ? cart[menuId].jumlah : 0;
        let qtySpan = document.getElementById(`qty-${menuId}`);
        if (qtySpan) qtySpan.innerText = currentUserQty;

        let inCart = document.getElementById(`in-cart-${menuId}`);
        let notInCart = document.getElementById(`not-in-cart-${menuId}`);

        if (currentUserQty > 0) {
            if (inCart) { inCart.classList.remove('d-none'); inCart.classList.add('d-flex'); }
            if (notInCart) notInCart.classList.add('d-none');
        } else {
            if (inCart) { inCart.classList.remove('d-flex'); inCart.classList.add('d-none'); }
            if (notInCart) notInCart.classList.remove('d-none');
        }
    });

    localStorage.setItem(`stocks_${currentQrToken}`, JSON.stringify(tableStocks));
    updateGlobalCartCount();
}

document.addEventListener('DOMContentLoaded', function() {
    hydrateUI();
});

function addToCart(menuId, nama, harga) {
    let stokBadge = document.getElementById(`stok-badge-${menuId}`);
    let currentStok = stokBadge ? parseInt(stokBadge.getAttribute('data-stok')) : 1;
    let maxStok = stokBadge ? parseInt(stokBadge.getAttribute('data-max-stok')) : 0;

    if (currentStok <= 0) {
        Swal.fire('Peringatan', 'Stok tidak mencukupi!', 'warning');
        return;
    }

    if (!cart[menuId]) {
        cart[menuId] = { id: menuId, nama: nama, harga: harga, jumlah: 1, maxStok: maxStok };
    } else {
        cart[menuId].jumlah += 1;
    }

    saveCart();

    let qtySpan = document.getElementById(`qty-${menuId}`);
    if (qtySpan) qtySpan.innerText = cart[menuId].jumlah;

    let inCart = document.getElementById(`in-cart-${menuId}`);
    if (inCart) { inCart.classList.remove('d-none'); inCart.classList.add('d-flex'); }
    let notInCart = document.getElementById(`not-in-cart-${menuId}`);
    if (notInCart) notInCart.classList.add('d-none');

    updateStockUI(menuId);
}

function removeFromCart(menuId) {
    if (!cart[menuId]) return;

    cart[menuId].jumlah -= 1;
    let isRemoved = false;

    if (cart[menuId].jumlah <= 0) {
        delete cart[menuId];
        isRemoved = true;
    }
    saveCart();

    if (isRemoved) {
        let inCart = document.getElementById(`in-cart-${menuId}`);
        if (inCart) { inCart.classList.remove('d-flex'); inCart.classList.add('d-none'); }
        let notInCart = document.getElementById(`not-in-cart-${menuId}`);
        if (notInCart) notInCart.classList.remove('d-none');

        let qtySpan = document.getElementById(`qty-${menuId}`);
        if (qtySpan) qtySpan.innerText = 0;
    } else {
        let qtySpan = document.getElementById(`qty-${menuId}`);
        if (qtySpan) qtySpan.innerText = cart[menuId].jumlah;
    }

    updateStockUI(menuId);
}

function confirmBatalSesi() {
    Swal.fire({
        title: 'Batalkan Sesi?',
        text: "Semua pesanan yang belum dibayar di meja ini akan dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formBatalSesi').submit();
        }
    });
}

function confirmHapusOrang(id, nama) {
    Swal.fire({
        title: 'Hapus ' + nama + "?",
        text: "Anda akan menghapus " + nama + " dari sesi ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formHapusOrang-' + id).submit();
        }
    });
}

// ==========================================
// REVISI LOGIKA FILTER (MENGGUNAKAN DROPDOWN)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelect = document.getElementById('kategoriFilter');
    
    if (kategoriSelect) {
        kategoriSelect.addEventListener('change', function() {
            const filter = this.value; // Membaca kategori yang dipilih dari dropdown
            
            document.querySelectorAll('.menu-category-section').forEach(section => {
                if (filter === 'all' || section.dataset.kategori === filter) {
                    section.style.display = '';
                    section.classList.add('anim-fade-in');
                } else {
                    section.style.display = 'none';
                }
            });
        });
    }
});
</script>

@endsection

@section('scripts')
<script>
@if(session('success'))
    Swal.fire({
        title: 'Berhasil',
        text: '{{ session("success") }}',
        icon: 'success',
        confirmButtonColor: '#FF7043',
        confirmButtonText: 'OK'
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Peringatan',
        text: '{{ session("error") }}',
        icon: 'warning',
        confirmButtonColor: '#FF7043',
        confirmButtonText: 'OK'
    });
@endif

@if(session('stok_error'))
    Swal.fire({
        title: 'Stok tidak mencukupi',
        html: `{!! session('stok_error') !!}`,
        icon: 'error',
        confirmButtonColor: '#FF7043',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endsection