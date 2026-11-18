# Perbaikan Alur Pemesanan

> Kerjakan berurutan. Jangan ubah hal di luar daftar ini.
> Setelah selesai, laporkan file yang berubah dan migrasi yang ditambahkan.
> Poin 5, 6, 7 dari review sebelumnya (notifikasi, error 500 layanan nonaktif, sensor data Cek Status) **sudah selesai**, jangan disentuh lagi.

---

## 1. Kode unik harus benar-benar unik

**Masalah:** kode unik diambil acak 1–999 tanpa dicek (`app/Services/OrderPriceCalculator.php`). Dua pesanan bisa punya total transfer yang sama.

**Perbaikan:**
- Saat membuat kode unik, ambil semua kode yang sedang dipakai pesanan **aktif** (status selain `selesai` dan `dibatalkan`).
- Pilih acak dari angka 1–999 yang **belum** dipakai.
- Kalau semua angka terpakai (sangat jarang), tolak pesanan dengan pesan yang jelas, jangan error 500.
- Lindungi dari race condition: bungkus pembuatan pesanan dalam transaksi database + cek ulang sebelum simpan (atau unique index parsial jika database mendukung).

**Tes:**
- Dua pesanan aktif tidak pernah punya kode unik yang sama.
- Kode dari pesanan yang sudah `selesai`/`dibatalkan` boleh dipakai ulang.

---

## 2. Status Dibatalkan & kedaluwarsa

**Masalah:** pesanan yang tidak dibayar tetap "Menunggu Pembayaran" selamanya.

**Perbaikan:**
- Tambah status `dibatalkan`, plus kolom `alasan_batal` (nullable) dan `dibatalkan_pada` (timestamp nullable).
- **Otomatis:** pesanan `menunggu_pembayaran` yang lebih dari **24 jam** tanpa bukti bayar → `dibatalkan` dengan alasan "Kedaluwarsa: tidak dibayar dalam 24 jam". Jalankan lewat scheduled command (Laravel scheduler, tiap jam).
- **Manual oleh admin:** tombol **Batalkan** di detail pesanan, wajib isi alasan. Hanya untuk pesanan yang belum `selesai`.
- Konfirmasi pembatalan dibuat di dalam halaman (modal), bukan `confirm()` bawaan browser.
- Halaman Cek Status pelanggan menampilkan status Dibatalkan beserta alasannya.
- Badge status di admin: Dibatalkan = abu-abu/merah sesuai token di `DESIGNUIUX-REVISI.md`.
- Filter daftar pesanan admin: default **menyembunyikan** yang dibatalkan, ada opsi untuk menampilkannya.

**Tes:**
- Pesanan 25 jam tanpa bayar ikut dibatalkan command; pesanan 23 jam tidak.
- Pesanan yang sudah upload bukti bayar **tidak** ikut kedaluwarsa.
- Pesanan `selesai` tidak bisa dibatalkan.

---

## 3. Jadwal: sesi, kuota, dan ubah jadwal

**Masalah:** tanggal bebas dipilih tanpa cek kapasitas, tidak ada pilihan jam, admin tidak bisa mengubah jadwal, dan pesanan untuk hari ini masih bisa dibuat malam hari.

**Perbaikan:**
- Form order: setelah pilih tanggal, pelanggan memilih **sesi**:
  - Pagi (08.00–12.00)
  - Siang (13.00–17.00)
- Simpan di kolom `sesi` (`pagi` / `siang`).
- **Kuota per sesi:** ambil dari config `config/booking.php` → `kuota_per_sesi` (default **3**). Hitung pesanan aktif (bukan `dibatalkan`) di tanggal + sesi yang sama. Sesi penuh tampil nonaktif dengan label "Penuh".
- **Batas waktu pesan:**
  - Sesi Pagi hari ini tidak bisa dipilih.
  - Sesi Siang hari ini hanya bisa dipilih sebelum pukul **10.00**.
  - Tanggal yang sudah lewat tidak bisa dipilih.
  - Maksimal 30 hari ke depan.
- Validasi yang sama wajib dilakukan **di backend** (`StoreOrderRequest`), bukan hanya di form.
- **Admin ubah jadwal:** tombol **Ubah Jadwal** di detail pesanan → pilih tanggal + sesi baru (kuota tetap dicek, admin boleh override dengan centang "Paksa meski penuh"). Catat jadwal lama di riwayat/log pesanan.

**Tes:**
- Sesi dengan 3 pesanan aktif menolak pesanan ke-4.
- Pesanan dibatalkan tidak menghitung kuota.
- Pesan sesi Siang hari ini pukul 10.30 ditolak backend.

---

## 4. Harga layanan Perbaikan

**Masalah:** Perbaikan dibayar di muka dengan harga pasti, padahal kerusakan baru ketahuan setelah diperiksa.

**Perbaikan (tanpa ubah alur pembayaran):**
- Tambah kolom boolean `harga_mulai` di tabel layanan. Set `true` untuk Perbaikan.
- Jika `harga_mulai = true`, tampilkan harga sebagai **"Mulai dari Rp …"** di landing page, form order, dan ringkasan pembayaran.
- Tampilkan catatan di bawahnya:
  > Harga di atas adalah biaya pengecekan dan jasa perbaikan dasar. Penggantian sparepart (kapasitor, freon, kompresor, dll.) dibayar terpisah di lokasi setelah dicek teknisi dan disetujui pelanggan.
- Admin bisa mengatur `harga_mulai` di halaman Layanan & Harga.
- Hapus/ubah teks di website yang menjanjikan "pekerjaan atas persetujuan Anda" jika tidak sesuai dengan catatan di atas.

---

## 5. Data contoh yang tampil sebagai fakta

**Masalah:** statistik, testimoni, kontak, rekening, alamat, dan QRIS masih data karangan.

**Perbaikan:**
- **Hapus** section statistik (15+, 250+, 98%, 35+) dan testimoni. Jangan diganti angka/testimoni karangan lain.
- Pindahkan semua data usaha ke satu tempat: `config/usaha.php` (atau tabel pengaturan), berisi:
  - nama usaha, nomor WhatsApp, email, alamat, area layanan, jam operasional
  - nama pemilik QRIS
- Semua halaman (landing, kontak, footer, pembayaran) membaca dari situ, tidak ada yang hard-code.
- Isi nilai sementara dengan teks yang jelas belum final, misalnya `ISI_NOMOR_WA`, dan beri komentar `// TODO: ganti dengan data asli CV Pooja Cool Prima`.
- **Hapus** tampilan rekening bank/e-wallet jika pembayaran hanya lewat QRIS.
- Gambar QRIS tetap memakai `public/images/qris.jpeg`. Tambahkan catatan di README bahwa file ini **wajib** diganti dengan QRIS asli sebelum website dipakai.

---

## Checklist

- [ ] Kode unik tidak bentrok antar pesanan aktif + tes
- [ ] Status Dibatalkan, kedaluwarsa 24 jam (scheduler), batal manual oleh admin + tes
- [ ] Sesi Pagi/Siang, kuota per sesi, batas waktu pesan, validasi backend + tes
- [ ] Admin ubah jadwal + log jadwal lama
- [ ] Layanan "Mulai dari" + catatan sparepart
- [ ] Hapus statistik & testimoni karangan
- [ ] Data usaha terpusat di config + TODO
- [ ] Semua tes lama dan baru lulus