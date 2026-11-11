# DESIGN.md

Panduan desain untuk website jasa HVACR dan AC rumahan. Semua halaman wajib mengikuti dokumen ini agar tampilan konsisten. Implementasi memakai **Tailwind CSS + Blade components**.

## 1. Prinsip Desain

1. **Bersih, segar, tepercaya.** Kesan "dingin" dan profesional, cocok dengan bisnis pendingin udara.
2. **Mobile first.** Mayoritas pelanggan membuka dari HP. Rancang untuk 375px dulu, lalu naik ke desktop.
3. **Satu tujuan per halaman.** Halaman order fokus memilih layanan, halaman pembayaran fokus bayar dan upload.
4. **Jelas soal harga.** Total, kode unik, dan rincian selalu terlihat. Tidak ada biaya tersembunyi.
5. **Cepat dan ringan.** Gambar dioptimasi, tanpa animasi berat.

## 2. Warna

Definisikan di `tailwind.config.js` sebagai token, jangan pakai hex langsung di view.

| Token | Hex | Penggunaan |
|---|---|---|
| `primary-50` | `#EFF8FF` | Latar section lembut |
| `primary-100` | `#DBEFFE` | Hover ringan, badge |
| `primary-500` | `#1D9BF0` | Aksen, link |
| `primary-600` | `#0B7FD1` | **Tombol utama**, brand |
| `primary-700` | `#0A66A8` | Hover tombol utama |
| `primary-900` | `#0B2F4A` | Heading, footer |
| `accent-500` | `#F97316` | CTA sekunder penting (Pesan Sekarang), highlight promo |
| `accent-600` | `#EA580C` | Hover accent |
| `neutral-50` | `#F8FAFC` | Latar halaman |
| `neutral-200` | `#E2E8F0` | Border |
| `neutral-600` | `#475569` | Teks sekunder |
| `neutral-900` | `#0F172A` | Teks utama |
| `success-600` | `#16A34A` | Disetujui, selesai |
| `warning-500` | `#F59E0B` | Menunggu verifikasi |
| `danger-600` | `#DC2626` | Ditolak, error |

Aturan:

- Biru = brand dan aksi utama. Oranye hanya untuk **satu CTA paling penting** per layar.
- Kontras teks terhadap latar minimal **WCAG AA (4.5:1)**.
- Jangan mengandalkan warna saja untuk status. Sertakan teks atau ikon.

## 3. Tipografi

- **Font utama:** `Plus Jakarta Sans` (alternatif: `Inter`). Muat dari Google Fonts dengan `display=swap`, fallback `system-ui, sans-serif`.
- Tidak lebih dari 2 bobot utama: 400 (isi) dan 600/700 (judul).

| Elemen | Mobile | Desktop | Bobot |
|---|---|---|---|
| H1 (hero) | 30px | 48px | 700 |
| H2 (judul section) | 24px | 36px | 700 |
| H3 (judul card) | 18px | 20px | 600 |
| Body | 16px | 16px | 400 |
| Caption/label | 14px | 14px | 500 |

Line-height isi 1.6, judul 1.2. Lebar baris teks maksimal sekitar 65 karakter.

## 4. Spacing, Radius, Bayangan

- Skala spacing Tailwind (kelipatan 4px). Jarak antar section: `py-16` mobile, `py-24` desktop.
- Container: `max-w-6xl mx-auto px-4 sm:px-6`.
- Radius: card `rounded-2xl`, tombol dan input `rounded-xl`, badge `rounded-full`.
- Bayangan: `shadow-sm` untuk card biasa, `shadow-lg` hanya untuk elemen yang melayang (navbar sticky, ringkasan harga).

## 5. Breakpoint

Gunakan breakpoint default Tailwind: `sm 640`, `md 768`, `lg 1024`, `xl 1280`.

- Grid layanan: 1 kolom (mobile), 2 (md), 3 (lg).
- Navbar: hamburger di bawah `md`, menu horizontal di atas `md`.

## 6. Komponen

Buat sebagai Blade component di `resources/views/components/`.

### Tombol (`<x-button>`)

- **Primary:** bg `primary-600`, teks putih, hover `primary-700`.
- **Accent (CTA):** bg `accent-500`, teks putih.
- **Secondary:** border `primary-600`, teks `primary-600`, latar transparan.
- Tinggi minimal **44px** (area sentuh nyaman). Ada state `focus-visible` (ring `primary-500`), `disabled`, dan `loading`.

