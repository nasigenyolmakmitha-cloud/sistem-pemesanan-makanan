@extends('layouts.admin')
@section('page-title', 'QR Code — ' . $meja->nomor_meja)
@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
    <a href="/admin/meja" style="text-decoration:none;">
        <ion-button fill="clear" color="medium" style="--padding-start:8px; --padding-end:8px;">
            <ion-icon name="arrow-back-outline" style="font-size:1.3rem;"></ion-icon>
        </ion-button>
    </a>
    <div>
        <h2 style="margin:0; font-size:1.3rem; font-weight:800; color:#2D3436;">QR Code — {{ $meja->nomor_meja }}</h2>
        <p style="margin:0; color:#999; font-size:0.8rem; font-weight:600;">Cetak dan letakkan di meja pelanggan</p>
    </div>
</div>

<div style="text-align:center;">
    <!-- COASTER CONTAINER -->
    <div style="display:flex; justify-content:center; margin-bottom:2rem;">
        <div id="qr-coaster" style="position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; width:400px; height:400px; background-color:#1a1a1a; border-radius:50%; padding:2rem; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <!-- Table Text -->
            <div style="margin-bottom:1.5rem; display:flex; align-items:flex-end; justify-content:center; width:100%;">
                <span style="color:#fff; font-size:1.8rem; font-weight:500; font-family:'Helvetica Neue',Arial,sans-serif;">Meja</span>
                <span style="color:#fff; font-size:3.5rem; font-weight:800; font-family:'Helvetica Neue',Arial,sans-serif; line-height:0.8; margin-left:12px;">{{ str_replace('Meja ', '', $meja->nomor_meja) }}</span>
            </div>

            <!-- QR Code Block -->
            <div style="background:#fff; padding:12px; border-radius:16px; margin-bottom:1.5rem;">
                {!! QrCode::size(160)->margin(0)->generate($url) !!}
            </div>

            <!-- Footer Text -->
            <div style="color:#fff; font-size:1.1rem; font-weight:600; font-family:'Helvetica Neue',Arial,sans-serif; letter-spacing:1.5px; margin-bottom:0.5rem;">
                Scan . Order . Pay
            </div>
            <div style="color:#999; font-size:0.8rem; font-family:'Helvetica Neue',Arial,sans-serif; font-weight:400;">
                Powered by Nasi Be Genyol
            </div>
        </div>
    </div>

    <ion-button onclick="downloadCoaster()" color="primary" style="--border-radius:20px; font-weight:700; font-size:0.9rem; height:50px; --padding-start:32px; --padding-end:32px;">
        <ion-icon name="download-outline" slot="start"></ion-icon>
        Download QR Code
    </ion-button>
    <p style="color:#999; font-size:0.8rem; margin-top:0.8rem;">
        Link URL: <a href="{{ $url }}" target="_blank" style="color:var(--orange);">{{ $url }}</a>
    </p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadCoaster() {
    const coaster = document.getElementById('qr-coaster');
    html2canvas(coaster, {
        backgroundColor: null,
        scale: 3,
    }).then(canvas => {
        const link = document.createElement('a');
        const tableName = '{{ str_replace('Meja ', '', $meja->nomor_meja) }}'.trim();
        link.download = `QR_Meja_${tableName}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}
</script>

@endsection
