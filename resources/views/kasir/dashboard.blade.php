@extends('layouts.kasir')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
            <i class="fad fa-bell-concierge text-orange me-2"></i> Pesanan Masuk
        </h2>
        <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Pantau sesi aktif & pesanan pelanggan secara real-time</p>
    </div>
    <span class="status-badge" style="background:#FFF3E0; color:#F57C00;">
        <i class="fad fa-circle text-orange flash" style="font-size:0.5rem; margin-right:4px;"></i> Live
    </span>
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

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:1rem;">
    {{-- BARIS YANG DITAMBAHKAN: Filter hanya sesi yang pesanan-nya lebih dari 0 --}}
    @php
        $sesiSudahPesan = collect($sesiAktif)->filter(function($sesi) {
            return $sesi->pesanan && $sesi->pesanan->count() > 0;
        });
    @endphp

    {{-- Looping menggunakan variabel $sesiSudahPesan hasil filter di atas --}}
    @forelse($sesiSudahPesan as $sesi)
    @php
        $statusCount = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'dibayar' => 0];
        $adaMenunggu = false;
        foreach ($sesi->pesanan as $p) {
            $statusCount[$p->status] = ($statusCount[$p->status] ?? 0) + 1;
            if ($p->status == 'menunggu') $adaMenunggu = true;
        }
    @endphp
    
    <ion-card style="margin:0; border-left:4px solid {{ $adaMenunggu ? '#F44336' : 'var(--orange)' }}; position:relative; overflow:hidden;">
        <div style="position:absolute; right:-20px; bottom:-20px; opacity:0.04; transform:rotate(-15deg);">
            <i class="fad fa-table" style="font-size:8rem; color:var(--orange);"></i>
        </div>
        
        <ion-card-content style="padding:1.5rem; position:relative; z-index:2;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem;">
                <div>
                    <p style="margin:0 0 4px 0; color:#999; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:1px;">Meja Pelanggan</p>
                    <h3 style="margin:0; font-size:2rem; font-weight:900; color:#2D3436; line-height:1;">{{ $sesi->meja->nomor_meja }}</h3>
                </div>
                <div style="background:rgba(255,87,34,0.1); color:var(--orange); padding:6px 12px; border-radius:12px; font-weight:800; font-size:0.85rem; display:flex; align-items:center; gap:6px;">
                    <i class="fad fa-users"></i> {{ $sesi->pemesan->count() }} Org
                </div>
            </div>

            <div style="display:flex; align-items:center; color:#999; font-size:0.8rem; font-weight:600; margin-bottom:1.2rem;">
                <i class="fad fa-clock" style="color:var(--orange); margin-right:6px;"></i> 
                Mulai: {{ $sesi->dibuka_pada ? $sesi->dibuka_pada->format('H:i') : '-' }}
            </div>

            <div style="background:#FAFAFA; border:1px solid #f0f0f0; border-radius:12px; padding:12px; margin-bottom:1.5rem;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; font-weight:700; color:#666;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#FFC107;"></span> {{ $statusCount['menunggu'] }} Menunggu
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; font-weight:700; color:#666;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#2196F3;"></span> {{ $statusCount['diproses'] }} Diproses
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; font-weight:700; color:#666;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#4CAF50;"></span> {{ $statusCount['selesai'] }} Selesai
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; font-weight:700; color:#666;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#9C27B0;"></span> {{ $statusCount['dibayar'] }} Dibayar
                    </div>
                </div>
            </div>

            <a href="/kasir/sesi/{{ $sesi->id }}" style="text-decoration:none;">
                <ion-button expand="block" color="{{ $adaMenunggu ? 'danger' : 'primary' }}" style="--border-radius:12px; font-weight:700; font-size:0.95rem; height:46px;">
                    Kelola Pesanan
                    <ion-icon name="arrow-forward-outline" slot="end"></ion-icon>
                </ion-button>
            </a>
        </ion-card-content>
    </ion-card>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:4rem 1rem;">
        <div style="width:120px; height:120px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(0,0,0,0.05); margin:0 auto 1.5rem;">
            <i class="fad fa-wind" style="font-size:4rem; color:#e0e0e0;"></i>
        </div>
        <h3 style="margin:0 0 0.5rem; font-size:1.3rem; font-weight:800; color:#2D3436;">Belum Ada Pesanan Masuk</h3>
    </div>
    @endforelse
</div>

<audio id="audio-alert" src="{{ asset('audio/bell.mp3') }}" preload="auto"></audio>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let latestOrderId = {{ \App\Models\Pesanan::max('id') ?? 0 }};
    let audio = document.getElementById('audio-alert');
    let hasInteracted = false;

    // Autoplay unlock
    document.body.addEventListener('click', function() {
        if (!hasInteracted && audio.paused) {
            hasInteracted = true;
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
            }).catch(e => {});
        }
    });

    // Check for new orders every 2.5s
    setInterval(() => {
        fetch('/kasir/api/latest-order?_t=' + new Date().getTime(), { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                if(data.latest_id > latestOrderId) {
                    let playPromise = audio.play();
                    if (playPromise !== undefined) {
                        playPromise.then(_ => {
                            setTimeout(() => { window.location.reload(); }, 2000);
                        }).catch(error => {
                            window.location.reload();
                        });
                    } else {
                        setTimeout(() => { window.location.reload(); }, 2000);
                    }
                }
            })
            .catch(err => console.error(err));
    }, 2500); 
});
</script>
@endsection
