# DESIGN.md

Panduan desain website jasa instalasi, maintenance, dan perbaikan HVACR/AC rumahan.
Arah visual mengacu pada gambar referensi (website kontraktor konstruksi bergaya modern-minimal). Tujuannya **meniru kesan dan struktur visualnya**, bukan menyalin konten atau aset. Semua teks, foto, dan logo harus milik usaha ini.

Implementasi: Laravel Blade + Tailwind CSS. Baca bersama `AGENTS.md`.

> Nilai warna dan ukuran di bawah adalah perkiraan dari gambar referensi. Jika ada file Figma atau kode warna asli, nilai itu menang.

---

## 1. Karakter Visual (Inti)

Satu kalimat: **terang, lapang, tipografi besar, foto nyata, hampir tanpa dekorasi.**

Ciri khas yang harus terlihat:

1. **Latar putih dan abu sangat muda**, bergantian antar section. Bukan putih polos terus-menerus.
2. **Tipografi besar dan rapat**, sans-serif modern, bobot sedang (bukan tebal ekstrem), tracking sedikit rapat.
3. **Foto sebagai elemen utama**, sudut membulat, tanpa overlay warna berat. Foto bersih, terang, bergaya arsitektur/interior.
4. **Hero full-bleed dengan sudut membulat**, di dalam margin halaman (tidak mentok ke tepi layar). Judul sangat besar berwarna putih, **dipecah kiri dan kanan** pada dua baris berbeda.
5. **Aksen warna tunggal: biru kelabu (steel/periwinkle)** untuk panel CTA, footer, angka langkah, dan tanda kutip. Sisanya hitam, putih, abu.
6. **Tombol utama hitam pekat**, kecil, huruf kapital, dengan ikon panah kecil.
7. **Label kecil huruf kapital (eyebrow)** di atas hampir setiap judul section.
8. **Footer ditutup wordmark raksasa** selebar layar, warna putih di atas panel biru kelabu.
9. **Kartu datar**: latar abu muda, sudut membulat, **tanpa bayangan** dan tanpa border tebal.

## 2. Hal yang DILARANG (anti "AI slop")

- Gradien warna di latar, tombol, atau teks.
- Ikon emoji atau ikon besar berwarna-warni dalam lingkaran pastel.
- Tema ungu/pink, neon, atau glassmorphism.
- Bayangan tebal (`shadow-xl`, `shadow-2xl`) dan efek glow.
- Semua teks rata tengah. Referensi dominan **rata kiri**.
- Kartu fitur dengan ikon di atas, judul di tengah, deskripsi di tengah (pola templat generik).
- Hero berisi "badge pil" kecil + judul tengah + dua tombol tengah.
- Foto stock yang terlihat palsu atau kolase ilustrasi 3D.
- Teks tebal (`font-extrabold`/`font-black`) untuk judul. Gunakan bobot 500.
- Jarak sempit. Referensi sangat lapang.
- Lebih dari satu warna aksen.

## 3. Design Tokens

Definisikan di `tailwind.config.js`. Jangan pakai hex langsung di view.

### Warna

| Token | Hex (perkiraan) | Penggunaan |
|---|---|---|
| `ink` | `#0E1116` | Teks utama, tombol utama, nav tombol |
| `ink-soft` | `#4B5563` | Paragraf, deskripsi |
| `ink-mute` | `#8A919C` | Label kecil, meta, teks sekunder |
| `paper` | `#FFFFFF` | Latar halaman dan kartu pada section abu |
| `mist` | `#F3F4F6` | Latar kartu di atas putih |
| `fog` | `#EEF1F6` | Latar section selang-seling (agak kebiruan) |
| `line` | `#E5E7EB` | Garis pemisah tipis |
| `steel-300` | `#B7C8E3` | Angka langkah (01 02 03 04), tanda kutip |
| `steel-500` | `#6F8FBF` | **Aksen utama**: panel CTA, footer |
| `steel-600` | `#5B7AAA` | Hover pada elemen aksen |
| `success` | `#16A34A` | Status disetujui (admin/status order) |
| `warning` | `#D97706` | Status menunggu verifikasi |
| `danger` | `#DC2626` | Status ditolak, error |

Aturan:

