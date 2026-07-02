@extends('layouts.kasir')
@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
    <a href="{{ $sesi->status == 'aktif' ? '/kasir/dashboard' : '/kasir/riwayat-pemesanan' }}" style="text-decoration:none;">
        <ion-button fill="clear" color="medium" style="--padding-start:8px; --padding-end:8px;">
            <ion-icon name="arrow-back-outline" style="font-size:1.3rem;"></ion-icon>
        </ion-button>
    </a>
    <div>
        <h2 style="margin:0; font-size:1.3rem; font-weight:800; color:#2D3436;">
            {{ $sesi->meja->nomor_meja }}
            @if($sesi->status != 'aktif')
                <span style="font-size:0.75rem; background:#E8F5E9; color:#388E3C; padding:4px 8px; border-radius:8px; margin-left:8px; vertical-align:middle; font-weight:700;">
                    <i class="fad fa-check-circle" style="margin-right:3px;"></i>Riwayat
                </span>
            @endif
        </h2>
        <p style="margin:0; color:#999; font-size:0.8rem; font-weight:600;">
            {{ $sesi->status == 'aktif' ? 'Kelola Pesanan Sesi Aktif' : 'Detail Riwayat Selesai pada ' . optional($sesi->ditutup_pada)->format('d/m/Y H:i') }}
        </p>
    </div>
</div>

@if(session('success'))
<ion-card color="success" style="margin-bottom:1rem; --background:rgba(76,175,80,0.1); --color:#388E3C;">
    <ion-card-content style="padding:1rem; display:flex; align-items:center; gap:8px; font-weight:600; font-size:0.85rem;">
        <ion-icon name="checkmark-circle" style="font-size:1.2rem;"></ion-icon>
        {{ session('success') }}
    </ion-card-content>
</ion-card>
@endif
@if(session('error'))
<ion-card style="margin-bottom:1rem; --background:rgba(244,67,54,0.1); --color:#D32F2F;">
    <ion-card-content style="padding:1rem; display:flex; align-items:center; gap:8px; font-weight:600; font-size:0.85rem;">
        <ion-icon name="alert-circle" style="font-size:1.2rem;"></ion-icon>
        {{ session('error') }}
    </ion-card-content>
</ion-card>
@endif

@php $grandTotal = 0; @endphp

