# Template Website Yayasan Pendidikan

Template website modern untuk yayasan pendidikan (TK, SD, SMP, SMA/SMK) — **PHP murni (native)** + **Tailwind CSS v4 via CDN**. Fitur lengkap: profil, unit pendidikan, berita + pagination, PPDB online dengan upload berkas, panel admin, dan form kontak. Siap pakai untuk client atau dijadikan starter template usaha jasa pembuatan web.

## Palet Warna

| Token | Warna | Kegunaan |
|---|---|---|
| `navy` | `#0b2545` | Header hero, footer, judul |
| `navy-soft` | `#13315c` | Lapisan kedua navy |
| `emerald` | `#10b981` | Aksen utama |
| `emerald-dark` | `#047857` | Tombol & teks aksen |
| `mist` | `#f4f8fb` | Latar section alternatif |

## Struktur File

```
files/
├── index.php            # Beranda: hero, statistik, unit, berita, testimoni & galeri
├── profil.php           # Sejarah, visi misi, struktur pengurus yayasan
├── unit.php             # Detail unit TK, SD, SMP, SMA/SMK
├── berita.php           # Daftar berita + pagination (?page=N, 6 artikel/halaman)
├── detail_berita.php    # Halaman baca artikel (?slug=...) + berita terkait + 404
├── ppdb.php             # Formulir pendaftaran siswa baru (multipart/form-data)
├── proses_ppdb.php      # Backend PPDB: validasi, simpan berkas & data, halaman sukses
├── kontak.php           # Info kontak, Google Maps embed, form pesan
├── admin_login.php      # Login pengurus (password hash + lock 5x salah)
├── admin_dashboard.php  # Dashboard: tabel pendaftar PPDB, detail, ubah status, kelola berita
├── header.php           # Navbar + hamburger menu (konsisten semua halaman)
├── footer.php           # Footer: tautan cepat, sosial media, copyright + JS menu
├── functions.php        # Lapisan penyimpanan, CSRF, flash, autentikasi, utilitas
├── data.php             # Data statis: nama yayasan, kontak, unit pendidikan
└── data/                # Penyimpanan JSON (terproteksi akses langsung)
    ├── ppdb.json        # Daftar pendaftar PPDB
    ├── berita.json      # Artikel berita
    ├── admin.json       # Akun admin (username + password hash)
    └── .htaccess        # Require all denied (server Apache)
└── uploads/berkas/      # Berkas unggahan PPDB (nama acak, eksekusi script diblokir)
```

## Alur Penyimpanan Data (penting)

Template ini **tidak butuh database** — semua data disimpan sebagai file JSON:

1. **PPDB**: `ppdb.php` POST multipart → `proses_ppdb.php` validasi server-side →
   berkas dipindah ke `uploads/berkas/<nama-acak>.<ext>` → data ditulis ke
   `data/ppdb.json` (dengan nomor urut `PPDB-YYYY-NNNN` + status `Baru`) →
   halaman sukses dengan nomor pendaftaran.
2. **Berita**: admin menambah/menghapus dari `admin_dashboard.php` → ditulis ke
   `data/berita.json` → `index.php` dan `berita.php` membaca file yang sama secara otomatis.
3. **Login admin**: `admin_login.php` memverifikasi `password_verify()` terhadap
   `data/admin.json`, lalu status login disimpan di **PHP session** (`$_SESSION['admin']`).
   Token CSRF + batas 5 percobaan gagal (kunci 5 menit) ikut aktif.
4. **Validasi form kontak/PPDB**: error disimpan di session (`flash`), redirect
   PRG (POST-Redirect-GET) supaya refresh tidak mengirim ulang data.

### Alternatif MySQL

Ganti pemanggilan `json_baca()`/`json_tulis()` dengan PDO — contoh kode `db()`
beserta query INSERT PPDB tersedia di komentar bagian bawah `functions.php`.
Tabel yang disarankan: `ppdb`, `berita`, `admin`.

## Cara Menjalankan

### XAMPP
1. Salin folder ini ke `C:\xampp\htdocs\yayasan`.
2. Start **Apache** di XAMPP Control Panel.
3. Buka `http://localhost/yayasan/index.php`.

### Laragon
- **Apache**: salin ke `C:\laragon\www\yayasan` → start Apache → buka `http://localhost/yayasan`.
- **nginx**: folder sama, tetapi `.htaccess` tidak berlaku — tambahkan blok ini di
  `C:\laragon\etc\nginx\sites-enabled\00-default.conf` lalu reload:
  ```nginx
  location ^~ /yayasan/data/ { deny all; }
  ```
  (blok ini sudah ditambahkan pada instalasi dev template)

### Tanpa server (PHP bawaan)
```bash
cd files
php -S localhost:8000
```

## Login Admin

- URL: `http://localhost/yayasan/admin_login.php`
- Default: **admin / admin123** — ganti di `data/admin.json` menggunakan
  `password_hash()` (lihat riwayat pembuatan) sebelum dipakai produksi.

## Kustomisasi Cepat

- **Nama & kontak yayasan, unit** → `data.php`
- **Berita, statistik, testimoni** → `index.php` (berita diambil dari `data/berita.json`)
- **Jadwal & alur PPDB** → `ppdb.php` (sidebar)
- **Fasilitas tiap unit** → `unit.php`
- **Google Maps** → `src` iframe di `kontak.php`

## Catatan Teknis

- Tailwind CSS v4 via `@tailwindcss/browser@4` (CDN) — tanpa build step; font Poppins.
- Keamanan: `htmlspecialchars()` pada semua output, token CSRF, password hash,
  validasi ekstensi + MIME saat upload, `.htaccess` blokir akses `data/` & eksekusi
  script di `uploads/` (Apache), plus aturan nginx untuk server nginx.
- Hamburger menu: JavaScript vanilla + `aria-expanded` (aksesibel).
- Gambar demo memakai `picsum.photos` — ganti dengan foto asli yayasan saat produksi.