### Input form (`<x-input>`, `<x-textarea>`, `<x-select>`)

- Label selalu di atas input, bukan hanya placeholder.
- Border `neutral-200`, fokus ring `primary-500`.
- Pesan error di bawah field, warna `danger-600`, teks bahasa Indonesia yang jelas ("Nomor telepon wajib diisi").
- Field telepon memakai `type="tel"` dan `inputmode="numeric"`.

### Card layanan (`<x-service-card>`)

- Ikon/gambar, nama, deskripsi singkat, harga (`Rp` dengan pemisah titik), kontrol qty (tombol `−` `+`) atau tombol pilih.
- Kartu terpilih: border `primary-600` dan latar `primary-50`.
- Badge "Paling Laris" memakai warna accent (maksimal satu per grup).

### Badge status (`<x-status-badge>`)

| Status | Warna | Label |
|---|---|---|
| `menunggu_pembayaran` | neutral | Menunggu Pembayaran |
| `menunggu_verifikasi` | warning | Menunggu Verifikasi |
| `disetujui` | success | Disetujui |
| `ditolak` | danger | Ditolak |
| `selesai` | primary | Selesai |

### Ringkasan harga (`<x-price-summary>`)

- Daftar item dengan qty dan subtotal, lalu subtotal, kode unik, dan **Total** (angka paling besar dan tebal).
- Di mobile: bar sticky di bawah layar berisi total dan tombol lanjut.
- Di desktop: kolom sticky di sisi kanan.

### Alert/notifikasi

Empat varian (info, success, warning, danger) dengan ikon dan teks. Muncul di atas konten, bisa ditutup.

## 7. Layout per Halaman

### 7.1 Navbar (semua halaman publik)

- Logo di kiri. Menu: **Home, Order, About, Contact**.
- Tombol "Pesan Sekarang" (accent) di kanan, mengarah ke `/order`.
- Sticky di atas, latar putih, `shadow-sm` saat discroll.
- Menu aktif diberi warna `primary-600` dan garis bawah.

### 7.2 Home

Urutan section:

1. **Hero:** judul manfaat jelas ("Service AC Cepat, Rapi, dan Bergaransi"), subjudul, 2 tombol (Pesan Sekarang, Hubungi via WhatsApp), foto teknisi atau ilustrasi AC.
2. **Layanan unggulan:** 3 sampai 6 card (Cuci AC, Isi Freon, Instalasi AC Baru, Perbaikan, Bongkar Pasang, Maintenance Berkala).
3. **Kenapa memilih kami:** 3 sampai 4 poin dengan ikon (Teknisi berpengalaman, Harga transparan, Garansi servis, Datang tepat waktu).
4. **Cara order:** 4 langkah bernomor (Pilih layanan → Isi data → Bayar QRIS → Teknisi datang).
5. **Paket dan harga ringkas** dengan tombol ke halaman order.
6. **Galeri hasil kerja** (grid foto, lazy load).
7. **Testimoni** pelanggan (nama, area, kutipan singkat).
8. **FAQ** (accordion).
9. **CTA penutup** (latar `primary-900`, teks putih, tombol accent).
10. **Footer:** logo, alamat, jam operasional, kontak, menu, area layanan, hak cipta.

### 7.3 About

Cerita singkat usaha, visi, pengalaman, angka pencapaian (jumlah pelanggan, tahun berdiri), foto tim, sertifikasi/legalitas jika ada, dan area layanan.

### 7.4 Contact

Kartu kontak (WhatsApp, telepon, email, alamat), jam operasional, peta (embed Google Maps atau tautan), dan form pesan singkat (opsional). Tombol WhatsApp menonjol.

### 7.5 Order

Alur satu halaman, dua langkah visual:

1. **Pilih layanan:** tab/filter **Paket** dan **Satuan**. Card layanan dengan kontrol qty. Ringkasan harga terbarui otomatis (Alpine.js).
2. **Data pelanggan:** nama, nomor telepon/WhatsApp, alamat lengkap, tanggal dan jam kunjungan, catatan (jenis AC, jumlah unit, keluhan).
3. Tombol **Lanjut ke Pembayaran** nonaktif sampai minimal 1 layanan dipilih dan form valid.

Catatan kecil di dekat total: "Total final dihitung ulang oleh sistem saat pesanan dibuat."

### 7.6 Pembayaran

