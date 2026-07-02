# PRD — Sistem Pemesanan Makanan QR Code Berbasis Web
## Nasi Be Genyol Mak Mitha

---

## 1. Ringkasan Proyek

| Item | Detail |
|------|--------|
| **Nama Sistem** | Sistem Pemesanan Makanan Menggunakan Kode QR Berbasis Web |
| **Studi Kasus** | Nasi Be Genyol Mak Mitha, Jl. Lembusora I No.7, Ubung Kaja, Denpasar Utara |
| **Stack Teknologi** | Laravel (PHP), MySQL, Blade Templating |
| **CSS Framework** | Bootstrap 5 (via CDN) |
| **Local Server** | Laragon (Apache + MySQL) |
| **Code Editor** | Visual Studio Code |
| **Metode Pengembangan** | Waterfall |
| **Pengujian** | Black Box Testing + System Usability Scale (SUS) |

---

## 2. Masalah yang Dipecahkan

| No | Masalah | Dampak |
|----|---------|--------|
| 1 | Antrean panjang di kasir saat jam ramai | Pelanggan tidak nyaman, potensi pembatalan pesanan |
| 2 | Percampuran antrean pesan dan antrean bayar di kasir | Alur operasional terganggu |
| 3 | Pelanggan berkelompok bayar terpisah (split bill) | Kasir harus verifikasi pesanan manual per orang |
| 4 | Kesalahan pencatatan pesanan (human error) | Kerugian pelanggan dan pihak usaha |

---

## 3. Aktor dan Hak Akses

### 3.1 Pelanggan (Unauthenticated)
- Tidak memerlukan login atau registrasi akun.
- Mengakses sistem **hanya** melalui scan QR Code di meja.
- Wajib memasukkan nama sebelum dapat melihat menu dan memesan.
- Dapat melakukan pemesanan berkali-kali (multiple round) selama sesi meja masih berstatus `aktif`.
- Tidak dapat mengakses halaman admin.

### 3.2 Admin / Kasir (Authenticated)
- Login menggunakan email dan password.
- Satu role saja: `admin`. Kasir dan admin adalah orang yang sama.
- Akun dibuat manual via database seeder, tidak ada registrasi publik.
- Memiliki akses penuh ke seluruh fitur admin: manajemen menu, manajemen meja, dashboard pesanan, dan riwayat pesanan.

---

## 4. Alur Sistem Utama

### 4.1 Alur Pelanggan

```
Pelanggan scan QR Code di meja
        │
        ▼
Sistem cek: apakah sesi aktif sudah ada untuk meja ini?
        │
   Tidak ada ──► Buat sesi baru otomatis (status: aktif)
        │
        ▼
Halaman Input Nama
Pelanggan masukkan nama → sistem buat record `pemesan`
        │
        ▼
Halaman Menu
Pelanggan pilih item → masuk keranjang (session/localStorage)
        │
        ▼
Halaman Keranjang
Review pesanan → tambah catatan opsional → klik "Kirim Pesanan"
        │
        ▼
Sistem simpan ke tabel `pesanan` dan `detail_pesanan`
Status pesanan otomatis: `menunggu`
        │
        ▼
Halaman Konfirmasi Pesanan
Tampilkan ringkasan pesanan milik pelanggan ini
Pelanggan bisa klik "Tambah Pesanan Lagi" (kembali ke menu)
```

### 4.2 Alur Kasir

```
Kasir login → Dashboard Pesanan
        │
        ▼
Lihat semua pesanan masuk, dikelompokkan per meja
        │
        ▼
Klik meja → Halaman Detail Sesi
Lihat daftar pemesan + pesanan masing-masing + subtotal per orang
        │
        ▼
Ubah status pesanan:
menunggu → diproses → selesai → dibayar
        │
        ▼
Setelah semua pemesan di meja berstatus dibayar:
Kasir klik "Tutup Sesi" → sesi berubah menjadi `selesai`
Meja siap digunakan untuk sesi berikutnya
```

---

## 5. Struktur Database

### Tabel: `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| name | varchar(100) | Nama kasir/admin |
| email | varchar(150) unique | |
| password | varchar(255) | Hash bcrypt |
| remember_token | varchar(100) nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### Tabel: `meja`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| nomor_meja | varchar(20) | Contoh: "Meja 1", "Meja 2" |
| qr_token | varchar(64) unique | Token permanen per meja, digunakan sebagai parameter URL QR Code |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Catatan:** QR Code bersifat **statis permanen** per meja. URL format: `/pesan/{qr_token}`. Sesi baru dibuat otomatis saat token di-akses dan tidak ada sesi aktif.

