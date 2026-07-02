@extends('layouts.app')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="/admin/dashboard" class="btn btn-light rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height:45px;">
        <i class="fas fa-arrow-left text-orange"></i>
    </a>
    <div>
        <h2 class="fw-bold text-dark mb-0"><i class="fad fa-clipboard-list text-orange me-2"></i> {{ $sesi->meja->nomor_meja }}</h2>
        <span class="text-muted small fw-bold">Detail Sesi Aktif</span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 rounded-4"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 rounded-4"><i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}</div>
@endif

@php $grandTotal = 0; @endphp

@foreach($sesi->pemesan as $p)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="fad fa-user text-orange me-2"></i> {{ $p->nama }}</h5>
            <small class="text-muted">{{ $p->created_at->format('H:i') }}</small>
        </div>
    </div>
    <div class="card-body p-0">
        @foreach($p->pesanan as $pesanan)
        @php $pesananTotal = 0; @endphp
        <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">Pesanan #{{ $pesanan->id }}</span>
                <div class="d-flex align-items-center gap-2">
                    @php
                        $statusColors = ['menunggu' => 'warning', 'diproses' => 'info', 'selesai' => 'primary', 'dibayar' => 'success'];
                        $statusIcons = ['menunggu' => 'fa-clock', 'diproses' => 'fa-fire', 'selesai' => 'fa-check', 'dibayar' => 'fa-money-bill-wave'];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$pesanan->status] }} rounded-pill px-3 py-2">
                        <i class="fal {{ $statusIcons[$pesanan->status] }} me-1"></i> {{ ucfirst($pesanan->status) }}
                    </span>
                    @if($sesi->status == 'aktif' && $pesanan->status !== 'dibayar')
                    <form action="/admin/pesanan/{{ $pesanan->id }}/status" method="POST" class="d-inline">
                        @csrf
                        @php
                            $nextStatus = ['menunggu' => 'diproses', 'diproses' => 'selesai', 'selesai' => 'dibayar'];
                            $next = $nextStatus[$pesanan->status] ?? null;
                        @endphp
                        @if($next)
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-arrow-right me-1"></i> {{ ucfirst($next) }}
                        </button>
                        @endif
                    </form>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="text-muted small">
                        <tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->detailPesanan as $detail)
                        @php $pesananTotal += $detail->subtotal; @endphp
                        <tr>
                            <td class="fw-medium">{{ $detail->menu->nama ?? 'Menu dihapus' }}</td>
                            <td class="text-center">{{ $detail->jumlah }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($detail->harga_saat_pesan, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pesanan->catatan)
            <div class="mt-2 p-2 bg-light rounded-3 small"><i class="fal fa-sticky-note text-orange me-1"></i> {{ $pesanan->catatan }}</div>
            @endif

            <div class="text-end mt-2">
                <span class="fw-bold text-orange">Subtotal: Rp {{ number_format($pesananTotal, 0, ',', '.') }}</span>
            </div>
        </div>
        @php $grandTotal += $pesananTotal; @endphp
        @endforeach
    </div>
</div>
@endforeach

<div class="card border-0 shadow-sm bg-light mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark fs-5">Grand Total Sesi</span>
        <span class="fw-bold text-orange fs-3">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
</div>

@if($sesi->status == 'aktif')
@php $allPaid = $sesi->pesanan->every(fn($p) => $p->status === 'dibayar'); @endphp
<form action="/admin/sesi/{{ $sesi->id }}/tutup" method="POST" id="form-tutup-sesi">
    @csrf
    <button type="button" onclick="confirmTutupSesi()" class="btn {{ $allPaid ? 'btn-danger' : 'btn-secondary' }} w-100 py-3 fs-5 fw-bold shadow d-flex align-items-center justify-content-center"
        {{ $allPaid ? '' : 'disabled' }}>
        <i class="fad fa-door-closed me-2"></i> Tutup Sesi Meja
    </button>
</form>
@if(!$allPaid)
<p class="text-center text-muted small mt-2"><i class="fal fa-info-circle me-1"></i> Semua pesanan harus berstatus "dibayar" untuk menutup sesi.</p>
@endif
@endif
@endsection

@section('scripts')
<script>
function confirmTutupSesi() {
    Swal.fire({
        title: 'Tutup Sesi Meja Ini?',
        text: "Meja akan dikosongkan secara sistem dan siap untuk pelanggan baru. Lanjutkan?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tutup Sesi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-tutup-sesi').submit();
        }
    })
}
</script>
@endsection