@foreach($sesi->pemesan as $p)
{{-- Hanya tampilkan nama pelanggan jika mereka benar-benar memiliki pesanan --}}
@if($p->pesanan && $p->pesanan->count() > 0)
<ion-card style="margin-bottom:1.5rem;">
    <ion-card-header style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
        <ion-card-title style="font-size:1.1rem; font-weight:800; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center;">
                <div style="width:36px; height:36px; border-radius:10px; background:var(--orange-light); color:var(--orange); display:flex; align-items:center; justify-content:center; margin-right:12px;">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                {{ $p->nama }}
            </div>
            <span style="font-size:0.75rem; color:#999; font-weight:600;">{{ $p->created_at->format('H:i') }}</span>
        </ion-card-title>
    </ion-card-header>
    
    <ion-card-content style="padding:0;">
        @foreach($p->pesanan as $pesanan)
        @php $pesananTotal = 0; @endphp
        <div style="padding:1.5rem; {{ !$loop->last ? 'border-bottom:1px solid #f0f0f0;' : '' }}">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:1rem;">
                <span style="color:#999; font-size:0.8rem; font-weight:600;">Pesanan #{{ $pesanan->id }}</span>
                <div style="display:flex; align-items:center; gap:8px;">
                    @php
                        $statusColors = ['menunggu' => '#FFF3E0', 'diproses' => '#E3F2FD', 'selesai' => '#E8F5E9', 'dibayar' => '#F3E5F5'];
                        $textColors = ['menunggu' => '#F57C00', 'diproses' => '#1976D2', 'selesai' => '#388E3C', 'dibayar' => '#7B1FA2'];
                        $statusFlow = ['menunggu', 'diproses', 'selesai', 'dibayar'];
                        $currentIdx = array_search($pesanan->status, $statusFlow);
                        $hasPrev = $currentIdx !== false && $currentIdx > 0;
                        $hasNext = $currentIdx !== false && $currentIdx < count($statusFlow) - 1;
                    @endphp

                    @if($sesi->status == 'aktif')
                    <div style="display:flex; align-items:center; background:{{ $statusColors[$pesanan->status] }}; border-radius:20px; overflow:hidden;">
                        @if($hasPrev)
                        <form action="/kasir/pesanan/{{ $pesanan->id }}/status" method="POST" style="margin:0;">
                            @csrf
                            <input type="hidden" name="direction" value="prev">
                            <button type="submit" style="background:transparent; color:{{ $textColors[$pesanan->status] }}; border:none; padding:6px 10px; cursor:pointer; display:flex; align-items:center;">
                                <ion-icon name="chevron-back-outline" style="font-size:1rem;"></ion-icon>
                            </button>
                        </form>
                        @else
                        <span style="padding:6px 10px; opacity:0.3; display:flex; align-items:center;">
                            <ion-icon name="chevron-back-outline" style="font-size:1rem; color:{{ $textColors[$pesanan->status] }};"></ion-icon>
                        </span>
                        @endif

                        <span style="color:{{ $textColors[$pesanan->status] }}; padding:6px 4px; font-size:0.75rem; font-weight:700; white-space:nowrap;">
                            {{ ucfirst($pesanan->status) }}
                        </span>

                        @if($hasNext)
                        <form action="/kasir/pesanan/{{ $pesanan->id }}/status" method="POST" style="margin:0;">
                            @csrf
                            <input type="hidden" name="direction" value="next">
                            <button type="submit" style="background:transparent; color:{{ $textColors[$pesanan->status] }}; border:none; padding:6px 10px; cursor:pointer; display:flex; align-items:center;">
                                <ion-icon name="chevron-forward-outline" style="font-size:1rem;"></ion-icon>
                            </button>
                        </form>
                        @else
                        <span style="padding:6px 10px; opacity:0.3; display:flex; align-items:center;">
                            <ion-icon name="chevron-forward-outline" style="font-size:1rem; color:{{ $textColors[$pesanan->status] }};"></ion-icon>
                        </span>
                        @endif
                    </div>
                    @else
                    <span style="background:{{ $statusColors[$pesanan->status] }}; color:{{ $textColors[$pesanan->status] }}; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                        {{ ucfirst($pesanan->status) }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:8px 12px; text-align:left; font-size:0.75rem; color:#999; font-weight:700; border-bottom:1px solid #eee;">Item</th>
                            <th style="padding:8px 12px; text-align:center; font-size:0.75rem; color:#999; font-weight:700; border-bottom:1px solid #eee;">Qty</th>
                            <th style="padding:8px 12px; text-align:right; font-size:0.75rem; color:#999; font-weight:700; border-bottom:1px solid #eee;">Harga</th>
                            <th style="padding:8px 12px; text-align:right; font-size:0.75rem; color:#999; font-weight:700; border-bottom:1px solid #eee;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->detailPesanan as $detail)
                        @php $pesananTotal += $detail->subtotal; @endphp
                        <tr>
                            <td style="padding:8px 12px; font-weight:600; font-size:0.85rem; color:#2D3436; border-bottom:1px solid #fafafa;">{{ $detail->menu->nama ?? 'Menu dihapus' }}</td>
                            <td style="padding:8px 12px; text-align:center; font-size:0.85rem; border-bottom:1px solid #fafafa;">{{ $detail->jumlah }}</td>
                            <td style="padding:8px 12px; text-align:right; font-size:0.85rem; color:#999; border-bottom:1px solid #fafafa;">Rp {{ number_format($detail->harga_saat_pesan, 0, ',', '.') }}</td>
                            <td style="padding:8px 12px; text-align:right; font-size:0.85rem; font-weight:700; border-bottom:1px solid #fafafa;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($pesanan->catatan)
            <div style="margin-top:1rem; padding:10px 14px; background:#fff9e6; border-left:4px solid #FFC107; border-radius:4px; font-size:0.8rem; color:#666;">
                <ion-icon name="document-text-outline" style="color:#FFC107; margin-right:4px; vertical-align:middle;"></ion-icon>
                {{ $pesanan->catatan }}
            </div>
            @endif

            <div style="text-align:right; margin-top:1rem; padding-top:1rem; border-top:1px dashed #eee;">
                <span style="font-size:0.85rem; color:#999; margin-right:12px;">Subtotal:</span>
                <span style="font-size:1.1rem; font-weight:800; color:var(--orange);">Rp {{ number_format($pesananTotal, 0, ',', '.') }}</span>
            </div>
        </div>
        @php $grandTotal += $pesananTotal; @endphp
        @endforeach
    </ion-card-content>
