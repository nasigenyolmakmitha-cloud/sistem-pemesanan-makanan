@extends('layouts.app')
@section('content')
<div class="row justify-content-center mt-2">
    <div class="col-md-10 col-lg-8">
        <!-- Back Button & Header -->
        <div class="d-flex align-items-center mb-4 anim-fade-in">
            <a href="/pesan/{{ $meja->qr_token }}/menu" class="btn btn-white rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height:50px; border: 1px solid rgba(0,0,0,0.05);">
                <i class="fas fa-chevron-left text-orange fs-4"></i>
            </a>
            <div>
                <h3 class="fw-extrabold text-dark mb-0">Keranjang</h3>
                <span class="text-muted small fw-bold text-uppercase letter-spacing-1">{{ $meja->nomor_meja }}</span>
            </div>
        </div>

<div id="cart-content" style="display: none;">
    <form action="/pesan/{{ $meja->qr_token }}/pesan" method="POST" id="form-pesan" class="anim-fade-in">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">

        <div id="cart-groups-container"></div>

        <div class="card border-0 shadow-lg bg-orange text-white mb-5 mt-4 rounded-4 overflow-hidden">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-uppercase small fw-bold opacity-75 letter-spacing-1 d-block mb-1">Total Pembayaran Meja</span>
                    <h3 class="fw-extrabold m-0" id="grand-total-display">Rp 0</h3>
                </div>
                <i class="fad fa-wallet fs-1 opacity-25"></i>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-extrabold shadow-lg rounded-4 d-flex align-items-center justify-content-center transition-all" style="background: linear-gradient(135deg, #FF7043 0%, #F4511E 100%); border: none;">
            <i class="fad fa-utensils-alt me-3"></i> Pesan Sekarang
        </button>
    </form>
</div>

        <div id="empty-cart-message" class="text-center py-5 my-5 anim-fade-in" style="display: none;">
            <div class="glass d-inline-flex align-items-center justify-content-center rounded-circle mb-4 shadow-sm" style="width: 160px; height: 160px; border: 4px solid white;">
                <i class="fad fa-shopping-basket text-secondary opacity-25 display-1"></i>
            </div>
            <h3 class="text-dark fw-bold">Keranjang Kosong</h3>
            <p class="text-muted mb-4">Sepertinya Anda belum memilih menu apapun.</p>
            <a href="/pesan/{{ $meja->qr_token }}/menu" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                <i class="fas fa-arrow-left me-2"></i> Jelajahi Menu
            </a>
        </div>
    </div>
</div>

<script>
const currentQrToken = '{{ $meja->qr_token }}';
const currentPemesanId = '{{ session("pemesan_id") }}';
const semuaPemesan = @json($semuaPemesan); // Mengubah data objek pelanggan PHP ke JavaScript Array

// Fungsi menghitung akumulasi kuantitas menu dari seluruh orang di meja
function getTotalQtyInTable(menuId) {
    let total = 0;
    semuaPemesan.forEach(person => {
        const personCartKey = `cart_${currentQrToken}_${person.id}`;
        const personCart = JSON.parse(localStorage.getItem(personCartKey)) || {};
        if (personCart[menuId]) {
            total += personCart[menuId].jumlah;
        }
    });
    return total;
}

function renderCart() {
    const contentDiv = document.getElementById('cart-content');
    const emptyDiv = document.getElementById('empty-cart-message');
    const groupsContainer = document.getElementById('cart-groups-container');
    
    // Trik: Simpan sementara teks yang sudah diketik agar tidak ter-reset saat mencet tombol + atau -
    let tempNotes = {};
    document.querySelectorAll('textarea[name^="catatan["]').forEach(ta => {
        let match = ta.name.match(/catatan\[(.*?)\]/);
        if(match) tempNotes[match[1]] = ta.value;
    });

    groupsContainer.innerHTML = ''; 
    let totalMeja = 0;
    let hasItems = false;
    let flatCartData = []; 

    semuaPemesan.forEach(person => {
        const personCartKey = `cart_${currentQrToken}_${person.id}`;
        const personCart = JSON.parse(localStorage.getItem(personCartKey)) || {};
        
        if (Object.keys(personCart).length > 0) {
            hasItems = true;
            
            let personHtml = `
            <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                <div class="card-header bg-orange text-white border-0 py-3 px-4 d-flex align-items-center" style="background: #F4511E !important;">
                    <i class="fad fa-user-circle me-3 fs-4"></i>
                    <h6 class="fw-bold mb-0">Pesanan: <span class="fw-black">${person.nama}</span> ${person.id == currentPemesanId ? '<span class="badge bg-white text-orange ms-2 rounded-pill small"></span>' : ''}</h6>
                </div>
                <div class="card-body p-0">
            `;
            
            for (let id in personCart) {
                let item = personCart[id];
                let subtotal = item.harga * item.jumlah;
                totalMeja += subtotal;
                
                flatCartData.push({
                    id: item.id,
                    nama: item.nama,
                    harga: item.harga,
                    jumlah: item.jumlah,
                    pemesan_id: person.id,
                    maxStok: item.maxStok
                });
                
                personHtml += `
                <div class="p-3 border-bottom hover-bg-light transition-all">
                    <div class="row align-items-center g-2 flex-nowrap">
                        <div class="col text-truncate">
                            <h6 class="fw-bold text-dark mb-1 text-truncate">${item.nama}</h6>
                            <span class="text-orange fw-extrabold small">Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</span>
                        </div>
                        <div class="col-auto flex-shrink-0">
                            <div class="d-flex align-items-center bg-white rounded-pill p-1 shadow-sm border cart-controls flex-nowrap" style="min-width: 105px;">
                                <button type="button" onclick="updateQty('${person.id}', '${id}', -1)" class="btn btn-sm btn-white rounded-circle p-0 border-0 shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; padding: 0 !important;">
                                    <i class="fas fa-minus text-muted" style="font-size: 0.7rem;"></i>
                                </button>
                                <span class="fw-extrabold text-dark px-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="min-width: 30px;">${item.jumlah}</span>
                                <button type="button" onclick="updateQty('${person.id}', '${id}', 1)" class="btn btn-sm btn-white rounded-circle p-0 border-0 shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; padding: 0 !important;">
                                    <i class="fas fa-plus text-orange" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-auto flex-shrink-0">
                            <button type="button" onclick="removeItem('${person.id}', '${id}')" class="btn btn-outline-danger border-0 rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; padding: 0 !important;">
                                <i class="fad fa-trash-alt fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            }
            
            // Kolom catatan dipindah ke sini (menjadi penutup kartu tiap orang)
            personHtml += `
                </div>
                <div class="card-footer bg-light border-top border-0 p-3 px-4">
                    <label class="form-label small fw-bold text-dark d-flex align-items-center mb-2"><i class="fad fa-sticky-note text-orange me-2"></i> Catatan Khusus ${person.nama}</label>
                    <textarea name="catatan[${person.id}]" class="form-control form-control-sm border-2 bg-white" rows="2" placeholder="Contoh: Sangat pedas, tanpa wijen..."></textarea>
                </div>
            </div>`;
            
            groupsContainer.innerHTML += personHtml;
        }
    });
    
    if (!hasItems) {
        contentDiv.style.display = 'none';
        emptyDiv.style.display = 'block';
        return;
    }
    
    contentDiv.style.display = 'block';
    emptyDiv.style.display = 'none';
    
    document.getElementById('grand-total-display').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(totalMeja)}`;
    document.getElementById('cart_data_input').value = JSON.stringify(flatCartData);

    // Kembalikan isi teks yang tersimpan ke kotaknya masing-masing
    for(let pid in tempNotes) {
        let ta = document.querySelector(`textarea[name="catatan[${pid}]"]`);
        if(ta) ta.value = tempNotes[pid];
    }
}

function updateQty(personId, id, delta) {
    const personCartKey = `cart_${currentQrToken}_${personId}`;
    let personCart = JSON.parse(localStorage.getItem(personCartKey)) || {};
    
    if (!personCart[id]) return;
    
    if (delta > 0) {
        let tableStocks = JSON.parse(localStorage.getItem(`stocks_${currentQrToken}`)) || {};
        let maxStok = personCart[id].maxStok || tableStocks[id] || 0;
        let totalDiMeja = getTotalQtyInTable(id);
        
        if (totalDiMeja >= maxStok) {
            Swal.fire({
                title: 'Stok Tidak Mencukupi',
                text: `Maaf, item "${personCart[id].nama}" sudah mencapai batas maksimum kapasitas stok meja saat ini.`,
                icon: 'warning',
                confirmButtonColor: '#FF7043',
                confirmButtonText: 'OK'
            });
            return;
        }
    }
    
    personCart[id].jumlah += delta;
    if (personCart[id].jumlah < 1) {
        personCart[id].jumlah = 1;
    }
    localStorage.setItem(personCartKey, JSON.stringify(personCart));
    renderCart();
}

function removeItem(personId, id) {
    const personCartKey = `cart_${currentQrToken}_${personId}`;
    let personCart = JSON.parse(localStorage.getItem(personCartKey)) || {};
    
    if (personCart[id]) {
        delete personCart[id];
        localStorage.setItem(personCartKey, JSON.stringify(personCart));
        renderCart();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderCart();
});
</script>

<style>
.hover-bg-light:hover { background-color: #F8F9FA; }
.btn-white { background: white; color: #4A4A4A; }
.transition-all { transition: all 0.3s ease; }
.fw-extrabold { font-weight: 800; }
.letter-spacing-1 { letter-spacing: 1px; }
.anim-fade-in { animation: fadeIn 0.5s ease forwards; opacity: 0; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('scripts')
<script>
@if(session('stok_error'))
    Swal.fire({
        title: 'Stok tidak mencukupi',
        html: `{!! session('stok_error') !!}`,
        icon: 'error',
        confirmButtonColor: '#FF7043',
        confirmButtonText: 'OK'
    });
@endif

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
</script>
@endsection