---

### Tabel: `sesi_pemesanan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| meja_id | bigint FK → meja.id | |
| kode_sesi | varchar(32) unique | UUID, di-generate otomatis saat sesi dibuat |
| status | enum('aktif','selesai') | Default: `aktif` |
| dibuka_pada | timestamp | Waktu sesi pertama kali dibuat |
| ditutup_pada | timestamp nullable | Diisi saat kasir tutup sesi manual |

---

### Tabel: `pemesan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| sesi_id | bigint FK → sesi_pemesanan.id | |
| nama | varchar(100) | Nama yang diinput pelanggan |
| created_at | timestamp | |

---

### Tabel: `menu`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| nama | varchar(150) | Nama menu |
| deskripsi | text nullable | Deskripsi singkat |
| harga | decimal(10,2) | |
| foto | varchar(255) nullable | Path relatif file foto, disimpan di `storage/app/public/menu` |
| kategori | varchar(50) | Contoh: "Makanan", "Minuman" |
| tersedia | tinyint(1) | 1 = tersedia, 0 = habis. Default: 1 |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### Tabel: `pesanan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| pemesan_id | bigint FK → pemesan.id | |
| sesi_id | bigint FK → sesi_pemesanan.id | |
| status | enum('menunggu','diproses','selesai','dibayar') | Default: `menunggu` |
| catatan | text nullable | Catatan dari pelanggan |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Catatan:** Satu pemesan bisa memiliki lebih dari satu record `pesanan` jika melakukan multiple round pemesanan.

---

### Tabel: `detail_pesanan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK auto increment | |
| pesanan_id | bigint FK → pesanan.id | |
| menu_id | bigint FK → menu.id | |
| jumlah | int | |
| harga_saat_pesan | decimal(10,2) | Snapshot harga saat pesanan dibuat, agar tidak berubah jika harga menu diedit |
| subtotal | decimal(10,2) | harga_saat_pesan × jumlah |

---

## 6. Daftar Halaman dan Fitur

### 6.1 Halaman Pelanggan

---

#### H-01: Halaman Input Nama
**URL:** `/pesan/{qr_token}`  
**Akses:** Publik (via scan QR)  
**Deskripsi:** Halaman pertama yang muncul setelah pelanggan scan QR Code.

**Logika:**
- Sistem validasi `qr_token` → cari meja yang sesuai.
- Jika token tidak valid → tampilkan halaman error "QR tidak dikenali".
- Cek apakah ada `sesi_pemesanan` dengan `meja_id` ini dan status `aktif`.
  - Jika **ada** → gunakan sesi tersebut.
  - Jika **tidak ada** → buat sesi baru otomatis, simpan `kode_sesi` ke session browser.
- Simpan `meja_id` dan `sesi_id` ke session browser.

**Fitur:**
- Form input satu field: Nama Pemesan (required, max 100 karakter).
- Tombol "Lihat Menu".
- Tampilkan nomor meja di halaman (contoh: "Anda di Meja 3").

**Validasi:**
- Nama tidak boleh kosong.
- Nama minimal 2 karakter.

---

#### H-02: Halaman Menu
**URL:** `/pesan/{qr_token}/menu`  
**Akses:** Publik, hanya bisa diakses setelah H-01  
**Deskripsi:** Menampilkan seluruh menu yang tersedia untuk dipilih pelanggan.

**Fitur:**
- Tampilkan nama pemesan dan nomor meja di bagian atas halaman.
- Filter menu berdasarkan kategori (tab atau tombol filter).
- Setiap card menu menampilkan: foto, nama, deskripsi singkat, harga, tombol "+ Tambah".
- Menu dengan `tersedia = 0` tetap ditampilkan tapi dengan label "Habis" dan tombol tambah dinonaktifkan.
- Icon keranjang di pojok kanan atas dengan badge jumlah item.
- Klik tombol "+ Tambah" → item masuk ke keranjang (disimpan di session).
- Jika item sudah di keranjang, tombol berubah menjadi kontrol jumlah (− jumlah +).

---

#### H-03: Halaman Keranjang
**URL:** `/pesan/{qr_token}/keranjang`  
**Akses:** Publik, hanya bisa diakses setelah H-01  
**Deskripsi:** Ringkasan pesanan sebelum dikonfirmasi dan dikirim.