</ion-card>
@endif
@endforeach

<ion-card style="margin:0 0 1.5rem 0; --background:linear-gradient(135deg, #FFF3E0, #FFE0B2);">
    <ion-card-content style="padding:1.5rem; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-weight:800; font-size:1.2rem; color:#2D3436;">Grand Total</span>
        <span style="font-weight:800; font-size:2rem; color:var(--orange);">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </ion-card-content>
</ion-card>

@if($sesi->status == 'aktif')
@php $allPaid = $sesi->pesanan->every(fn($p) => $p->status === 'dibayar'); @endphp
<div style="margin-bottom: 1.5rem;">
    @if($allPaid)
    <form action="/kasir/sesi/{{ $sesi->id }}/tutup" method="POST" id="form-tutup-sesi" style="margin: 0;">
        @csrf
        <ion-button expand="block" type="button" onclick="confirmTutupSesi()" color="danger" style="--border-radius:14px; font-weight:800; font-size:1.1rem; height:54px; margin: 0;">
            <ion-icon name="power-outline" slot="start"></ion-icon>
            Tutup Sesi (Selesai Pembayaran)
        </ion-button>
    </form>
    @else
    <form action="/kasir/sesi/{{ $sesi->id }}/tutup-paksa" method="POST" id="form-tutup-sesi-paksa" style="margin:0;">
        @csrf
        <ion-button expand="block" type="button" onclick="confirmTutupSesiPaksa()" color="warning" style="--border-radius:14px; font-weight:800; font-size:1.1rem; height:54px; margin:0;">
            <ion-icon name="alert-circle-outline" slot="start"></ion-icon>
            Tutup Sesi Paksa (Batalkan Pesanan Belum Bayar)
        </ion-button>
    </form>
    <p style="text-align:center; color:#999; font-size:0.8rem; margin:10px 0 0;">
        <ion-icon name="information-circle-outline" style="vertical-align:middle;"></ion-icon>
        Terdapat pesanan yang belum dibayar. Sesi akan ditutup paksa dan pesanan yang belum dibayar akan dibatalkan otomatis.
    </p>
    @endif
</div>
@endif

@endsection

@section('scripts')
<script>
function confirmTutupSesi() {
    Swal.fire({
        title: 'Tutup Sesi & Selesaikan Transaksi?',
        text: "Pastikan pelanggan sudah membayar seluruh pesanan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tutup Sesi!',
        cancelButtonText: 'Batal',
        customClass: {
            container: 'font-sans'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-tutup-sesi').submit();
        }
    })
}

function confirmTutupSesiPaksa() {
    Swal.fire({
        title: 'Tutup Sesi Paksa?',
        text: "Pesanan yang BELUM DIBAYAR akan DIBATALKAN dan DIHAPUS permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff9800',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tutup Paksa!',
        cancelButtonText: 'Batal',
        customClass: {
            container: 'font-sans'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-tutup-sesi-paksa').submit();
        }
    })
}
</script>
@endsection