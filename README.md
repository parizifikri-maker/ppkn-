# Merdeka Belajar PPKn (versi PHP + MySQL)

Prototipe platform belajar PPKn interaktif: materi per bab, studi kasus, kuis dengan poin, leaderboard, dashboard siswa, dan dashboard guru. Dibangun dengan PHP native (tanpa framework) dan MySQL, cocok untuk konsep skripsi/jurnal yang butuh sistem berjalan nyata (bukan sekadar mockup).

## Kebutuhan
- PHP 8.0 ke atas (dengan ekstensi `pdo_mysql`)
- MySQL atau MariaDB
- Web server (bawaan XAMPP/Laragon, atau `php -S`)

## Instalasi

1. **Buat database & impor skema**
   ```bash
   mysql -u root -p < schema.sql
   ```
   Perintah ini otomatis membuat database `ppkn_platform`, seluruh tabel, dan data contoh (5 bab, subbab, soal, studi kasus, akun demo).

2. **Atur koneksi database**
   Buka `config.php`, sesuaikan `$DB_HOST`, `$DB_NAME`, `$DB_USER`, `$DB_PASS` dengan pengaturan MySQL kamu.

3. **Jalankan**
   - **XAMPP/Laragon**: salin folder `ppkn-php` ke `htdocs`/`www`, lalu buka `http://localhost/ppkn-php/`.
   - **PHP built-in server** (untuk uji cepat):
     ```bash
     cd ppkn-php
     php -S localhost:8000
     ```
     lalu buka `http://localhost:8000/`.

## Akun demo (dari schema.sql)

| Peran | Username | Password |
|---|---|---|
| Guru | `guru` | `guru123` |
| Siswa | `fikri` | `siswa123` |
| Siswa | `andi` | `andi123` |
| Siswa | `budi` | `budi123` |
| Siswa | `siti` | `siti123` |
| Siswa | `rina` | `rina123` |

Akun `andi`, `budi`, `siti`, `rina` sudah punya riwayat poin dummy supaya leaderboard langsung terisi. Kamu juga bisa mendaftar akun baru lewat halaman **Daftar**.

## Struktur folder

```
ppkn-php/
├── config.php              # koneksi database
├── schema.sql               # struktur tabel + data contoh
├── index.php                 # beranda (publik)
├── login.php / register.php / logout.php
├── dashboard.php              # dashboard siswa
├── materi.php                 # daftar bab
├── materi_detail.php          # isi materi + studi kasus
├── kuis.php                   # kuis interaktif + poin
├── leaderboard.php
├── includes/
│   ├── bootstrap.php          # gabungan config + functions
│   ├── functions.php          # logika progres, poin, dsb
│   ├── header.php / footer.php
│   └── sidebar_siswa.php
├── guru/
│   ├── dashboard.php           # progres siswa & daftar materi
│   ├── tambah_bab.php
│   ├── tambah_subbab.php
│   └── tambah_soal.php
└── assets/style.css
```

## Fitur utama

- **Progres berurutan**: setiap siswa baru otomatis membuka Bab 1. Menyelesaikan kuis suatu bab akan menandainya "selesai" dan otomatis membuka bab berikutnya (tabel `user_progress`).
- **Poin & leaderboard**: setiap kuis selesai disimpan di `kuis_hasil` (skor, total soal, poin = jawaban benar × 10). Leaderboard menjumlahkan seluruh poin per siswa.
- **Studi kasus**: contoh interaktif ada di Bab 3, disimpan di tabel `studi_kasus`, jawaban siswa disimpan sementara di session.
- **Dashboard guru**: menampilkan progres tiap siswa dan menyediakan form sederhana untuk menambah bab, subbab, dan soal baru — tanpa perlu menyentuh database langsung.
- **Latihan acak 10 menit**: 10 soal diambil acak dari semua materi PPKn dan waktu tersisa terlihat di layar.
- **Video belajar**: video YouTube per bab ditampilkan langsung di dalam platform.
- **Misi Pancasila**: game lari mini berbasis browser untuk jeda belajar yang tetap bertema nilai baik.
- **Tugas & penilaian**: siswa dapat mengumpulkan jawaban/lampiran; guru menerbitkan tugas, membuka lampiran, memberi nilai, dan komentar.
- **Forum kelas**: siswa dan guru dapat membuat topik dan saling membalas dengan akun masing-masing.

> Jika database sebelumnya sudah pernah diimpor, impor ulang `schema.sql` pada database baru agar tabel fitur baru (video, tugas, dan forum) ikut dibuat.

## Catatan pengembangan lanjutan
Ini adalah prototipe fungsional, bukan produk siap-produksi. Sebelum dipakai di luar lingkungan uji coba lokal, pertimbangkan menambahkan: proteksi CSRF pada form, validasi input yang lebih ketat, fitur edit/hapus materi & soal, serta halaman reset password.
