# DESIGNUIUX — Revisi Warna & Hero

> Addendum untuk `DESIGNUIUX.md`. **Layout, struktur halaman, navbar, font, dan halaman admin TIDAK diubah.**
> Yang diubah hanya: warna, kesan "terlalu estetik/arsitek", dan isi hero.

---

## 0. ROLLBACK DULU — Wajib Sebelum Menerapkan Dokumen Ini

Versi sebelumnya dari dokumen ini (revisi "biru es + gradien") **dibatalkan**. Kalau perubahan dari versi itu sudah diterapkan, kembalikan dulu ke tampilan lama, baru terapkan bagian 1–6.

> Cara paling aman: kalau proyek pakai git, kembalikan file style/komponen ke commit **sebelum** revisi biru-es diterapkan (`git log` → cari commit sebelumnya → `git checkout <commit> -- <file>`). Kalau tidak ada git, ikuti daftar di bawah.

### 0.1 Hapus semua ini (berasal dari revisi yang dibatalkan)
- Token `--primary-50` s/d `--primary-700`, `--accent-*`, `--ink-*`, `--bg`, `--surface`, `--status-*` versi lama
- Semua gradien: `--grad-cool`, `--grad-cta`, `linear-gradient` di tombol, hero, banner CTA, teks
- `--shadow-soft`, `--shadow-hover`, dan efek kartu naik saat hover
- Font **Plus Jakarta Sans** dan link Google Fonts-nya → kembalikan font lama
- Hero split 2 kolom, blob/lingkaran gradien, garis SVG aliran udara + animasinya, chip mengambang "18°C", badge "❄ Teknisi bersertifikat", baris trust dengan ikon centang
- Ikon dalam lingkaran berwarna di kartu layanan
- `backdrop-filter: blur` di navbar
- Penghapusan `text-transform: uppercase` → **kembalikan uppercase** di headline dan tombol

### 0.2 Tampilan lama yang harus dikembalikan (acuan)
**Navbar**
- Background putih, tinggi ±110px, garis tipis di bawah
- Kiri: wordmark **POOJA COOL** (bold, tracking rapat) + label kecil **HVACR** (uppercase, letter-spacing lebar, abu-abu)
- Tengah: menu huruf kecil — `home · order · tentang kami · kontak · cek status`; menu aktif teks gelap + garis bawah tebal
- Kanan: tombol pill gelap **PESAN SEKARANG ↗** (uppercase, bold, teks putih)

**Hero**
- Kontainer foto lebar (sejajar margin konten, bukan full-bleed tepi layar), langsung di bawah navbar
- Headline sangat besar, **UPPERCASE**, putih, font grotesk tebal dengan tracking rapat (negatif)
- Tata letak berundak: baris 1 **UDARA SEJUK,** rata kiri; baris 2 **RUMAH NYAMAN.** menjorok ke kanan
- Tanpa badge, tanpa ikon, tanpa dekorasi

**Gaya umum**
- Flat, tegas, kontras tinggi, sudut tajam/kecil, tanpa shadow berwarna
- Font: font grotesk yang dipakai sebelum revisi. Kalau tidak tercatat, pakai **Inter** 700–800, `letter-spacing: -0.03em` untuk headline

Setelah tampilan kembali seperti di atas, lanjut ke bagian 1.

---

## 1. Prinsip

- **Warna solid saja.** Tidak ada gradien, blob, glassmorphism, glow, atau shadow berwarna.
- **Kurangi kesan galeri/arsitek.** Website ini toko jasa, bukan portofolio. Setiap section harus langsung menjawab: *layanan apa, harga berapa, cara pesan gimana*.
- **Satu warna utama, satu warna gelap, satu warna muda.** Tidak lebih.

---

## 2. Palet Warna (solid)

| Token | Hex | Peran | Porsi |
|---|---|---|---|
| `--white` | `#FFFFFF` | Background utama, kartu | ~55% |
| `--ice` | `#E3F2FB` | Background section selang-seling, area info | ~25% |
| `--blue` | `#1771B8` | Tombol, link, menu aktif, angka harga | ~10% |
| `--navy` | `#0F2A44` | Heading, teks utama, footer, overlay hero | ~10% |
| `--slate` | `#5B6B7A` | Teks sekunder, caption | — |
| `--line` | `#D6E3EC` | Border, garis pemisah | — |

```css
:root {
  --white: #FFFFFF;
  --ice:   #E3F2FB;
  --blue:  #1771B8;
  --blue-hover: #125E9A;
  --navy:  #0F2A44;
  --slate: #5B6B7A;
  --line:  #D6E3EC;

  /* status admin — dipakai di admin saja */
  --st-pending:  #C98A12;
  --st-approved: #1E8A5A;
  --st-rejected: #C23B3B;
}
```

