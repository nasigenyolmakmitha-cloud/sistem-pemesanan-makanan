@extends('layouts.admin')
@section('page-title', 'Laporan Pendapatan')
@section('content')

<div style="margin-bottom:1.5rem;">
    <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
        <ion-icon name="wallet-outline" style="color:var(--orange); margin-right:6px; vertical-align:middle;"></ion-icon>
        Laporan Pendapatan
    </h2>
    <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Lihat total pendapatan berdasarkan hari, bulan, atau tahun</p>
</div>

{{-- Filter --}}
<ion-card style="margin-bottom:1.5rem;">
    <ion-card-content style="padding:1.2rem 1.5rem;">
        <form action="/admin/pendapatan" method="GET" style="display:flex; align-items:flex-end; gap:1rem; flex-wrap:wrap;">
            
            <div style="flex:1; min-width:150px;">
                <label style="display:block; font-weight:700; font-size:0.75rem; color:#999; margin-bottom:4px; text-transform:uppercase;">Jenis Laporan</label>
                <select name="jenis" id="jenisLaporan" onchange="toggleFilterInputs()" style="width:100%; padding:10px 14px; border:2px solid #eee; border-radius:10px; font-size:0.85rem; font-family:inherit; box-sizing:border-box;">
                    <option value="harian" {{ $jenis == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="bulanan" {{ $jenis == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $jenis == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <div style="flex:1; min-width:150px;" id="inputHarian">
                <label style="display:block; font-weight:700; font-size:0.75rem; color:#999; margin-bottom:4px; text-transform:uppercase;">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    style="width:100%; padding:10px 14px; border:2px solid #eee; border-radius:10px; font-size:0.85rem; font-family:inherit; box-sizing:border-box;">
            </div>

            <div style="flex:1; min-width:150px;" id="inputBulanan" style="display:none;">
                <label style="display:block; font-weight:700; font-size:0.75rem; color:#999; margin-bottom:4px; text-transform:uppercase;">Pilih Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}"
                    style="width:100%; padding:10px 14px; border:2px solid #eee; border-radius:10px; font-size:0.85rem; font-family:inherit; box-sizing:border-box;">
            </div>

            <div style="flex:1; min-width:150px;" id="inputTahunan" style="display:none;">
                <label style="display:block; font-weight:700; font-size:0.75rem; color:#999; margin-bottom:4px; text-transform:uppercase;">Pilih Tahun</label>
                <select name="tahun" style="width:100%; padding:10px 14px; border:2px solid #eee; border-radius:10px; font-size:0.85rem; font-family:inherit; box-sizing:border-box;">
                    @for($i = now()->year; $i >= 2023; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <ion-button type="submit" color="primary" style="--border-radius:10px; font-weight:700; height:44px;">
                <ion-icon name="search-outline" slot="start"></ion-icon>
                Tampilkan
            </ion-button>
        </form>
    </ion-card-content>
</ion-card>

<ion-card style="margin:0; --background:linear-gradient(135deg, #FFF3E0, #FFE0B2); border-radius:15px; box-shadow:0 10px 20px rgba(0,0,0,0.05);">
    <ion-card-content style="padding:2.5rem; text-align:center;">
        <span style="display:block; font-weight:700; font-size:1.1rem; color:#2D3436; margin-bottom:10px; text-transform:uppercase; letter-spacing:1px;">
            Total Pendapatan <br>
            <span style="font-size:0.9rem; color:#666; font-weight:600;">( {{ $labelRentang }} )</span>
        </span>
        <span style="display:block; font-weight:800; font-size:3.5rem; color:var(--orange); text-shadow:2px 2px 0px rgba(255,255,255,0.5);">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </span>
    </ion-card-content>
</ion-card>

<!-- Menu Terjual Section -->
<ion-card style="margin:1.5rem 0 0; border-radius:15px; box-shadow:0 10px 20px rgba(0,0,0,0.05);">
    <ion-card-content style="padding:1.5rem;">
        <h3 style="margin:0 0 1.5rem; font-weight:700; font-size:1rem; color:#2D3436;">Jumlah Menu Terjual</h3>
        
        @if($menuTerjual->count() > 0)
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:2px solid #eee;">
                        <th style="padding:12px 10px; text-align:left; font-weight:700; color:#666; background:#fafafa; border-radius:8px 0 0 0;">Menu</th>
                        <th style="padding:12px 10px; text-align:right; font-weight:700; color:#666; background:#fafafa; border-radius:0 8px 0 0;">Jumlah Porsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuTerjual as $item)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px 10px; color:#2D3436; font-weight:500;">{{ $item->menu->nama ?? 'Menu (Dihapus)' }}</td>
                            <td style="padding:12px 10px; text-align:right; color:var(--orange); font-weight:700;">{{ $item->total_jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align:center; color:#999; padding:2rem 1rem; margin:0;">Tidak ada data menu terjual untuk periode ini</p>
        @endif
    </ion-card-content>
</ion-card>

<script>
    function toggleFilterInputs() {
        const jenis = document.getElementById('jenisLaporan').value;
        const divHarian = document.getElementById('inputHarian');
        const divBulanan = document.getElementById('inputBulanan');
        const divTahunan = document.getElementById('inputTahunan');

        divHarian.style.display = 'none';
        divBulanan.style.display = 'none';
        divTahunan.style.display = 'none';

        if (jenis === 'harian') {
            divHarian.style.display = 'block';
        } else if (jenis === 'bulanan') {
            divBulanan.style.display = 'block';
        } else if (jenis === 'tahunan') {
            divTahunan.style.display = 'block';
        }
    }
    
    // Inisialisasi saat pertama kali diload
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs();
    });
</script>

@endsection