**Fitur:**
- Daftar item yang dipilih: nama menu, jumlah, harga satuan, subtotal.
- Tombol ubah jumlah (− +) dan tombol hapus item per baris.
- Field catatan pesanan (textarea, opsional).
- Tampilkan total harga keseluruhan.
- Tampilkan nama pemesan dan nomor meja.
- Tombol "Kembali ke Menu".
- Tombol "Kirim Pesanan" (disabled jika keranjang kosong).
- Saat "Kirim Pesanan" diklik → sistem simpan ke tabel `pesanan` dan `detail_pesanan`, keranjang di-reset.

---

#### H-04: Halaman Konfirmasi Pesanan
**URL:** `/pesan/{qr_token}/konfirmasi`  
**Akses:** Publik, redirect otomatis setelah pesanan berhasil disimpan  
**Deskripsi:** Halaman sukses setelah pesanan berhasil dikirim ke kasir.

**Fitur:**
- Pesan sukses: "Pesanan kamu berhasil dikirim!"
- Ringkasan pesanan: nama pemesan, daftar item, total harga.
- Tombol "Tambah Pesanan Lagi" → kembali ke H-02 (menu), sesi dan nama tetap aktif.
- Informasi: "Tunjukkan halaman ini ke kasir untuk pembayaran" (opsional, sebagai panduan).

---

### 6.2 Halaman Admin / Kasir

---

#### H-05: Halaman Login
**URL:** `/admin/login`  
**Akses:** Publik  

**Fitur:**
- Form: email, password, tombol "Masuk".
- Redirect ke H-06 (Dashboard) jika berhasil.
- Tampilkan pesan error jika kredensial salah.
- Tidak ada fitur lupa password (di luar scope).

---

#### H-06: Dashboard Pesanan
**URL:** `/admin/dashboard`  
**Akses:** Authenticated  
**Deskripsi:** Halaman utama kasir. Menampilkan ringkasan seluruh pesanan aktif yang masuk, dikelompokkan per meja.

**Fitur:**
- Kartu per meja yang memiliki sesi aktif, menampilkan:
  - Nomor meja.
  - Jumlah pemesan dalam sesi aktif.
  - Jumlah total item dipesan.
  - Status ringkasan (contoh: "3 menunggu, 1 diproses").
  - Tombol "Lihat Detail" → menuju H-07.
- Meja yang tidak memiliki sesi aktif tidak ditampilkan di dashboard (atau ditampilkan dengan label "Kosong").
- Tombol refresh manual atau auto-refresh setiap 30 detik.
- Navbar dengan link ke: Dashboard, Manajemen Menu, Manajemen Meja, Riwayat Pesanan, Logout.

---

#### H-07: Halaman Detail Sesi Meja
**URL:** `/admin/sesi/{sesi_id}`  
**Akses:** Authenticated  
**Deskripsi:** Detail lengkap satu sesi meja. Menampilkan semua pemesan dan pesanan masing-masing. Ini adalah halaman utama untuk operasional split bill.

**Fitur:**
- Header: nomor meja, waktu sesi dibuka, status sesi.
- Daftar pemesan dalam sesi, diurutkan berdasarkan waktu registrasi.
- Untuk setiap pemesan, tampilkan:
  - Nama pemesan.
  - Daftar item pesanan beserta jumlah dan harga.
  - Subtotal pesanan milik pemesan ini (untuk keperluan split bill).
  - Status pesanan terakhir.
  - Dropdown atau tombol ubah status: `menunggu` → `diproses` → `selesai` → `dibayar`.
- Total keseluruhan sesi (semua pemesan digabung).
- Tombol "Tutup Sesi" (hanya aktif jika semua pesanan dalam sesi berstatus `dibayar`).
  - Saat diklik → status `sesi_pemesanan` berubah menjadi `selesai`, `ditutup_pada` diisi timestamp sekarang.
  - Konfirmasi dialog sebelum eksekusi.
- Tombol kembali ke Dashboard.

---

#### H-08: Halaman Manajemen Menu
**URL:** `/admin/menu`  
**Akses:** Authenticated  
**Deskripsi:** Kelola seluruh data menu makanan dan minuman.