- Header: kode order dan batas waktu bayar (opsional).
- Kartu ringkasan pesanan dan **total yang harus dibayar** (termasuk kode unik) dengan tombol salin nominal.
- **Gambar QRIS** di tengah, lebar maksimal 320px, latar putih, bingkai rapi, tombol "Unduh QRIS".
- Langkah singkat: Scan → Bayar sesuai nominal → Upload bukti.
- **Form upload:** area drag-and-drop dengan pratinjau gambar, info format (JPG/PNG/WEBP, maks 2 MB), tombol Kirim Bukti.
- Setelah upload: tampilkan status "Menunggu Verifikasi" dan tombol konfirmasi via WhatsApp (pesan terisi kode order).

### 7.7 Status Order

Input kode order (atau langsung dari URL), badge status, timeline sederhana (Dibuat → Dibayar → Diverifikasi → Selesai), rincian pesanan. Jika ditolak, tampilkan alasan dan tombol upload ulang.

### 7.8 Admin

- Layout dengan sidebar (desktop) atau menu atas (mobile). Latar `neutral-50`, nuansa lebih netral dan padat informasi.
- **Daftar order:** tabel dengan kolom kode, nama, total, status (badge), tanggal. Filter status, pencarian kode/nama, paginasi. Di mobile berubah menjadi daftar kartu.
- **Detail order:** data pelanggan, rincian item, **pratinjau bukti bayar** (klik untuk perbesar, modal), nominal yang diharapkan.
- Aksi: tombol **Setujui** (success) dan **Tolak** (danger, wajib isi alasan) dengan dialog konfirmasi.
- Halaman login admin sederhana, terpusat, tanpa navbar publik.

## 8. Ikon dan Gambar

- Ikon: **Heroicons** (outline) atau **Lucide**, ukuran konsisten (20 atau 24px).
- Foto: teknisi bekerja, unit AC, rumah. Hindari stock foto yang terasa palsu, utamakan foto asli.
- Format WebP, `loading="lazy"`, atribut `width` dan `height` untuk mencegah layout shift, `alt` deskriptif.
- Gaya ilustrasi (jika dipakai): flat dengan palet biru dan oranye di atas.

## 9. Tone of Voice (Teks)

- Bahasa Indonesia yang ramah, jelas, tidak bertele-tele. Gunakan "Anda" atau "kamu" secara konsisten (pilih satu, disarankan **Anda**).
- Tombol berupa kata kerja: "Pesan Sekarang", "Kirim Bukti", "Setujui".
- Pesan error membantu dan spesifik, tidak menyalahkan pengguna.
- Format angka: `Rp 150.000`. Format tanggal: `10 November 2026`. Zona waktu WIB.

## 10. Aksesibilitas

- Semua elemen interaktif bisa diakses dengan keyboard dan punya `focus-visible` jelas.
- Gunakan HTML semantik (`nav`, `main`, `section`, `footer`, `button`, `label`).
- `alt` pada semua gambar bermakna (QRIS: "Kode QRIS pembayaran").
- Area sentuh minimal 44×44px.
- Hormati `prefers-reduced-motion`.
- Pesan form error dihubungkan dengan `aria-describedby`.

## 11. Animasi dan Interaksi

- Transisi halus 150 sampai 200ms untuk hover, fokus, dan buka/tutup accordion.
- Tanpa animasi besar atau parallax. Fokus pada kecepatan.
- Tampilkan state loading pada tombol saat submit untuk mencegah klik ganda.

## 12. SEO dan Performa (terkait desain)

- Satu `<h1>` per halaman, hirarki heading berurutan.
- Title dan meta description per halaman, mengandung kata kunci lokal ("Service AC [Kota]").
- Open Graph image untuk berbagi di WhatsApp.
- Target Lighthouse mobile: Performance dan Accessibility ≥ 90.
- Schema.org `LocalBusiness` di halaman Home.

## 13. Checklist Sebelum Rilis UI

- [ ] Semua halaman rapi di 375px, 768px, dan 1280px
- [ ] Warna hanya memakai token dari bagian 2
- [ ] Kontras teks lolos AA
- [ ] Semua tombol dan input punya state hover, fokus, dan disabled
- [ ] Gambar dioptimasi dan memiliki `alt`
- [ ] QRIS terlihat jelas dan bisa di-scan dari layar HP
- [ ] Teks seluruhnya Bahasa Indonesia dan konsisten dengan tone of voice