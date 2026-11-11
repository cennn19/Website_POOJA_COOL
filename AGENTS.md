# AGENTS.md

Panduan untuk AI coding agent (dan developer) yang mengerjakan proyek ini. Baca seluruh file ini sebelum mengubah kode. Untuk aturan tampilan, baca juga `DESIGN.md`.

## 1. Ringkasan Proyek

Website jasa **instalasi, maintenance, dan perbaikan HVACR dan AC rumahan**. Terdiri dari:

1. **Company profile** (Home, About, Contact)
2. **Order**: pelanggan memilih paket/layanan satuan, total dihitung otomatis, lalu mengisi data diri
3. **Pembayaran**: QRIS statis + upload bukti bayar
4. **Admin**: melihat bukti bayar, approve atau tolak

Pelanggan **tidak perlu login**. Hanya admin yang login.

## 2. Tech Stack

| Bagian | Pilihan |
|---|---|
| Backend | Laravel (versi stabil terbaru yang didukung PHP terpasang) |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS |
| Interaktivitas | Alpine.js (kalkulasi harga di sisi browser) |
| Auth admin | Laravel Breeze (Blade) |
| Panel admin | Filament (opsional) atau Blade manual |
| Build | Vite |
| Testing | Pest atau PHPUnit |

Jangan menambah framework JS besar (React/Vue/SPA) tanpa persetujuan. Proyek ini sengaja dibuat sederhana.

## 3. Perintah Umum

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link        # hanya untuk aset publik, bukan bukti bayar
npm run dev                     # development
npm run build                   # production
php artisan serve
php artisan test
./vendor/bin/pint               # format kode (wajib sebelum selesai)
```

## 4. Struktur Folder (target)

```
app/
  Http/
    Controllers/
      HomeController.php
      OrderController.php
      PaymentController.php
      Admin/OrderController.php
    Requests/                   # Form Request untuk semua validasi
  Models/                       # Service, Order, OrderItem, Payment, User
  Services/
    OrderPriceCalculator.php    # satu-satunya sumber hitung harga
  Enums/
    OrderStatus.php
    PaymentStatus.php
    ServiceType.php
database/
  migrations/
  seeders/
resources/views/
  layouts/
  components/                   # komponen Blade reusable
  pages/                        # home, about, contact
  order/
  payment/
  admin/
routes/web.php
tests/Feature/
```

## 5. Skema Database

- `services`: id, name, slug, type (`package`|`unit`), price (integer, Rupiah), description, is_active, sort_order
- `orders`: id, code (unik, acak), name, phone, address, schedule_date, notes, subtotal, unique_code, total, status, timestamps
- `order_items`: id, order_id, service_id, service_name, qty, price_at_order, subtotal
- `payments`: id, order_id, proof_path, status, note, verified_by (nullable, FK users), verified_at, timestamps
- `users`: akun admin

Aturan:

- Semua uang disimpan sebagai **integer Rupiah**. Jangan pakai float/decimal untuk harga.
- `order_items` menyimpan **snapshot** `service_name` dan `price_at_order`, supaya riwayat tidak berubah saat harga layanan diedit.
- Gunakan foreign key dengan constraint dan index pada `orders.code`, `orders.status`, `payments.order_id`.

## 6. Status dan Alur Order

```
menunggu_pembayaran → menunggu_verifikasi → disetujui → selesai
                                          ↘ ditolak (pelanggan boleh upload ulang)
