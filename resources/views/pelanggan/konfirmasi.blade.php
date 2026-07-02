@extends('layouts.app')
@section('content')
<div class="row justify-content-center mt-3 mt-md-5">
    <div class="col-md-8 col-lg-7">
        <div class="text-center mb-5">
            <div class="bg-white border rounded-4 p-3 p-md-4 shadow-sm mb-4">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 120px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto;">
                        <dotlottie-player src="{{ asset('lottie/Correct.lottie') }}" background="transparent" speed="1" style="width: 120px; height: 120px;" autoplay></dotlottie-player>
                    </div>
                </div>
            </div>
            <h2 class="fw-bold text-dark">Pesanan Terkirim!</h2>
            <p class="text-muted fs-5 mb-0">Pesanan kamu berhasil dikirim ke kasir.</p>
        </div>

        @php $grandTotal = 0; @endphp
        @foreach($pesanans as $pesanan)
        <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark"><i class="fal fa-receipt text-orange me-2"></i> Pesanan: {{ $pesanan->pemesan->nama }}</span>
                    <div id="status-container-{{ $pesanan->id }}">
                        @php
                            $badgeClass = 'bg-warning';
                            $iconClass = 'fal fa-clock';
                            if($pesanan->status == 'diproses') { $badgeClass = 'bg-primary'; $iconClass = 'fal fa-spinner-third fa-spin'; }
                            elseif($pesanan->status == 'selesai') { $badgeClass = 'bg-success'; $iconClass = 'fal fa-check-circle'; }
                            elseif($pesanan->status == 'dibayar') { $badgeClass = 'bg-info'; $iconClass = 'fal fa-receipt'; }
                        @endphp
                        <span class="badge {{ $badgeClass }} text-dark rounded-pill px-3"><i class="{{ $iconClass }} me-1"></i> {{ ucfirst($pesanan->status) }}</span>
                    </div>
                </div>
                <small class="text-muted">{{ $meja->nomor_meja }} — {{ $pesanan->created_at->format('H:i') }}</small>
            </div>
            <div class="card-body p-0">
                @php $subtotalSesi = 0; @endphp
                @foreach($pesanan->detailPesanan as $detail)
                @php $subtotalSesi += $detail->subtotal; $grandTotal += $detail->subtotal; @endphp
                <div class="d-flex justify-content-between align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <span class="fw-semibold text-dark">{{ $detail->menu->nama ?? 'Menu dihapus' }}</span>
                        <span class="text-muted ms-2">x{{ $detail->jumlah }}</span>
                    </div>
                    <span class="fw-bold text-orange">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="card-footer bg-light border-top p-3 px-4 text-end">
                <span class="text-secondary small me-2">Subtotal</span>
                <span class="fw-bold text-dark">Rp {{ number_format($subtotalSesi, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($pesanan->catatan)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <small class="text-muted"><i class="fal fa-sticky-note me-1"></i> Catatan {{ $pesanan->pemesan->nama }}:</small>
                <p class="mb-0 fw-medium">{{ $pesanan->catatan }}</p>
            </div>
        </div>
        @endif
        @endforeach

        <div class="card border-0 shadow-sm bg-orange text-white mb-4 rounded-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-5">Total Bayar Meja</span>
                <span class="fw-bold fs-4">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="alert border-0 bg-info bg-opacity-10 text-info d-flex align-items-center rounded-3 mb-4">
            <i class="fad fa-info-circle fs-4 me-3"></i>
            <small>Tunjukkan halaman ini ke kasir untuk pembayaran.</small>
        </div>

        <a href="/pesan/{{ $meja->qr_token }}/menu" class="btn btn-primary w-100 py-3 fs-5 fw-bold shadow d-flex align-items-center justify-content-center">
            <i class="fad fa-plus-circle me-2"></i> Tambah Pesanan Lagi
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
<script>
    // 0. Clear Local Storage Cart if just ordered
    @if(session('clear_cart'))
        for (let i = localStorage.length - 1; i >= 0; i--) {
            let key = localStorage.key(i);
            if (key && key.startsWith('cart_{{ $meja->qr_token }}_')) {
                localStorage.removeItem(key);
            }
        }
    @endif

    // 1. Prevent Browser Back Button
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        window.history.pushState(null, "", window.location.href);
    };

    // 2. Real-time Status Polling
    function updateStatus() {
        fetch('/pesan/{{ $meja->qr_token }}/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.pesanans.forEach(pesanan => {
                        // Update badges
                        const container = document.getElementById(`status-container-${pesanan.id}`);
                        if (container) {
                            const s = pesanan.status || 'menunggu';
                            let bgClass = 'bg-warning';
                            let iconClass = 'fal fa-clock';
                            let statusText = s.charAt(0).toUpperCase() + s.slice(1);
                            
                            if (s === 'diproses') { bgClass = 'bg-primary'; iconClass = 'fal fa-spinner-third fa-spin'; }
                            else if (s === 'selesai') { bgClass = 'bg-success'; iconClass = 'fal fa-check-circle'; }
                            else if (s === 'dibayar') { bgClass = 'bg-info'; iconClass = 'fal fa-receipt'; }
                            
                            const newBadge = `<span class="badge ${bgClass} text-dark rounded-pill px-3"><i class="${iconClass} me-1"></i> ${statusText}</span>`;
                            if (container.innerHTML.trim() !== newBadge.trim()) {
                                container.innerHTML = newBadge;
                            }
                        }
                    });

                    // 1. Check for Session Closure
                    if (data.session_status === 'selesai') {
                        window.location.href = `/pesan/{{ $meja->qr_token }}/terimakasih`;
                        return;
                    }
                }
            })
            .catch(err => console.error('Error fetching status:', err));
    }

    // Poll every 5 seconds
    setInterval(updateStatus, 5000);
</script>
@endsection