- Warna status (success/warning/danger) **hanya** untuk badge status dan pesan error. Jangan dipakai dekorasi.
- Teks di atas `steel-500` selalu putih dan harus lolos kontras untuk teks besar. Teks kecil di atas biru gunakan putih dengan bobot 500.
- Kontras teks isi minimal WCAG AA.

### Tipografi

- **Heading:** `Inter Tight` (Google Fonts), bobot **500**, `letter-spacing: -0.03em`, `line-height: 1.05 sampai 1.1`.
- **Body dan UI:** `Inter`, bobot 400/500, `line-height: 1.6`.
- Fallback: `system-ui, sans-serif`. Muat dengan `display=swap`.

| Elemen | Mobile | Desktop | Catatan |
|---|---|---|---|
| Hero (H1) | 44px | 120 sampai 160px | HURUF KAPITAL, putih, bobot 500 |
| H2 section | 32px | 48 sampai 56px | Huruf kapital di awal kata saja, rata kiri, maksimal 2 baris |
| H3 judul kartu | 18px | 20px | Bobot 500 |
| Paragraf | 15 sampai 16px | 16px | `ink-soft`, lebar maksimal 60ch |
| Eyebrow | 11px | 12px | KAPITAL, `tracking-[0.12em]`, `ink-mute` |
| Tombol/tautan kecil | 11 sampai 12px | 12px | KAPITAL, `tracking-wider`, bobot 600 |
| Angka statistik | 40px | 64px | Bobot 500, `ink` |
| Angka langkah | 48px | 72px | `steel-300`, bobot 500 |
| Wordmark footer | 22vw | 22vw | Selebar container, putih, bobot 600 |

Teks tambahan bergaya "abu muda besar" (seperti "/ Delivering with Excellence." di referensi): ukuran sama dengan H2, warna `#C3C8D0`, diawali karakter `/`.

### Spacing, radius, bayangan

- Container: `max-w-[1440px] mx-auto px-4 md:px-8`.
- Jarak antar section: `py-20` mobile, `py-32` desktop. **Lapang.**
- Radius: panel besar/hero/CTA/footer `rounded-[28px]`, kartu `rounded-2xl` (16px), foto `rounded-xl`, tombol `rounded-full` atau `rounded-lg` (pilih satu dan konsisten, referensi memakai bentuk kecil membulat penuh).
- Bayangan: **tidak ada**, kecuali `shadow-sm` sangat tipis pada kartu statistik yang melayang di hero.
- Garis pemisah: `border-line` 1px, dipakai hemat (di atas baris statistik, di footer).

## 4. Komponen

### Navbar

- Bar tipis di atas, **menimpa hero** (transparan, teks putih) di Home. Di halaman lain: latar putih, teks `ink`.
- Kiri: logo kecil. Tengah: Home, Order, About, Contact (12px, huruf kecil biasa). Kanan: tombol hitam kecil **"PESAN SEKARANG ↗"**.
- Mobile: logo + tombol hamburger, menu terbuka sebagai panel penuh putih.

### Tombol

- **Utama:** latar `ink`, teks putih, KAPITAL 11 sampai 12px, padding `px-5 py-3`, ikon panah `↗` kecil di kanan. Hover: `bg-black` + panah bergeser 2px.
- **Sekunder di atas foto:** latar putih, teks `ink`, bentuk sama.
- **Tautan teks:** "LIHAT SEMUA ↗" huruf kapital kecil, tanpa latar.
- Tinggi sentuh minimal 44px di mobile.

### Kartu layanan (grid 3 kolom)

Struktur dari atas ke bawah: **foto** (rasio sekitar 4:3, `rounded-xl`) → nomor kecil `01` (eyebrow) → judul 18px → deskripsi 2 sampai 3 baris `ink-soft` 13 sampai 14px → tautan **"LIHAT LAYANAN ↗"**.
Latar kartu `mist`, padding 12px di sekeliling, jarak antar kartu 16px. Tanpa border, tanpa bayangan.

### Kartu nilai (2×2) dan kartu langkah (4 kolom)

