@extends('layouts.admin')
@section('page-title', 'Manajemen Meja')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#2D3436;">
            <ion-icon name="grid-outline" style="color:var(--orange); margin-right:6px; vertical-align:middle;"></ion-icon>
            Manajemen Meja
        </h2>
        <p style="margin:0.3rem 0 0; color:#999; font-size:0.8rem; font-weight:600;">Kelola meja dan QR Code restoran</p>
    </div>
</div>

{{-- Add Table Form --}}
<ion-card style="margin-bottom:1.5rem;">
    <ion-card-content style="padding:1.2rem 1.5rem;">
        <form action="/admin/meja" method="POST" id="formTambahMeja" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            @csrf
            <div style="flex:1; min-width:200px;">
                <div id="input-container" style="display:flex; align-items:center; width:100%; border:2px solid #eee; border-radius:12px; padding:0 16px; background:#fff; transition:all 0.2s; box-sizing:border-box;">
                    <span style="font-weight:800; color:#2D3436; font-size:0.9rem; margin-right:6px;">Meja</span>
                    
                    <input type="text" id="inputAngkaMeja" inputmode="numeric" required placeholder="Contoh: 6"
                        style="flex:1; padding:12px 0; border:none; outline:none; font-size:0.9rem; font-weight:700; font-family:inherit; background:transparent;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        onfocus="document.getElementById('input-container').style.borderColor='var(--orange)'" 
                        onblur="document.getElementById('input-container').style.borderColor='#eee'">
                </div>
                
                <input type="hidden" name="nomor_meja" id="hiddenNomorMeja">
            </div>
            
            <button type="submit" style="background:var(--orange); color:#fff; border:none; border-radius:12px; font-weight:700; height:46px; padding:0 20px; cursor:pointer; display:flex; align-items:center; gap:8px;">
                <ion-icon name="add-outline"></ion-icon>
                Tambah Meja
            </button>
        </form>
    </ion-card-content>
</ion-card>

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

{{-- Meja Grid --}}
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1rem;">
    @foreach($mejas as $m)
    @php $sesi = \App\Models\SesiPemesanan::where('meja_id', $m->id)->where('status', 'aktif')->first(); @endphp
    <ion-card style="margin:0; text-align:center;">
        <ion-card-content style="padding:1.5rem;">
            <div style="width:60px; height:60px; border-radius:16px; background:{{ $sesi ? '#FFEBEE' : '#FFF3E0' }}; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                <ion-icon name="{{ $sesi ? 'people-outline' : 'grid-outline' }}" style="font-size:1.6rem; color:{{ $sesi ? '#F44336' : 'var(--orange)' }};"></ion-icon>
            </div>
            <h3 style="margin:0 0 0.5rem; font-size:1.2rem; font-weight:800; color:#2D3436;">{{ $m->nomor_meja }}</h3>
            
            @if($sesi)
                <span style="display:inline-flex; align-items:center; gap:4px; background:#FFEBEE; color:#D32F2F; padding:4px 12px; border-radius:20px; font-size:0.72rem; font-weight:700;">
                    <span style="width:6px; height:6px; border-radius:50%; background:#F44336; animation:pulse 2s infinite;"></span>
                    Sedang Dipakai
                </span>
            @else
                <span style="display:inline-flex; align-items:center; gap:4px; background:#E8F5E9; color:#388E3C; padding:4px 12px; border-radius:20px; font-size:0.72rem; font-weight:700;">
                    <ion-icon name="checkmark-circle" style="font-size:0.8rem;"></ion-icon>
                    Tersedia
                </span>
            @endif

            <div style="display:flex; gap:6px; margin-top:1rem;">
                <a href="/admin/meja/{{ $m->id }}/qr" style="flex:1; text-decoration:none;">
                    <ion-button expand="block" size="small" fill="outline" color="primary" style="--border-radius:10px; font-weight:700; font-size:0.7rem;">
                        <ion-icon name="qr-code-outline" slot="start"></ion-icon>
                        QR Code
                    </ion-button>
                </a>
                <form action="/admin/meja/{{ $m->id }}" method="POST" style="flex:1;" id="deleteMejaForm-{{ $m->id }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDeleteMeja({{ $m->id }})" style="background:transparent; border:1px solid #F44336; color:#F44336; border-radius:10px; width:100%; height:32px; font-weight:700; font-size:0.7rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:4px; transition:all 0.2s;">
                        <ion-icon name="trash-outline"></ion-icon> Hapus
                    </button>
                </form>
            </div>
        </ion-card-content>
    </ion-card>
    @endforeach
</div>

@endsection

@section('scripts')
<script>
function confirmDeleteMeja(id) {
    Swal.fire({
        title: 'Hapus Meja?',
        text: "Meja beserta QR Code-nya akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteMejaForm-' + id).submit();
        }
    });
}
</script>


@section('scripts')
<script>
// 1. Kumpulkan semua nomor meja dari database ke dalam Array JavaScript
const mejaTersedia = [
    @foreach($mejas as $m)
        "{{ strtolower(trim($m->nomor_meja)) }}",
    @endforeach
];

// 2. Validasi sebelum form dikirim
document.getElementById('formTambahMeja').addEventListener('submit', function(e) {
    e.preventDefault(); // Tahan pengiriman form ke Controller

    let inputAngka = document.getElementById('inputAngkaMeja').value.trim();
    if(!inputAngka) return;

    // Gabungkan teks "Meja " dengan angka yang diinput admin
    let finalNomorMeja = "Meja " + inputAngka;
    
    // Cek apakah meja tersebut sudah ada di array mejaTersedia
    if (mejaTersedia.includes(finalNomorMeja.toLowerCase())) {
        Swal.fire({
            title: 'Gagal Menambahkan!',
            text: finalNomorMeja + ' sudah terdaftar di sistem. Silakan masukkan nomor/angka lain.',
            icon: 'error',
            confirmButtonColor: '#F44336',
            confirmButtonText: 'Mengerti'
        });
        return false; // Hentikan eksekusi, form batal terkirim
    }

    // Jika belum ada, masukkan ke hidden input dan kirim secara otomatis
    document.getElementById('hiddenNomorMeja').value = finalNomorMeja;
    this.submit();
});

// Fungsi bawaan Anda untuk hapus meja (Jangan dihapus)
function confirmDeleteMeja(id) {
    Swal.fire({
        title: 'Hapus Meja?',
        text: "Meja beserta QR Code-nya akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteMejaForm-' + id).submit();
        }
    });
}
</script>
@endsection