```

- Implementasikan sebagai **PHP Enum** (`OrderStatus`). Jangan pakai string mentah tersebar di kode.
- Transisi status hanya lewat method di model/service, bukan `update(['status' => ...])` sembarangan.
- Upload bukti bayar mengubah status ke `menunggu_verifikasi`.
- Hanya admin yang boleh `disetujui`, `ditolak`, dan `selesai`.

## 7. Aturan Bisnis Penting

1. **Harga tidak boleh dipercaya dari browser.** Kalkulasi di Alpine.js hanya untuk tampilan. Saat submit, server menghitung ulang total dari tabel `services` lewat `OrderPriceCalculator`. Abaikan semua nilai harga/total dari request.
2. Validasi bahwa `service_id` ada dan `is_active = true`, `qty` integer minimal 1 dan ada batas maksimal.
3. **Kode unik pembayaran**: tambahkan angka kecil acak (misal 1 sampai 999) ke total agar admin mudah mencocokkan mutasi QRIS. Simpan di `orders.unique_code` dan tampilkan rincian jelas ke pelanggan.
4. **Kode order** harus acak dan tidak bisa ditebak (contoh `HVC-` + 8 karakter acak, atau ULID). Jangan gunakan ID auto-increment di URL publik.
5. Satu order hanya boleh punya satu bukti bayar aktif. Upload ulang hanya jika status `ditolak` atau `menunggu_pembayaran`.

## 8. Keamanan (Wajib)

- **Upload bukti bayar**
  - Validasi: `required|image|mimes:jpg,jpeg,png,webp|max:2048`
  - Simpan di disk **private** (`storage/app/private`), bukan `public`
  - Nama file di-generate acak (hash), jangan pakai nama asli
  - Admin melihat gambar lewat route yang dilindungi middleware `auth` (stream file dari controller)
- **Route admin** semua di bawah prefix `/admin` dengan middleware `auth` dan pengecekan role admin.
- **Rate limiting** pada `POST /order` dan upload bukti (contoh `throttle:10,1`).
- Selalu gunakan Form Request, CSRF aktif, dan Eloquent/query builder (tanpa raw SQL dari input user).
- Escape output Blade dengan `{{ }}`. Hindari `{!! !!}` kecuali konten statis tepercaya.
- Jangan commit `.env`, kredensial, atau file bukti bayar.
- Kredensial admin awal dibuat lewat seeder dari variabel `.env`, bukan hardcode.
- Halaman publik order/pembayaran tidak boleh membocorkan data order lain.

## 9. Routes (target)

| Method | URL | Keterangan |
|---|---|---|
| GET | `/` | Home |
| GET | `/about` | Tentang |
| GET | `/contact` | Kontak |
| GET | `/order` | Pilih layanan dan isi data |
| POST | `/order` | Simpan order, redirect ke pembayaran |
| GET | `/pembayaran/{code}` | QRIS dan form upload |
| POST | `/pembayaran/{code}` | Upload bukti |
| GET | `/status/{code}` | Cek status order |
| GET | `/admin/orders` | Daftar order (auth) |
| GET | `/admin/orders/{order}` | Detail dan bukti (auth) |
| POST | `/admin/orders/{order}/approve` | Setujui (auth) |
| POST | `/admin/orders/{order}/reject` | Tolak dengan catatan (auth) |
| GET | `/admin/payments/{payment}/proof` | Stream gambar bukti (auth) |

## 10. Konvensi Kode

- Ikuti PSR-12 dan jalankan `./vendor/bin/pint` sebelum commit.
- Controller tipis. Logika bisnis di `app/Services`. Validasi di Form Request.
- Gunakan Enum, Eloquent relationships, dan eager loading (`with()`) untuk mencegah N+1.
- Nama class, method, variabel, kolom DB, dan route dalam **bahasa Inggris**, kecuali URL publik yang ramah pengguna Indonesia (`/pembayaran`, `/status`).
- **Teks yang tampil ke pengguna dalam Bahasa Indonesia.** Simpan di file bahasa (`lang/id`) bila bisa, bukan tersebar di view.
- Set `APP_LOCALE=id`, `APP_TIMEZONE=Asia/Jakarta`.
- Format Rupiah lewat satu helper, contoh `Rp 1.250.000`.
- Komponen Blade untuk elemen berulang (button, card, input, badge status).
- Commit kecil dengan pesan jelas (Conventional Commits disarankan: `feat:`, `fix:`, `chore:`).

## 11. Testing Minimum

Tulis feature test untuk:

- Hitung total di server benar dan mengabaikan harga dari request
- Order gagal jika layanan nonaktif atau qty tidak valid
- Pelanggan tidak bisa mengakses bukti bayar tanpa login admin
- Upload menolak file non-gambar dan file di atas 2 MB
- Admin bisa approve dan reject, pelanggan tidak bisa
- Transisi status tidak valid ditolak

## 12. Definition of Done

Sebuah tugas dianggap selesai jika:

- [ ] Berjalan tanpa error di lokal
- [ ] Validasi dan otorisasi sudah ada
- [ ] Test terkait lulus (`php artisan test`)
- [ ] `pint` sudah dijalankan
- [ ] Tampilan sesuai `DESIGN.md` dan responsif di layar mobile
- [ ] Tidak ada secret atau file sensitif ikut ter-commit

## 13. Hal yang Jangan Dilakukan

- Jangan percaya harga, total, atau status dari input client.
- Jangan simpan bukti bayar di folder `public`.
- Jangan ubah struktur database yang sudah dimigrasi dengan mengedit migration lama. Buat migration baru.
- Jangan menghapus data order secara permanen. Gunakan status atau soft delete.
- Jangan menambah dependency berat tanpa alasan jelas.
- Jangan membuat fitur di luar scope (payment gateway otomatis, akun pelanggan, dll.) tanpa diminta.

## 14. Di Luar Scope (Saat Ini)

Payment gateway otomatis, akun/login pelanggan, aplikasi teknisi, chat realtime, multi-cabang. Bisa dipertimbangkan di fase berikutnya.

## 15. Placeholder yang Perlu Diisi Pemilik Bisnis

Ganti sebelum rilis: nama usaha, logo, nomor WhatsApp, alamat, jam operasional, area layanan, gambar QRIS, daftar layanan dan harga awal (seeder), foto galeri dan testimoni.