**Fitur:**
- Tabel daftar menu: foto thumbnail, nama, kategori, harga, status ketersediaan, aksi.
- Tombol "Tambah Menu Baru" → buka form tambah (modal atau halaman terpisah).
- Tombol "Edit" per baris → buka form edit.
- Tombol "Hapus" per baris → konfirmasi dialog, lalu hapus (soft delete tidak diperlukan).
- Toggle ketersediaan per menu (switch atau tombol) → ubah field `tersedia` tanpa reload halaman (AJAX sederhana atau form POST).
- Filter tabel berdasarkan kategori.

**Form Tambah/Edit Menu:**
- Nama menu (required).
- Kategori (required, dropdown atau input teks bebas).
- Harga (required, numerik).
- Deskripsi (opsional, textarea).
- Upload foto (opsional, accept: jpg/png, max 2MB). Foto disimpan ke `storage/app/public/menu`.
- Checkbox "Tersedia".

---

#### H-09: Halaman Manajemen Meja
**URL:** `/admin/meja`  
**Akses:** Authenticated  
**Deskripsi:** Kelola data meja dan QR Code masing-masing meja.

**Fitur:**
- Tabel daftar meja: nomor meja, status sesi saat ini, aksi.
- Tombol "Tambah Meja" → form input nomor meja, sistem generate `qr_token` otomatis (UUID).
- Tombol "Lihat QR" per baris → tampilkan QR Code dalam modal. QR Code di-generate dari URL `/pesan/{qr_token}` menggunakan library `simplesoftwareio/simple-qrcode`.
- Tombol "Download QR" → download QR Code sebagai file PNG.
- Tombol "Hapus Meja" → hanya bisa dihapus jika tidak ada sesi aktif yang terkait.

---

#### H-10: Halaman Riwayat Pesanan
**URL:** `/admin/riwayat`  
**Akses:** Authenticated  
**Deskripsi:** Daftar semua sesi yang sudah ditutup. Berguna untuk rekap harian.

**Fitur:**
- Tabel daftar sesi selesai: nomor meja, waktu buka, waktu tutup, jumlah pemesan, total pendapatan sesi.
- Filter berdasarkan tanggal (date picker dari - sampai).
- Klik baris → tampilkan detail sesi (read-only, tampilan sama seperti H-07 tapi tanpa tombol aksi ubah status).
- Total pendapatan dari hasil filter yang ditampilkan.

---

## 7. Routing Ringkasan

### Route Pelanggan (Publik)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/pesan/{qr_token}` | Tampilkan halaman input nama, buat sesi jika belum ada |
| POST | `/pesan/{qr_token}/nama` | Simpan nama pemesan ke session, redirect ke menu |
| GET | `/pesan/{qr_token}/menu` | Tampilkan halaman menu |
| GET | `/pesan/{qr_token}/keranjang` | Tampilkan halaman keranjang |
| POST | `/pesan/{qr_token}/keranjang/tambah` | Tambah item ke keranjang (session) |
| POST | `/pesan/{qr_token}/keranjang/update` | Update jumlah item di keranjang |
| POST | `/pesan/{qr_token}/keranjang/hapus` | Hapus item dari keranjang |
| POST | `/pesan/{qr_token}/pesan` | Submit pesanan → simpan ke DB |
| GET | `/pesan/{qr_token}/konfirmasi` | Tampilkan halaman konfirmasi sukses |

### Route Admin (Authenticated, prefix `/admin`, middleware `auth`)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/admin/login` | Tampilkan form login |
| POST | `/admin/login` | Proses login |
| POST | `/admin/logout` | Logout |
| GET | `/admin/dashboard` | Dashboard pesanan aktif |
| GET | `/admin/sesi/{sesi_id}` | Detail sesi meja |
| POST | `/admin/sesi/{sesi_id}/tutup` | Tutup sesi |
| POST | `/admin/pesanan/{pesanan_id}/status` | Update status pesanan |
| GET | `/admin/menu` | Daftar menu |
| GET | `/admin/menu/create` | Form tambah menu |
| POST | `/admin/menu` | Simpan menu baru |
| GET | `/admin/menu/{id}/edit` | Form edit menu |
| PUT | `/admin/menu/{id}` | Update menu |
| DELETE | `/admin/menu/{id}` | Hapus menu |
| POST | `/admin/menu/{id}/toggle` | Toggle ketersediaan menu |
| GET | `/admin/meja` | Daftar meja |
| POST | `/admin/meja` | Tambah meja baru |
| DELETE | `/admin/meja/{id}` | Hapus meja |
| GET | `/admin/meja/{id}/qr` | Tampilkan/download QR Code |
| GET | `/admin/riwayat` | Riwayat sesi selesai |
| GET | `/admin/riwayat/{sesi_id}` | Detail riwayat sesi |

