@extends('layouts.kasir')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
            <i class="fad fa-history text-orange me-2"></i> Riwayat Pesanan Harian
        </h2>
        <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Daftar lengkap pesanan dalam 24 Jam</p>
    </div>
    <div style="display:flex; gap:8px; align-items:center;">
        <span style="background:rgba(76,175,80,0.1); color:#388E3C; padding:6px 14px; border-radius:10px; font-size:0.8rem; font-weight:700;">
            <i class="fad fa-check-circle" style="margin-right:4px;"></i> {{ $riwayat->count() }} Pesanan
        </span>
    </div>
</div>

@if($riwayat->count() > 0)
    @php
        $totalPelanggan = 0;
        foreach($riwayat as $r) {
            // Mengekstrak ID pemesan unik hanya dari daftar pesanan yang statusnya 'dibayar'
            $totalPelanggan += $r->pesanan->where('status', 'dibayar')->pluck('pemesan_id')->unique()->count();
        }
    @endphp

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <ion-card style="margin:0; background:linear-gradient(135deg, rgba(76,175,80,0.1), rgba(139,195,74,0.1)); border:1px solid rgba(76,175,80,0.2);">
            <ion-card-content style="padding:1.5rem;">
                <div style="color:#666; font-size:0.8rem; font-weight:600; margin-bottom:8px; text-transform:uppercase;">Total Pesanan</div>
                <div style="font-size:1.8rem; font-weight:900; color:#388E3C;">{{ $riwayat->count() }}</div>
            </ion-card-content>
        </ion-card>

        <ion-card style="margin:0; background:linear-gradient(135deg, rgba(255,152,0,0.1), rgba(255,167,38,0.1)); border:1px solid rgba(255,152,0,0.2);">
            <ion-card-content style="padding:1.5rem;">
                <div style="color:#666; font-size:0.8rem; font-weight:600; margin-bottom:8px; text-transform:uppercase;">Total Penjualan</div>
                <div style="font-size:1.8rem; font-weight:900; color:#F57F17;">Rp {{ number_format($riwayat->sum('total_pembayaran'), 0, ',', '.') }}</div>
            </ion-card-content>
        </ion-card>

        <ion-card style="margin:0; background:linear-gradient(135deg, rgba(33,150,243,0.1), rgba(66,165,245,0.1)); border:1px solid rgba(33,150,243,0.2);">
            <ion-card-content style="padding:1.5rem;">
                <div style="color:#666; font-size:0.8rem; font-weight:600; margin-bottom:8px; text-transform:uppercase;">Total Pelanggan</div>
                <div style="font-size:1.8rem; font-weight:900; color:#1976D2;">{{ $totalPelanggan }} <span style="font-size:1.6 rem; font-weight:700;">Orang</span></div>
            </ion-card-content>
        </ion-card>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; background: #fff; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 5px 15px; border: 1px solid rgba(0,0,0,0.05);">
            <i class="fad fa-search" style="color: #FF7043; font-size: 1.2rem; margin-right: 12px;"></i>
            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama pemesan..." style="border: none; outline: none; width: 100%; padding: 12px 0; font-size: 0.95rem; font-weight: 600; color: #333; background: transparent;">
        </div>
    </div>

    <ion-card style="margin:0; border-radius:16px; overflow:hidden;">
        <div style="overflow-x: auto;">
            <table style="width:100%; border-collapse:collapse;" id="tabel-riwayat">
                <thead>
                    <tr style="background:#F5F5F5;">
                        <th style="padding:14px; text-align:left; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Tanggal</th>
                        <th style="padding:14px; text-align:left; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Waktu Pesan</th>
                        <th style="padding:14px; text-align:left; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Waktu Selesai</th>
                        <th style="padding:14px; text-align:left; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Nomor Meja</th>
                        <th style="padding:14px; text-align:left; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Nama Pemesan</th>
                        <th style="padding:14px; text-align:right; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Total Pembayaran</th>
                        <th style="padding:14px; text-align:center; color:#999; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid #EEEEEE;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $item)
                    @php
                        $pesanan = $item->pesanan->first();
                        $namaPelanggan = $item->pemesan->first() ? $item->pemesan->first()->nama : 'Anonim';
                    @endphp
                    <tr class="riwayat-row" data-nama="{{ strtolower($namaPelanggan) }}" style="border-bottom:1px solid #F5F5F5;">
                        <td style="padding:14px; font-weight:600; color:#2D3436;">
                            {{ optional($pesanan?->created_at)->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="padding:14px; color:#666; font-weight:500;">
                            {{ optional($pesanan?->created_at)->format('H:i') ?? '-' }}
                        </td>
                        <td style="padding:14px; color:#666; font-weight:500;">
                            @if($pesanan?->updated_at)
                                {{ $pesanan->updated_at->format('H:i') }}
                            @else
                                <span style="color:#999; font-size:0.85rem;">—</span>
                            @endif
                        </td>
                        <td style="padding:14px;">
                            <span style="display:inline-flex; align-items:center; justify-content:center; width:45px; height:40px; background:rgba(255,87,34,0.1); color:var(--orange); font-weight:800; border-radius:10px;">
                                {{ $item->meja->nomor_meja }}
                            </span>
                        </td>
                        <td style="padding:14px; font-weight:700; color:#2D3436; text-transform: capitalize;">
                            {{ $namaPelanggan }}
                        </td>
                        <td style="padding:14px; text-align:right; font-weight:700; color:#1A1A2E; font-size:0.95rem;">
                            Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}
                        </td>
                        <td style="padding:14px; text-align:center;">
                            <ion-button size="small" fill="clear" color="primary"
                                onclick="lihatDetail({{ $item->id }})"
                                style="--border-radius:8px; font-weight:700; font-size:0.75rem; margin:0; --box-shadow:none;">
                                <ion-icon name="eye-outline" slot="start"></ion-icon>
                                Lihat
                            </ion-button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div id="emptySearchMsg" style="text-align:center; padding:3rem 1rem; display:none;">
                <i class="fad fa-search-minus" style="font-size:3rem; color:#e0e0e0; margin-bottom:1rem;"></i>
                <h4 style="font-weight:700; color:#666;">Nama tidak ditemukan</h4>
                <p style="font-size:0.9rem; color:#999;">Tidak ada pesanan dengan nama pelanggan tersebut hari ini.</p>
            </div>
        </div>
    </ion-card>

@else
    <div style="text-align:center; padding:4rem 1rem;">
        <div style="width:120px; height:120px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(0,0,0,0.05); margin:0 auto 1.5rem;">
            <i class="fad fa-inbox" style="font-size:4rem; color:#e0e0e0;"></i>
        </div>
        <h3 style="margin:0 0 0.5rem; font-size:1.3rem; font-weight:800; color:#2D3436;">Belum Ada Riwayat</h3>
        <p style="margin:0; color:#999; font-size:0.9rem; max-width:300px; margin:0 auto;">Riwayat pemesanan akan muncul di sini setelah ada pesanan yang selesai dibayar.</p>
    </div>
@endif

@endsection

@section('scripts')
<script>
// Logika untuk Tombol Detail
function lihatDetail(sejiId) {
    window.location.href = '/kasir/sesi/' + sejiId;
}

// Logika Pencarian Nama Pelanggan Real-time
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.riwayat-row');
    const emptyMsg = document.getElementById('emptySearchMsg');
    const tableHeader = document.querySelector('thead');
    let visibleCount = 0;

    rows.forEach(row => {
        const nama = row.getAttribute('data-nama');
        
        if (nama.includes(query)) {
            row.style.display = ''; 
            visibleCount++;
        } else {
            row.style.display = 'none'; 
        }
    });

    if (visibleCount === 0 && rows.length > 0) {
        emptyMsg.style.display = 'block';
        tableHeader.style.display = 'none';
    } else {
        emptyMsg.style.display = 'none';
        tableHeader.style.display = '';
    }
});
</script>
@endsection