### Kombinasi yang boleh (sudah lolos kontras WCAG AA)

| Teks | Di atas | Contoh pakai |
|---|---|---|
| `--navy` | `--white` | Heading & body |
| `--navy` | `--ice` | Section layanan / cara pesan |
| `--white` | `--blue` | Tombol utama |
| `--white` | `--navy` | Footer, hero overlay, tombol "Pesan Sekarang" |
| `--blue` | `--white` | Link, harga |
| `--ice` | `--navy` | Teks kecil di footer |

### Kombinasi yang dilarang
- `--blue` di atas `--ice` untuk teks kecil (kontras kurang)
- `--slate` di atas `--ice` untuk teks < 14px
- Lebih dari 2 warna latar dalam satu section
- Hitam murni `#000`. Ganti semua hitam lama dengan `--navy`.

---

## 3. Penerapan ke Elemen

- **Navbar:** tetap seperti sekarang. Background `--white`, teks `--navy`, garis bawah menu aktif `--blue` (2px).
- **Tombol "Pesan Sekarang":** tetap bentuk pill seperti sekarang, background `--navy`, hover `--blue`.
- **Tombol sekunder:** border 1px `--navy`, teks `--navy`, background transparan.
- **Section:** selang-seling `--white` dan `--ice`. Pemisah cukup ganti warna latar, tanpa ornamen.
- **Kartu:** background `--white`, border 1px `--line`, **tanpa shadow**, sudut sama seperti layout lama.
- **Input form:** border `--line`, saat focus border `--blue` 2px.
- **Footer:** background `--navy`, teks `--white` dan `--ice`.
- **Admin:** layout tetap. Ganti hitam → `--navy`, aksen → `--blue`, badge status pakai `--st-*` (teks putih, background solid).

---

## 4. Hero

### Yang dipertahankan
- Layout sama: kontainer foto lebar penuh di bawah navbar, headline besar putih di atas foto, posisi teks berundak (baris 1 kiri, baris 2 kanan).
- Headline: **UDARA SEJUK, RUMAH NYAMAN.** dengan font yang sama.

### Yang diubah
1. **Ganti foto.** Foto rumah arsitek diganti foto yang jelas soal AC, prioritas:
   - foto asli teknisi Pooja Cool sedang servis/memasang unit indoor, atau
   - close-up unit AC split / kisi-kisi louver di dinding ruangan.
   Jangan pakai foto rumah, gedung, atau interior mewah.
2. **Overlay solid, bukan gradien:** lapisan `--navy` dengan `opacity: 0.55` menutupi seluruh foto, supaya teks putih terbaca dan nuansanya lebih dingin.
3. **Headline sedikit dikecilkan** (±15%) agar ada ruang untuk info.
4. **Titik terakhir** pada "NYAMAN." diberi warna `--ice`. Itu satu-satunya aksen di hero.
5. **Tambahkan blok info di pojok kiri bawah hero** (ini yang menghilangkan kesan "galeri"):
   ```
   Servis · Cuci · Instalasi · Perbaikan AC
   Melayani area Malang dan sekitarnya   (sesuaikan area asli)

   [ Pesan Sekarang ]   [ Lihat Harga ]
   ```
   - Teks baris 1: putih, 18px, weight 600
   - Teks baris 2: `--ice`, 15px
   - Tombol 1: background `--white`, teks `--navy`
   - Tombol 2: border 1px `--white`, teks `--white`

### Mobile
Headline turun ke ±40px, blok info pindah di bawah headline, tombol full-width bertumpuk.

---

## 5. Konten (bagian dari menghilangkan kesan "estetik doang")

- Section layanan menampilkan **harga mulai** per layanan, bukan cuma nama dan deskripsi.
- Pakai info nyata: nomor WhatsApp, jam operasional, area layanan.
- Hindari kalimat generik seperti "solusi terbaik untuk kenyamanan Anda".

---

## 6. Checklist

- [ ] Ganti semua hitam → `--navy`
- [ ] Hapus gradien/shadow/blob kalau sempat ditambahkan dari revisi sebelumnya
- [ ] Section selang-seling `--white` / `--ice`
- [ ] Ganti foto hero + overlay navy solid 55%
- [ ] Kecilkan headline ±15%, titik akhir warna `--ice`
- [ ] Tambahkan blok info + 2 tombol di hero
- [ ] Tampilkan harga mulai di section layanan
- [ ] Cek tampilan mobile 375px