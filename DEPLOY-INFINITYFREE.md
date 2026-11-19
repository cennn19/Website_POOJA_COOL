# Deploy Laravel ke InfinityFree

Panduan deploy website pemesanan servis AC Pooja Cool (Laravel + MySQL) ke hosting gratis InfinityFree.

Batasan InfinityFree yang memengaruhi langkah di bawah:
- Tidak ada SSH. `composer`, `npm`, dan `php artisan` hanya bisa dijalankan di laptop.
- Tidak ada cron. Laravel scheduler tidak jalan otomatis.
- Tidak bisa membuat symlink. `php artisan storage:link` tidak bisa dipakai.
- Database tidak bisa diakses dari luar hosting. Isi database diimpor lewat phpMyAdmin.
- Ada sistem keamanan anti-bot. Request dari luar browser (cron-job.org, curl) biasanya diblokir.
- Semua file **wajib** ada di dalam `htdocs`. File yang diupload ke luar `htdocs` dihapus otomatis.
- File PHP lebih dari 1 MB dibuang diam-diam saat upload.
- Upload pakai FileZilla. Fitur extract zip di File Manager web InfinityFree sering bermasalah.

---

## Bagian A — Disiapkan agent di project lokal

> Bagian ini bisa langsung diberikan ke agent. Kerjakan di branch atau salinan terpisah. Jangan ubah perilaku aplikasi saat dijalankan lokal.

### A1. Upload file tanpa symlink
- Ubah disk `public` di `config/filesystems.php`:
  - `root` → `public_path('storage')`
  - `url` → `env('APP_URL').'/storage'`
- Pastikan semua upload (bukti bayar, dll.) memakai disk `public`, sehingga file tersimpan langsung di folder publik tanpa `storage:link`.
- Tambahkan `public/storage/.gitignore` agar folder ada tapi isinya tidak ikut ke Git.

### A2. Pengganti scheduler
- Pindahkan logika pembatalan pesanan kedaluwarsa (24 jam tanpa bukti bayar) ke satu service, misalnya `ExpireUnpaidOrders`.
- Panggil service itu:
  - dari scheduled command, untuk saat ada cron (lokal / hosting lain), **dan**
  - setiap kali admin membuka halaman daftar pesanan atau dashboard, **dan**
  - setiap kali pelanggan membuka halaman Cek Status untuk pesanan tersebut.
- Pastikan pemanggilan ringan (satu query update) supaya tidak menambah waktu muat.

### A3. Konfigurasi tanpa queue & cron
- Semua yang memakai queue harus tetap jalan dengan `QUEUE_CONNECTION=sync`.
- `SESSION_DRIVER` dan `CACHE_STORE` boleh `database` atau `file`. Jika `database`, pastikan migrasinya ada.

### A4. Buat `.env.production.example`
Isi dengan kunci yang dibutuhkan di hosting (tanpa nilai rahasia):
```
APP_NAME="Pooja Cool"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://NAMA-SITUS.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_LEVEL=error
```

Laporkan file yang berubah. Jangan menjalankan `config:cache` atau `route:cache`.

---

## Bagian B — Dikerjakan sendiri

### B1. Buat akun & situs
1. Daftar di infinityfree.com, lalu **Create Account**.
2. Pilih subdomain gratis, misalnya `poojacool.infinityfreeapp.com`.
3. Di **Control Panel → MySQL Databases**, buat database. Catat:
   - **MySQL Host** (contoh: `sql123.infinityfree.com`)
   - **Database name** (contoh: `if0_12345678_poojacool`)
   - **Username** (contoh: `if0_12345678`)
   - **Password**: password akun hosting (lihat di halaman **Account → Show password**)
4. SSL tidak perlu diaktifkan. Subdomain gratis InfinityFree sudah otomatis memakai HTTPS. Jika HTTPS belum jalan di hari pertama, tunggu beberapa jam.

### B2. Siapkan build di laptop
Salin dulu project ke folder terpisah, misalnya `../deploy-poojacool`, lalu jalankan perintah di bawah **di folder salinan itu**. Jangan jalankan di folder project utama, karena `--no-dev` menghapus paket development yang dibutuhkan untuk kerja lokal.
```bash
composer install --no-dev
composer dump-autoload --no-dev --optimize=false
npm run build
php artisan optimize:clear
```
- **Jangan pakai `--optimize-autoloader`.** InfinityFree diam-diam membuang file PHP yang ukurannya lebih dari 1 MB, dan autoloader versi optimize bisa melewati batas itu. Gejalanya: error "Class not found" padahal file-nya ada.
- Pastikan folder `bootstrap/cache/` hanya berisi `.gitignore` (tidak ada `config.php`, `routes-*.php`, `services.php`, `packages.php`), dan folder `storage/framework/views/` kosong. File cache dari laptop berisi path Windows dan akan membuat hosting error.