- Kartu nilai: latar putih di atas section `fog`, judul kecil, deskripsi pendek, ikon sangat kecil di pojok kanan atas (garis tipis, monokrom).
- Kartu langkah: latar `mist`, angka besar `steel-300` di atas, judul dan deskripsi pendek di bawah.

### Statistik (empat angka)

Baris empat kolom di bawah garis tipis. Label eyebrow kecil di atas, angka besar di bawah (`15+`, `250+`, `98%`, `35+`). Di mobile menjadi 2×2.

### Kartu proyek/galeri (grid 2 kolom)

Foto besar (rasio sekitar 16:10, `rounded-xl`), di bawahnya baris meta kecil KAPITAL `ink-mute` (lokasi · tahun), lalu judul 20px, lalu satu baris deskripsi.

### Testimoni (3 kolom)

Section dengan latar `fog`. Tanda kutip besar `steel-500`, isi testimoni 15px, garis pemisah tipis, lalu **nama KAPITAL kecil** dan peran/area `ink-mute`. Tanpa foto bulat, tanpa bintang.

### Panel CTA penutup

Panel lebar penuh `rounded-[28px]`, **foto gelap-biru sebagai latar** (langit/bangunan, dengan overlay `steel-500` tipis sekitar 40 persen untuk keterbacaan), judul besar putih rata kiri, paragraf putih 80 persen, satu tombol hitam atau putih.

### Footer

Panel `steel-500`, `rounded-t-[28px]`, teks putih. Kolom: logo + deskripsi singkat + kontak, lalu 3 kolom tautan (Perusahaan, Layanan, Sosial). Di bagian paling bawah: **wordmark nama usaha raksasa** selebar container, dipotong sedikit di bawah (efek besar dan berani). Baris hak cipta kecil di bawahnya.

### Form (order, pembayaran)

- Input: latar `mist`, tanpa border, `rounded-xl`, tinggi 48px, fokus: ring 2px `ink`.
- Label di atas, 12px KAPITAL `ink-mute`.
- Error di bawah field, `danger`, 13px, Bahasa Indonesia yang spesifik.
- Pilihan layanan terpilih: border 1.5px `ink` dan latar putih (bukan warna terang).

### Badge status

Pil kecil 11px KAPITAL dengan titik warna di kiri: Menunggu Pembayaran (abu), Menunggu Verifikasi (`warning`), Disetujui (`success`), Ditolak (`danger`), Selesai (`ink`). Latar badge sangat muda dari warna yang sama.

## 5. Layout Halaman Publik

### 5.1 Home (urutan mengikuti referensi)

1. **Hero:** foto full-bleed membulat di dalam margin. Judul KAPITAL putih dipecah: baris pertama rata kiri atas, baris kedua rata kanan bawah (contoh: "UDARA SEJUK," / "RUMAH NYAMAN."). Teks kecil dan dua tombol di kiri bawah. **Kartu statistik putih kecil** di kanan bawah (angka pelanggan terlayani + avatar tumpuk).
2. **Tentang singkat ("Bekerja dengan Sepenuh Hati."):** eyebrow, judul, foto kecil di kiri, teks abu besar "/ ..." di kanan, paragraf, tombol; di bawahnya baris empat statistik.
3. **Layanan:** judul kiri, "LIHAT SEMUA ↗" kanan, grid 3×2 kartu layanan (Cuci AC, Isi Freon, Instalasi AC Baru, Perbaikan, Bongkar Pasang, Maintenance Berkala).
4. **Nilai usaha:** section `fog`, judul 4 baris di kiri, kartu 2×2 di kanan (Teknisi Berpengalaman, Harga Transparan, Garansi Servis, Tepat Waktu).
5. **Hasil kerja:** grid 2×2 foto proyek dengan meta dan judul.
6. **Proses:** judul + 4 kartu langkah bernomor (Pilih Layanan → Isi Data → Bayar QRIS → Teknisi Datang).
7. **Testimoni:** section `fog`, 3 kolom.
8. **Tips/artikel (opsional, bisa dilewati di fase awal):** 3 kartu artikel (misal "Kapan AC perlu dicuci").
9. **CTA penutup + footer wordmark.**

### 5.2 About

Pola sama dengan Home: eyebrow + judul besar rata kiri, foto besar membulat, paragraf cerita, baris statistik, nilai usaha 2×2, foto tim, CTA penutup.

