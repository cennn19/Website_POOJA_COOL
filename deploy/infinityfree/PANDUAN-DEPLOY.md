# Panduan Deploy ke InfinityFree

InfinityFree tidak menyediakan terminal (SSH), cron, atau akses database dari luar.
Karena itu, semua persiapan dilakukan di laptop oleh skrip build, lalu hasilnya di-upload.

| Kebutuhan | Cara di InfinityFree |
|---|---|
| `composer install` | sudah dijalankan oleh skrip build (folder `vendor` ikut di-upload) |
| `php artisan migrate --seed` | impor `database.sql` lewat phpMyAdmin |
| Cron (pembatalan otomatis 24 jam) | tidak perlu, berjalan otomatis saat ada pengunjung |
| `php artisan app:cek-keamanan` | menu admin **Cek Keamanan** |
| PHP | server InfinityFree memakai PHP 8.4 (sesuai kebutuhan aplikasi) |

---

## 1. Siapkan akun InfinityFree

1. Daftar di infinityfree.com, lalu **Create Account** dan pilih subdomain gratis
   (misal `poojacool.infinityfreeapp.com`) atau domain sendiri.
2. Di **Client Area → akun hosting**, catat:
   - **FTP**: hostname, username (`if0_...`), password (tombol *Show* pada *Account Password*).
   - **MySQL**: buka **MySQL Databases**, buat database baru (misal `pooja`), lalu catat
     *MySQL Hostname* (`sqlXXX.infinityfree.com`), *Database Name* (`if0_XXXX_pooja`),
     *Username* (`if0_XXXX`), dan *Password* (sama dengan password akun hosting).
3. **SSL**: buka **Free SSL Certificates**, lalu terbitkan sertifikat untuk domain Anda dan
   pasang sampai statusnya aktif. Langkah ini wajib agar website berjalan di `https://`.

## 2. Build paket di laptop

Dari Git Bash di root project:

```bash
APP_URL=https://poojacool.infinityfreeapp.com \
DB_HOST=sqlXXX.infinityfree.com DB_DATABASE=if0_XXXX_pooja \
DB_USERNAME=if0_XXXX DB_PASSWORD='password-hosting' \
bash deploy/infinityfree/build.sh
```

Isi dengan data dari langkah 1. Kalau dikosongkan, isi nanti di `deploy/build/upload/htdocs/laravel/.env`.

Skrip ini akan:
1. menjalankan semua tes, dan berhenti jika ada yang gagal;
2. melakukan `npm run build` dan `composer install --no-dev`;
3. membuat `.env` produksi dengan `APP_KEY` baru dan `APP_DEBUG=false`;
4. membuat `database.sql`, yang berisi semua tabel, 5 kategori, 16 layanan, dan 1 akun admin.

Hasilnya ada di `deploy/build/` (tidak ikut git):

```
deploy/build/
├── upload/htdocs/          ← upload ISI folder ini ke /htdocs di hosting
│   ├── index.php, .htaccess, build/, images/, ...
│   └── laravel/            ← aplikasi + .env (dikunci, tidak bisa dibuka dari browser)
├── database.sql            ← impor lewat phpMyAdmin
└── KREDENSIAL-ADMIN.txt    ← email & password admin awal
```

## 3. Impor database

1. Client Area → **MySQL Databases** → **phpMyAdmin** pada database Anda.
2. Tab **Import** → pilih `deploy/build/database.sql` → **Import**.
3. Pastikan 15 tabel muncul (`orders`, `services`, `users`, `sessions`, dan lainnya).

## 4. Upload file

1. Buka FileZilla, lalu sambungkan dengan data FTP dari langkah 1 (port 21).
2. Di sisi server, buka folder **`/htdocs`** dan hapus file bawaan InfinityFree di dalamnya (misal `index2.html`).
3. Di sisi laptop, buka `deploy/build/upload/htdocs/`, pilih **semua isinya, termasuk
   `.htaccess`** (aktifkan *Server → Force showing hidden files*), lalu seret ke `/htdocs`.
4. Jumlahnya sekitar 6.700 file, jadi tunggu sampai antrean kosong. Jika ada *Failed transfers*,
   klik kanan lalu pilih **Reset and requeue all**, kemudian proses ulang.

> Pastikan file `htdocs/.htaccess` dan `htdocs/laravel/.htaccess` ikut ter-upload.
> Kedua file inilah yang mengunci `.env` dan bukti bayar pelanggan.

## 5. Periksa setelah upload (WAJIB)

1. Buka `https://domain-anda/laravel/.env`. Hasilnya **harus 403 Forbidden**.
   Jika isi file tampil, segera hapus `htdocs/laravel/.env` lewat FTP dan periksa kembali langkah 4.3.
2. Buka `https://domain-anda/admin/login` dan login dengan data dari `KREDENSIAL-ADMIN.txt`.
3. Menu **Ganti Password**: ganti password, lalu hapus `KREDENSIAL-ADMIN.txt` dari laptop.
4. Menu **Cek Keamanan**: semua baris wajib harus **LOLOS**.
   - Jika muncul peringatan proxy, ubah `TRUSTED_PROXIES=*` di `htdocs/laravel/.env`,
     lalu muat ulang halaman dan pastikan "IP pengunjung menurut aplikasi" sama dengan IP Anda.
5. Menu **Layanan & Harga**: isi harga. Layanan berharga Rp0 tidak tampil di website.
6. Coba satu pesanan dari HP: pesan, unggah bukti, setujui di admin, lalu cek status.

## 6. Update di kemudian hari

1. Jalankan ulang `build.sh` dengan data yang sama.
2. Upload ulang **hanya** `htdocs/build/` dan `htdocs/laravel/{app,bootstrap,config,database,resources,routes,vendor}`.
3. **Jangan** menimpa `htdocs/laravel/.env` (APP_KEY berubah, sehingga semua sesi login putus)
   dan **jangan** menimpa `htdocs/laravel/storage/` (berisi bukti bayar pelanggan).
4. Jika ada migrasi database baru, jalankan SQL-nya lewat phpMyAdmin. Jangan mengimpor ulang
   `database.sql` karena seluruh data akan hilang.

## Masalah umum

| Gejala | Penyebab & solusi |
|---|---|
| Halaman putih / "500" | Data database di `.env` salah. Cocokkan dengan Client Area. |
| Tampilan tanpa CSS | Folder `htdocs/build/` belum ter-upload lengkap. |
| Semua link mengarah ke `http://` | `APP_URL` masih `http://`. Ubah ke `https://` setelah SSL aktif. |
| "419 Page Expired" saat login | Cookie diblokir karena SSL belum aktif sementara `APP_URL` sudah `https://`. Aktifkan SSL terlebih dahulu. |
| Pelanggan sering kena "429 Too Many Requests" | Lihat peringatan proxy di menu Cek Keamanan (langkah 5.4). |