---

## 8. Aturan Bisnis (Business Rules)

| No | Aturan |
|----|--------|
| BR-01 | Satu meja hanya boleh memiliki **satu sesi aktif** dalam satu waktu. |
| BR-02 | Sesi baru dibuat **otomatis** saat pelanggan scan QR dan tidak ada sesi aktif. Kasir tidak perlu membuka sesi manual. |
| BR-03 | Sesi hanya bisa **ditutup oleh kasir**, bukan oleh pelanggan. |
| BR-04 | Kasir hanya bisa menutup sesi jika **semua pesanan** dalam sesi berstatus `dibayar`. |
| BR-05 | Pelanggan bisa melakukan **multiple round** pemesanan selama sesi meja masih `aktif`. Setiap round menghasilkan record `pesanan` baru. |
| BR-06 | Nama pemesan disimpan dalam session browser. Jika session habis/browser ditutup, pelanggan akan diminta input nama lagi saat mengakses QR. |
| BR-07 | Field `harga_saat_pesan` di `detail_pesanan` adalah snapshot, tidak berubah meski harga menu diedit kemudian. |
| BR-08 | Menu yang dihapus tidak akan menghilangkan data `detail_pesanan` yang sudah ada (gunakan `onDelete('set null')` atau pertahankan foreign key tanpa cascade). |
| BR-09 | Meja tidak bisa dihapus jika masih memiliki sesi aktif. |
| BR-10 | Status pesanan hanya bisa maju ke depan: `menunggu` → `diproses` → `selesai` → `dibayar`. Tidak bisa mundur. |

---

## 9. Struktur Direktori Laravel (Rekomendasi)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Pelanggan/
│   │   │   ├── PemesananController.php
│   │   │   └── KeranjangController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── SesiController.php
│   │       ├── PesananController.php
│   │       ├── MenuController.php
│   │       ├── MejaController.php
│   │       └── RiwayatController.php
│   └── Middleware/
│       └── Authenticate.php (bawaan Laravel)
├── Models/
│   ├── User.php
│   ├── Meja.php
│   ├── SesiPemesanan.php
│   ├── Pemesan.php
│   ├── Menu.php
│   ├── Pesanan.php
│   └── DetailPesanan.php

resources/views/
├── pelanggan/
│   ├── input-nama.blade.php
│   ├── menu.blade.php
│   ├── keranjang.blade.php
│   └── konfirmasi.blade.php
├── admin/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   ├── dashboard.blade.php
│   ├── sesi/
│   │   └── detail.blade.php
│   ├── menu/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── meja/
│   │   └── index.blade.php
│   └── riwayat/
│       ├── index.blade.php
│       └── detail.blade.php
```

---

## 10. Library Pihak Ketiga

| Library | Kegunaan | Instalasi |
|---------|----------|-----------|
| `simplesoftwareio/simple-qrcode` | Generate QR Code dari URL meja | `composer require simplesoftwareio/simple-qrcode` |
| Bootstrap 5 | CSS framework untuk tampilan | Via CDN di layout Blade |
| Bootstrap Icons | Ikon UI | Via CDN di layout Blade |

---

## 11. Seeder

Buat seeder berikut untuk data awal pengembangan:

- **UserSeeder:** 1 akun admin. Email: `admin@nasibegenyo.com`, Password: `password`.
- **MejaSeeder:** 5 data meja (Meja 1 sampai Meja 5), masing-masing dengan `qr_token` UUID unik.
- **MenuSeeder:** Minimal 8 data menu dengan variasi kategori Makanan dan Minuman.

---

## 12. Catatan untuk Pengembangan

1. Gunakan **Laravel Session** untuk menyimpan data sementara pelanggan: `sesi_id`, `meja_id`, `pemesan_id`, dan isi keranjang.
2. Gunakan `php artisan storage:link` untuk menghubungkan `storage/app/public` ke `public/storage` agar foto menu bisa diakses via URL.
3. Middleware `auth` Laravel bawaan sudah cukup untuk proteksi route admin. Tidak perlu package tambahan seperti Spatie.
4. Tidak perlu implementasi WebSocket/real-time. Refresh manual atau meta refresh setiap 30 detik di dashboard sudah cukup sesuai scope penelitian.
5. Validasi input menggunakan Laravel Form Request untuk Controller admin, dan validasi inline di Controller pelanggan.