### 5.3 Contact

Dua kolom: kiri judul + info kontak (WhatsApp, telepon, email, alamat, jam operasional) dalam kartu `mist`; kanan form pesan atau peta. Tombol WhatsApp memakai gaya tombol utama.

## 6. Halaman Transaksional (Order, Pembayaran, Status)

Halaman ini **lebih tenang dan fungsional** tetapi tetap memakai bahasa visual yang sama (warna, tipografi, radius). Tanpa hero foto.

### 6.1 Order

- Header sederhana: eyebrow "PESAN LAYANAN" + H2 rata kiri.
- Layout dua kolom di desktop: kiri daftar layanan (tab **Paket** / **Satuan**, kartu seperti kartu layanan tetapi lebih ringkas dengan kontrol qty `−  1  +`), kanan **ringkasan harga sticky** (kartu `mist`, daftar item, subtotal, **Total** besar 32px).
- Di mobile: ringkasan menjadi bar bawah sticky (total + tombol hitam "LANJUT ↗").
- Setelah pilih layanan: form data pelanggan (nama, telepon/WhatsApp, alamat, tanggal kunjungan, catatan).
- Catatan kecil `ink-mute`: "Total akhir dihitung ulang oleh sistem saat pesanan dibuat."

### 6.2 Pembayaran

- Eyebrow berisi kode order, H2 "Selesaikan Pembayaran."
- Kartu `mist`: ringkasan dan **total yang harus dibayar** (angka besar, termasuk kode unik) + tombol "SALIN NOMINAL".
- **QRIS** di kartu putih bersih, lebar maksimal 320px, bingkai `rounded-2xl`, tombol "UNDUH QRIS". Jangan beri latar berwarna di belakang QR agar mudah dipindai.
- Tiga langkah singkat bernomor dengan gaya angka `steel-300`.
- Area upload: kotak garis putus-putus `line`, `rounded-2xl`, ikon kecil, pratinjau setelah dipilih, info "JPG/PNG/WEBP, maks 2 MB". Tombol hitam "KIRIM BUKTI ↗".
- Setelah upload: status "Menunggu Verifikasi" + tautan konfirmasi WhatsApp.

### 6.3 Status Order

Kartu tunggal terpusat dalam container sempit (max 720px): badge status, timeline vertikal tipis (garis `line`, titik `ink` untuk tahap selesai, `steel-300` untuk berikutnya), rincian pesanan.

## 7. Admin

Lebih padat dan netral, tetap satu keluarga visual.

- Latar `mist`, kartu putih `rounded-2xl`, tipografi sama, **tanpa aksen biru besar**.
- Sidebar kiri (desktop) putih dengan logo, menu: Order, Layanan, Pengaturan. Mobile: menu atas dengan hamburger.
- **Daftar order:** tabel bersih dengan header KAPITAL kecil `ink-mute`, baris dipisah garis tipis, badge status, filter status + pencarian. Di mobile menjadi daftar kartu.
- **Detail order:** dua kolom, kiri data pelanggan dan item, kanan **pratinjau bukti bayar** (klik untuk memperbesar di modal). Nominal yang diharapkan ditampilkan besar di dekat bukti.
- Aksi: **Setujui** (tombol hitam) dan **Tolak** (tombol outline `danger`, wajib isi alasan, dengan dialog konfirmasi).
- Halaman login: kartu terpusat, tanpa navbar publik.

## 8. Foto dan Gambar

Foto adalah penentu utama kesan "premium". Aturan:

- Gaya foto: **terang, bersih, kontras lembut, komposisi geometris**. Contoh subjek: teknisi memasang unit indoor di dinding minimalis, unit outdoor rapi, close-up pembersihan filter, interior rumah modern dengan AC terpasang, tampak luar rumah.
- Gunakan **foto asli pekerjaan sendiri** bila ada. Jika memakai foto lisensi, pilih yang tidak terlihat seperti stock klise (hindari pose tersenyum ke kamera dengan alat yang dipegang kaku).
- Konsistensi: nada warna foto sebaiknya cenderung netral-dingin agar serasi dengan aksen biru kelabu.
- Format WebP, `loading="lazy"` (kecuali hero), atribut `width`/`height`, `alt` deskriptif.
- Rasio konsisten per jenis: layanan 4:3, proyek 16:10, hero 16:9 desktop dan 4:5 mobile.
- Ikon: gunakan **Lucide** garis tipis (stroke 1.5), ukuran 16 sampai 20px, monokrom `ink`. Hanya sebagai pelengkap, bukan elemen utama.

