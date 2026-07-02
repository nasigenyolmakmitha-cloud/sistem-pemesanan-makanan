@extends('layouts.admin')
@section('page-title', 'Tambah Menu Baru')
@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
    <a href="/admin/menu" style="text-decoration:none;">
        <ion-button fill="clear" color="medium" style="--padding-start:8px; --padding-end:8px;">
            <ion-icon name="arrow-back-outline" style="font-size:1.3rem;"></ion-icon>
        </ion-button>
    </a>
    <div>
        <h2 style="margin:0; font-size:1.3rem; font-weight:800; color:#2D3436;">Tambah Menu Baru</h2>
        <p style="margin:0; color:#999; font-size:0.8rem; font-weight:600;">Isi data menu yang ingin ditambahkan</p>
    </div>
</div>

<div style="max-width:700px;">
    <ion-card style="margin:0;">
        <ion-card-content style="padding:1.5rem;">
            <form action="/admin/menu" method="POST" enctype="multipart/form-data" id="form-menu" novalidate>
                @csrf
                
                <div style="margin-bottom:2rem; text-align:center;">
                    <div id="fotoPreview" style="background:#f5f5f5; border-radius:12px; padding:2rem; margin-bottom:1rem; min-height:200px; display:flex; align-items:center; justify-content:center;">
                        <div style="text-align:center; color:#ccc;">
                            <ion-icon name="image-outline" style="font-size:4rem; display:block; margin-bottom:1rem;"></ion-icon>
                            <p style="margin:0; font-size:0.9rem;">Pilih Gambar</p>
                        </div>
                    </div>
                    <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png" required style="display:none;">
                    <button type="button" onclick="document.getElementById('fotoInput').click()" style="background:var(--orange); color:#fff; border:none; border-radius:12px; font-weight:700; font-size:0.9rem; padding:10px 24px; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                        Pilih Foto
                    </button>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-weight:700; font-size:0.85rem; color:#2D3436; margin-bottom:8px;">Nama Menu <span style="color:#F44336;">*</span></label>
                    <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="Nama Menu"
                        style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; transition:border 0.2s; box-sizing:border-box;"
                        onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block; font-weight:700; font-size:0.85rem; color:#2D3436; margin-bottom:8px;">Kategori <span style="color:#F44336;">*</span></label>
                        <select name="kategori" required style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; background:#fff; box-sizing:border-box; transition:border 0.2s; cursor:pointer;">
                            <option value="">Pilih Kategori</option>
                            <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="Tambahan" {{ old('kategori') == 'Tambahan' ? 'selected' : '' }}>Tambahan</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight:700; font-size:0.85rem; color:#2D3436; margin-bottom:8px;">Harga (Rp) <span style="color:#F44336;">*</span></label>
                        <input type="text" id="harga-display" required value="{{ old('harga') }}" placeholder="0" inputmode="numeric" oninput="formatRupiah(this)"
                            style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; transition:border 0.2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">
                        <input type="hidden" name="harga" id="harga-real" value="{{ old('harga') }}">
                    </div>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-weight:700; font-size:0.85rem; color:#2D3436; margin-bottom:8px;">Deskripsi Menu</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi Menu"
                        style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; resize:vertical; transition:border 0.2s; box-sizing:border-box;"
                        onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">{{ old('deskripsi') }}</textarea>
                </div>

                <div style="margin-bottom:2rem;">
                    <label style="display:block; font-weight:700; font-size:0.85rem; color:#2D3436; margin-bottom:8px;">Stok Menu</label>
                    <input type="number" name="stok" value="{{ old('stok', 0) }}" min="0" step="1" placeholder="0"
                        style="width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-size:0.9rem; font-family:inherit; outline:none; transition:border 0.2s; box-sizing:border-box;"
                        onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='#eee'">
                </div>

                <button type="submit" style="background:var(--orange); color:#fff; border:none; border-radius:12px; font-weight:700; font-size:1rem; height:50px; width:100%; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background 0.2s;">
                    <ion-icon name="save-outline"></ion-icon>
                    Simpan Menu
                </button>
            </form>
        </ion-card-content>
    </ion-card>
</div>

<script>
    // PREVIEW FOTO
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('fotoPreview');
                preview.innerHTML = '<img src="' + event.target.result + '" style="max-height:180px; max-width:100%; border-radius:8px;">';
            };
            reader.readAsDataURL(file);
        }
    });

    // LOGIKA FORMAT RUPIAH
    function formatRupiah(input) {
        // Hapus karakter selain angka
        let angka = input.value.replace(/[^0-9]/g, '');
        // Update input hidden untuk dikirim ke database
        document.getElementById('harga-real').value = angka;
        // Format dengan titik
        input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Eksekusi format rupiah saat halaman diload (jika ada nilai old)
    window.addEventListener('DOMContentLoaded', function() {
        let inputHarga = document.getElementById('harga-display');
        if (inputHarga.value) {
            formatRupiah(inputHarga);
        }
    });

    // LOGIKA VALIDASI FORM
    document.getElementById('form-menu').addEventListener('submit', function(e) {
        let foto = document.getElementById('fotoInput').files.length;
        let nama = document.querySelector('input[name="nama"]').value.trim();
        let kategori = document.querySelector('select[name="kategori"]').value;
        let harga = document.querySelector('input[name="harga"]').value.trim();

        if (foto === 0 || nama === '' || kategori === '' || harga === '') {
            e.preventDefault(); 
            
            Swal.fire({
                title: 'Data Belum Lengkap!',
                text: 'Pastikan Anda telah mengisi Foto, Nama Menu, Kategori, dan Harga.',
                icon: 'warning',
                confirmButtonColor: '#FF7043',
                confirmButtonText: 'Baik, lengkapi'
            });
        }
    });
</script>

@endsection