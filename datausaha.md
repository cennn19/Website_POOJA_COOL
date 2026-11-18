# Data Usaha & Struktur Layanan

> Data asli dari Pooja Cool Prima. Kerjakan setelah poin 5 di `PERBAIKAN-ALUR.md` (data usaha terpusat di `config/usaha.php`).
> Nilai yang belum diketahui ditandai `TODO`. Jangan mengarang angka.
> Laporkan file, migrasi, dan seeder yang berubah.

---

## 1. Data usaha

Isi `config/usaha.php` (atau tabel pengaturan yang sudah dibuat):

| Kunci | Nilai |
|---|---|
| `nama` | Pooja Cool Prima |
| `telepon` | 0852-3285-5466 |
| `whatsapp` | `6285232855466` (format untuk link wa.me) — TODO: pastikan nomor ini aktif di WhatsApp |
| `email` | idhwanmuhammad1703@gmail.com |
| `alamat` | Dusun Grogol RT 04 RW 02, Desa Tritunggal, Kecamatan Babat, Kabupaten Lamongan |
| `area_layanan` | TODO: tunggu konfirmasi (contoh: "Babat dan sekitarnya, Kabupaten Lamongan") |
| `jam_operasional` | TODO |

- Ganti semua teks "Malang" yang merujuk ke lokasi usaha atau area layanan dengan data di atas.
- Halaman Kontak dan footer menampilkan telepon, email, dan alamat sebagai teks yang bisa diseleksi.

---

## 2. Struktur layanan: kategori + jenis pekerjaan

Layanan lama (Maintenance, Perbaikan, Pemasangan) diganti dengan **5 kategori** berisi **16 jenis pekerjaan**.

### 2.1 Database
- Tabel baru `kategori_layanan`: `id`, `nama`, `slug`, `urutan`, `catatan_harga` (text, nullable), `aktif`.
- Tabel `layanan` (jenis pekerjaan) ditambah:
  - `kategori_layanan_id` (FK)
  - `satuan` (string): `unit` atau `meter`
  - `harga_mulai` (boolean), kolom dari `PERBAIKAN-ALUR.md` poin 4
  - `urutan` (int)
- Pesanan yang sudah ada tetap valid. Buat migrasi data: layanan lama dipetakan ke jenis pekerjaan baru atau ditandai nonaktif, jangan dihapus.

### 2.2 Data seeder

`harga_mulai = true` berarti harga tampil "Mulai dari Rp …" karena bisa berubah sesuai lokasi atau tingkat kerusakan.

| Kategori | Jenis pekerjaan | Satuan | Harga | `harga_mulai` |
|---|---|---|---|---|
| **Cleaning AC** | AC Split 0,5–1 PK | unit | TODO | false |
| | AC Split 1,5–2 PK | unit | TODO | false |
| | AC Split/Cassette/Standing Floor | unit | TODO | false |
| **Pasang AC (Lokasi Standar)** | Pemasangan AC Split 0,5–1 PK | unit | TODO | true |
| | Pemasangan AC Split 1,5–2 PK | unit | TODO | true |
| | Pemasangan Standing Floor-Cassette | unit | TODO | true |
| | Bongkar AC Split | unit | TODO | true |
| | Instalasi pipa lebih dari 3 meter | meter | TODO | false |
| **Instalasi Listrik** | Instalasi listrik AC | unit | TODO | true |
| **Tambah Freon** | Split 0,5–1 PK | unit | TODO | false |
| | Split 1,5–2 PK | unit | TODO | false |
| | Cassette/Standing Floor | unit | TODO | false |
| | Isi Freon + Flushing | unit | TODO | false |
| **Service dan Perbaikan AC** | Reparasi ringan (service modul ringan, ganti sensor, thermistor, dan lainnya) | unit | TODO | true |
| | Ganti kapasitor/overload | unit | TODO | true |
| | Vacuum + Flushing instalasi unit lebih dari 1,5 PK | unit | TODO | true |

Selama harga masih TODO, seeder mengisi harga `0` dan layanan tersebut **tidak tampil** di website (anggap nonaktif sampai admin mengisi harga). Admin mengisi harga lewat halaman Kelola Layanan & Harga.

### 2.3 Catatan harga per kategori (`catatan_harga`)

- **Pasang AC (Lokasi Standar):** Harga untuk lokasi standar dengan pipa sampai 3 meter. Kelebihan pipa dihitung per meter. Tarif dapat menyesuaikan kondisi lokasi pemasangan dan dikonfirmasi teknisi sebelum pekerjaan dimulai.
- **Instalasi Listrik:** Tarif menyesuaikan kondisi instalasi di lokasi dan dikonfirmasi teknisi sebelum pekerjaan dimulai.
- **Service dan Perbaikan AC:** Tarif menyesuaikan tingkat kerusakan. Penggantian sparepart dibayar terpisah setelah dicek teknisi dan disetujui pelanggan.
- **Cleaning AC** dan **Tambah Freon:** tidak ada catatan, harga pasti per unit.

Catatan ini menggantikan isian `catatan_harga` per layanan dari `PERBAIKAN-ALUR.md` poin 4.

---

## 3. Tampilan

### Landing page (section layanan)
- Tampilkan per kategori. Setiap kategori menampilkan daftar jenis pekerjaan dan harganya dalam bentuk **daftar harga** (nama pekerjaan rata kiri, harga rata kanan), bukan kartu terpisah per pekerjaan.
- Harga `harga_mulai` ditulis "Mulai dari Rp …". Satuan `meter` ditulis "Rp … / meter".
- `catatan_harga` tampil kecil di bawah daftar kategori yang bersangkutan.
- Ikuti token warna dan aturan di `DESIGNUIUX-REVISI.md`.

### Form pemesanan
- Pilih **kategori** dulu, lalu **jenis pekerjaan** (dropdown kedua terisi sesuai kategori).
- Label jumlah mengikuti satuan: "Jumlah unit" atau "Panjang pipa (meter)".
- Total biaya = harga × jumlah + kode unik, seperti sebelumnya.
- Jika `harga_mulai = true`, ringkasan pembayaran menampilkan catatan bahwa biaya bisa bertambah di lokasi.

### Admin: Kelola Layanan & Harga
- Daftar dikelompokkan per kategori.
- Admin bisa: mengubah harga, satuan, `harga_mulai`, status aktif, dan urutan jenis pekerjaan; mengubah `catatan_harga` kategori; menambah jenis pekerjaan baru di kategori yang ada.
- Layanan dengan harga 0 diberi tanda "Harga belum diisi".

---

## 4. Tes
- Seeder menghasilkan 5 kategori dan 16 jenis pekerjaan.
- Layanan berharga 0 tidak muncul di landing page dan form, dan pesanan untuk layanan itu ditolak backend.
- Dropdown jenis pekerjaan hanya berisi pekerjaan dari kategori yang dipilih, dan backend menolak kombinasi kategori/pekerjaan yang tidak cocok.
- Pesanan "Instalasi pipa lebih dari 3 meter" dengan jumlah 4 menghasilkan total = harga per meter × 4 + kode unik.
- Pesanan lama tetap bisa dibuka di admin dan halaman Cek Status.