### B3. Siapkan database
1. Di laptop, pastikan database lokal sudah berisi tabel lengkap + data awal (layanan, akun admin): `php artisan migrate:fresh --seed`.
2. Ekspor ke `.sql` lewat phpMyAdmin / HeidiSQL / Laragon.
3. Di InfinityFree: **Control Panel → phpMyAdmin** → pilih database → **Import** → upload file `.sql`.

### B4. Susun folder untuk diupload
Struktur di hosting (folder `htdocs` adalah root web):
```
htdocs/
├── index.php        ← dari public/, sudah diedit (lihat B5)
├── .htaccess        ← dari public/
├── build/           ← dari public/build (hasil npm run build)
├── images/          ← dari public/images (termasuk qris.jpeg)
├── storage/         ← folder upload (dari langkah A1), boleh kosong
├── favicon.ico, robots.txt, dll. dari public/
└── core/            ← SEMUA isi project selain folder public/
    ├── .htaccess    ← baru, isinya di bawah
    ├── .env         ← versi production
    ├── app/  bootstrap/  config/  database/
    ├── resources/  routes/  storage/  vendor/
    └── artisan, composer.json, dll.
```

Isi `htdocs/core/.htaccess` (**wajib**, supaya `.env` dan source code tidak bisa dibuka lewat browser):
```apache
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Deny from all
</IfModule>
```

### B5. Edit `htdocs/index.php`
Ganti setiap `__DIR__.'/../` menjadi `__DIR__.'/core/`. Biasanya ada di 3 baris:
```php
if (file_exists($maintenance = __DIR__.'/core/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/core/vendor/autoload.php';

$app = require_once __DIR__.'/core/bootstrap/app.php';
```
Lalu, **tepat setelah** baris `$app = require_once ...`, tambahkan:
```php
$app->usePublicPath(__DIR__);
```
Baris ini memberi tahu Laravel bahwa folder publiknya adalah `htdocs`, bukan `core/public`. Tanpa baris ini, upload bukti bayar dan file hasil `npm run build` tidak akan ketemu.

### B6. Isi `.env` di `core/`
Salin dari `.env.production.example`, lalu isi:
- `APP_KEY`: salin dari `.env` lokal
- `APP_URL`: alamat situs, pakai `https://`
- `DB_*`: dari langkah B1
- `APP_DEBUG=false` (jangan `true`, karena error bisa menampilkan password database)

### B7. Upload
- Pakai **FileZilla**. Data FTP ada di **Control Panel → FTP Details** (host, username, password, port 21).
- Hapus file bawaan `htdocs/index2.html` jika ada.
- Upload sesuai struktur B4. Folder `vendor/` berisi ribuan file, jadi upload pertama bisa makan waktu lama. Biarkan sampai selesai dan cek tidak ada file yang gagal (tab **Failed transfers** di FileZilla).

### B8. Cek setelah online
- [ ] Halaman utama terbuka dengan CSS dan gambar normal
- [ ] `https://SITUSMU/core/.env` menampilkan **403 Forbidden** (bukan isi file)
- [ ] Form pemesanan bisa disubmit dan tersimpan
- [ ] Gambar QRIS tampil di halaman pembayaran
- [ ] Upload bukti bayar berhasil dan gambarnya bisa dilihat admin
- [ ] Login admin, setujui, tolak, selesai berjalan
- [ ] Cek Status menampilkan data tersamar

### Kalau error
- **Halaman putih / 500:** sementara set `APP_DEBUG=true`, buka halaman, baca errornya, lalu kembalikan ke `false`. Bisa juga cek `core/storage/logs/laravel.log`.
- **"Vite manifest not found":** folder `build/` belum terupload ke `htdocs/build`, atau baris `usePublicPath` belum ditambahkan.
- **"Connection refused" database:** `DB_HOST` masih `127.0.0.1`. Ganti dengan host dari InfinityFree.
- **"Permission denied" di storage:** pastikan folder `core/storage` dan `core/bootstrap/cache` ikut terupload lengkap.

### Setiap update berikutnya
Cukup upload file yang berubah. Kalau ada perubahan `composer.json`, ulangi B2 dan upload ulang `vendor/`. Kalau ada migrasi baru, jalankan SQL-nya lewat phpMyAdmin (ambil dari ekspor database lokal atau `php artisan migrate --pretend`).