## 9. Interaksi dan Gerak

- Transisi 150 sampai 250ms, `ease-out`.
- Hover kartu foto: gambar memperbesar sangat halus (`scale-[1.03]`) di dalam bingkai `overflow-hidden`.
- Hover tombol: panah bergeser, warna sedikit lebih gelap.
- Muncul saat scroll: fade + naik 12px, sekali saja, dan **nonaktif** bila `prefers-reduced-motion`.
- Tanpa parallax, tanpa animasi mencolok, tanpa carousel otomatis.

## 10. Responsif

- Mulai desain dari 375px. Breakpoint Tailwind default.
- Hero mobile: judul 44px dipecah beberapa baris, tetap rata kiri; kartu statistik berpindah di bawah teks.
- Grid layanan: 1 kolom (mobile), 2 (`md`), 3 (`lg`). Grid proyek: 1 kolom (mobile), 2 (`md`).
- Section judul dengan tautan "LIHAT SEMUA" kanan berpindah ke bawah judul di mobile.
- Wordmark footer memakai satuan `vw` agar selalu selebar layar.

## 11. Nada Bahasa (Konten)

- Bahasa Indonesia, ringkas, percaya diri, tidak bertele-tele. Gunakan "Anda".
- Judul berupa pernyataan pendek yang bernilai ("Layanan AC Lengkap, Dikerjakan Rapi."), bukan slogan kosong.
- Tombol berupa kata kerja: "PESAN SEKARANG", "KIRIM BUKTI", "LIHAT LAYANAN".
- Format: `Rp 150.000`, tanggal `11 November 2026`, zona WIB.

## 12. Aksesibilitas dan SEO

- HTML semantik (`header`, `nav`, `main`, `section`, `footer`), satu `<h1>` per halaman.
- Fokus keyboard terlihat jelas (ring 2px `ink` dengan offset).
- Teks putih di atas foto wajib memiliki overlay gelap tipis agar terbaca.
- Hormati `prefers-reduced-motion`.
- Title dan meta per halaman memuat kata kunci lokal ("Service AC [Kota]"), Open Graph image, schema `LocalBusiness` di Home.
- Target Lighthouse mobile: Performance dan Accessibility di atas 90 (foto dioptimasi!).

## 13. Contoh Konfigurasi Tailwind

```js
// tailwind.config.js
export default {
  theme: {
    extend: {
      colors: {
        ink: { DEFAULT: '#0E1116', soft: '#4B5563', mute: '#8A919C' },
        paper: '#FFFFFF',
        mist: '#F3F4F6',
        fog: '#EEF1F6',
        line: '#E5E7EB',
        steel: { 300: '#B7C8E3', 500: '#6F8FBF', 600: '#5B7AAA' },
      },
      fontFamily: {
        heading: ['"Inter Tight"', 'system-ui', 'sans-serif'],
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      letterSpacing: { tightest: '-0.03em' },
      borderRadius: { panel: '28px' },
    },
  },
};
```

## 14. Checklist Sebelum Dianggap Selesai

- [ ] Home dibuka berdampingan dengan gambar referensi: struktur section, ritme jarak, dan skala tipografi terasa setara
- [ ] Hanya satu warna aksen (biru kelabu) selain hitam/putih/abu
- [ ] Tidak ada gradien, emoji, bayangan tebal, atau teks tengah yang berlebihan
- [ ] Hero memakai foto nyata, judul besar terpecah kiri-kanan
- [ ] Footer memiliki wordmark raksasa
- [ ] Semua halaman rapi di 375px, 768px, dan 1440px
- [ ] Halaman order, pembayaran, dan admin terasa satu keluarga dengan Home
- [ ] Kontras, fokus keyboard, dan `alt` gambar sudah diperiksa
- [ ] Semua teks UI Bahasa Indonesia