@extends('layouts.admin')
@section('page-title', 'Dashboard & Statistik')
@section('content')

{{-- Summary Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <ion-card class="stat-card" style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Pendapatan Hari Ini</p>
                    <h2 style="margin:0.3rem 0 0; font-size:1.6rem; font-weight:800; color:#2D3436;">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h2>
                </div>
                <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#4CAF50,#66BB6A); display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="cash-outline" style="color:#fff; font-size:1.3rem;"></ion-icon>
                </div>
            </div>
            <i class="fad fa-money-bill-wave stat-icon" style="color:#4CAF50;"></i>
        </ion-card-content>
    </ion-card>

    <ion-card class="stat-card" style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Pendapatan Bulan Ini</p>
                    <h2 style="margin:0.3rem 0 0; font-size:1.6rem; font-weight:800; color:#2D3436;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h2>
                </div>
                <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#FF9800,#FFB74D); display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="trending-up-outline" style="color:#fff; font-size:1.3rem;"></ion-icon>
                </div>
            </div>
            <i class="fad fa-chart-line stat-icon" style="color:#FF9800;"></i>
        </ion-card-content>
    </ion-card>

    <ion-card class="stat-card" style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Menu</p>
                    <h2 style="margin:0.3rem 0 0; font-size:1.6rem; font-weight:800; color:#2D3436;">{{ $totalMenu }}</h2>
                </div>
                <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#FF5722,#FF7043); display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="restaurant-outline" style="color:#fff; font-size:1.3rem;"></ion-icon>
                </div>
            </div>
            <i class="fad fa-utensils stat-icon" style="color:#FF5722;"></i>
        </ion-card-content>
    </ion-card>

    <ion-card class="stat-card" style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Total Meja</p>
                    <h2 style="margin:0.3rem 0 0; font-size:1.6rem; font-weight:800; color:#2D3436;">{{ $totalMeja }}</h2>
                </div>
                <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#2196F3,#42A5F5); display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="grid-outline" style="color:#fff; font-size:1.3rem;"></ion-icon>
                </div>
            </div>
            <i class="fad fa-table stat-icon" style="color:#2196F3;"></i>
        </ion-card-content>
    </ion-card>
</div>

{{-- Chart & Top Menu Row --}}
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
    {{-- Revenue Chart --}}
    <ion-card style="margin:0;">
        <ion-card-header>
            <ion-card-title style="font-size:1rem; font-weight:700;">
                <ion-icon name="bar-chart-outline" style="margin-right:6px; color:var(--orange);"></ion-icon>
                Pendapatan 7 Hari Terakhir
            </ion-card-title>
        </ion-card-header>
        <ion-card-content>
            <canvas id="revenueChart" height="200"></canvas>
        </ion-card-content>
    </ion-card>

    {{-- Top Menu --}}
    <ion-card style="margin:0;">
        <ion-card-header>
            <ion-card-title style="font-size:1rem; font-weight:700;">
                <ion-icon name="trophy-outline" style="margin-right:6px; color:var(--orange);"></ion-icon>
                Menu Terlaris
            </ion-card-title>
        </ion-card-header>
        <ion-card-content style="padding-top:0;">
            @forelse($menuTerlaris as $index => $item)
            <div style="display:flex; align-items:center; padding:10px 0; {{ !$loop->last ? 'border-bottom:1px solid #f5f5f5;' : '' }}">
                <div style="width:28px; height:28px; border-radius:8px; background:{{ $index < 3 ? 'linear-gradient(135deg,#FF5722,#FF7043)' : '#f0f0f0' }}; color:{{ $index < 3 ? '#fff' : '#999' }}; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.75rem; margin-right:10px;">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700; font-size:0.85rem; color:#2D3436;">{{ $item->menu->nama ?? 'Menu dihapus' }}</div>
                </div>
                <div style="font-weight:800; font-size:0.85rem; color:var(--orange);">{{ $item->total_terjual }}x</div>
            </div>
            @empty
            <p style="color:#999; text-align:center; padding:2rem 0; font-size:0.85rem;">Belum ada data penjualan</p>
            @endforelse
        </ion-card-content>
    </ion-card>
</div>

{{-- Quick Stats --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
    <ion-card style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:50px; height:50px; border-radius:14px; background:#E8F5E9; display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="checkmark-circle-outline" style="font-size:1.5rem; color:#4CAF50;"></ion-icon>
                </div>
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700;">Total Sesi Selesai</p>
                    <h3 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">{{ $totalSesiSelesai }}</h3>
                </div>
            </div>
        </ion-card-content>
    </ion-card>

    <ion-card style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:50px; height:50px; border-radius:14px; background:#FFF3E0; display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="flash-outline" style="font-size:1.5rem; color:#FF9800;"></ion-icon>
                </div>
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700;">Sesi Aktif Sekarang</p>
                    <h3 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">{{ $sesiAktif }}</h3>
                </div>
            </div>
        </ion-card-content>
    </ion-card>

    <ion-card style="margin:0;">
        <ion-card-content style="padding: 1.5rem;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:50px; height:50px; border-radius:14px; background:#E3F2FD; display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="wallet-outline" style="font-size:1.5rem; color:#2196F3;"></ion-icon>
                </div>
                <div>
                    <p style="margin:0; color:#999; font-size:0.75rem; font-weight:700;">Total Pendapatan</p>
                    <h3 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </ion-card-content>
    </ion-card>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    const data7Hari = @json($pendapatan7Hari);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data7Hari.map(d => d.label + '\n' + d.tanggal),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data7Hari.map(d => d.total),
                backgroundColor: data7Hari.map((d, i) => i === data7Hari.length - 1 
                    ? 'rgba(255, 87, 34, 0.85)' 
                    : 'rgba(255, 87, 34, 0.25)'),
                borderColor: 'rgba(255, 87, 34, 1)',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000) + 'K';
                        },
                        font: { size: 11, weight: 600 }
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: {
                    ticks: { font: { size: 11, weight: 600 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection