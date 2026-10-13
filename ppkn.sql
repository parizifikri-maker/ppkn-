-- ============================================
-- Skema database: Merdeka Belajar PPKn
-- Import file ini lewat phpMyAdmin / mysql CLI:
--   mysql -u root -p < schema.sql
-- ============================================

CREATE DATABASE IF NOT EXISTS ppkn_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ppkn_platform;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('siswa','guru') NOT NULL DEFAULT 'siswa',
  kelas VARCHAR(20) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE bab (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urutan INT NOT NULL,
  tag VARCHAR(20) NOT NULL,
  title VARCHAR(100) NOT NULL,
  sub VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE subbab (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bab_id INT NOT NULL,
  urutan INT NOT NULL,
  judul VARCHAR(150) NOT NULL,
  isi TEXT NOT NULL,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE soal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bab_id INT NOT NULL,
  pertanyaan TEXT NOT NULL,
  pilihan_a VARCHAR(255) NOT NULL,
  pilihan_b VARCHAR(255) NOT NULL,
  pilihan_c VARCHAR(255) NOT NULL,
  pilihan_d VARCHAR(255) NOT NULL,
  jawaban_benar ENUM('a','b','c','d') NOT NULL,
  penjelasan TEXT NOT NULL,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE studi_kasus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bab_id INT NOT NULL,
  pertanyaan TEXT NOT NULL,
  opsi_a VARCHAR(255) NOT NULL,
  opsi_b VARCHAR(255) NOT NULL,
  opsi_c VARCHAR(255) NOT NULL,
  opsi_d VARCHAR(255) NOT NULL,
  jawaban_benar ENUM('a','b','c','d') NOT NULL,
  penjelasan_benar TEXT NOT NULL,
  penjelasan_salah TEXT NOT NULL,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bab_id INT NOT NULL,
  status ENUM('progress','done') NOT NULL DEFAULT 'progress',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY user_bab (user_id, bab_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE kuis_hasil (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bab_id INT NOT NULL,
  skor INT NOT NULL,
  total_soal INT NOT NULL,
  poin INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Video pembelajaran dari YouTube per bab
CREATE TABLE materi_video (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bab_id INT NOT NULL,
  judul VARCHAR(180) NOT NULL,
  youtube_id VARCHAR(20) NOT NULL,
  deskripsi VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (bab_id) REFERENCES bab(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tugas dan kiriman siswa
CREATE TABLE tugas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(180) NOT NULL,
  instruksi TEXT NOT NULL,
  tenggat DATE DEFAULT NULL,
  dibuat_oleh INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pengumpulan_tugas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tugas_id INT NOT NULL,
  user_id INT NOT NULL,
  catatan TEXT DEFAULT NULL,
  nama_file VARCHAR(255) DEFAULT NULL,
  nilai TINYINT UNSIGNED DEFAULT NULL,
  komentar_guru TEXT DEFAULT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY tugas_siswa (tugas_id, user_id),
  FOREIGN KEY (tugas_id) REFERENCES tugas(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Forum diskusi kelas
CREATE TABLE forum_topik (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  judul VARCHAR(180) NOT NULL,
  isi TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE forum_balasan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  topik_id INT NOT NULL,
  user_id INT NOT NULL,
  isi TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (topik_id) REFERENCES forum_topik(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==== Seed: Bab & Subbab ====
INSERT INTO bab (urutan, tag, title, sub) VALUES
(1,'Bab 1','Pancasila','Dasar negara dan nilai kehidupan'),
(2,'Bab 2','UUD 1945','Konstitusi dan hak warga negara'),
(3,'Bab 3','Bhinneka Tunggal Ika','Keberagaman dalam persatuan'),
(4,'Bab 4','NKRI','Wilayah, persatuan, dan bela negara'),
(5,'Bab 5','Demokrasi','Musyawarah dan partisipasi warga');

INSERT INTO subbab (bab_id, urutan, judul, isi) VALUES
(1,1,'Pengertian Pancasila','Pancasila adalah dasar negara Indonesia yang terdiri dari lima sila, merangkum nilai-nilai luhur bangsa sebagai pedoman hidup bernegara.'),
(1,2,'Sejarah Perumusan Pancasila','Pancasila dirumuskan melalui sidang BPUPKI dan PPKI pada tahun 1945, melibatkan tokoh-tokoh bangsa dalam merumuskan dasar negara yang disepakati bersama.'),
(1,3,'Kedudukan Pancasila','Pancasila berkedudukan sebagai dasar negara sekaligus sumber dari segala sumber hukum yang berlaku di Indonesia.'),
(1,4,'Nilai-nilai Pancasila','Setiap sila mengandung nilai ketuhanan, kemanusiaan, persatuan, kerakyatan, dan keadilan yang menjadi pedoman berperilaku.'),
(2,1,'Pengertian Konstitusi','Konstitusi adalah hukum dasar tertulis yang mengatur penyelenggaraan pemerintahan dan hak-hak warga negara.'),
(2,2,'Fungsi UUD 1945','UUD 1945 berfungsi sebagai landasan hukum tertinggi yang mengikat seluruh peraturan perundang-undangan di bawahnya.'),
(2,3,'Struktur UUD 1945','UUD 1945 terdiri atas pembukaan, batang tubuh, dan pasal-pasal yang mengatur ketatanegaraan Indonesia.'),
(2,4,'Hak dan Kewajiban Warga Negara','Setiap warga negara memiliki hak yang dilindungi sekaligus kewajiban yang harus dijalankan demi kepentingan bersama.'),
(3,1,'Keberagaman Indonesia','Indonesia memiliki keberagaman suku, agama, budaya, dan bahasa yang menjadi kekayaan sekaligus identitas bangsa.'),
(3,2,'Sikap Menghargai Keberagaman','Menghargai perbedaan dilakukan dengan sikap toleransi, terbuka, dan mau bekerja sama tanpa memandang latar belakang.'),
(4,1,'Pengertian NKRI','Negara Kesatuan Republik Indonesia adalah bentuk negara yang berdaulat, meliputi seluruh wilayah dari Sabang sampai Merauke.'),
(4,2,'Persatuan dan Kesatuan','Persatuan dan kesatuan menjadi kunci menjaga keutuhan bangsa di tengah keberagaman wilayah dan masyarakat.'),
(4,3,'Bela Negara','Bela negara adalah sikap dan tindakan warga negara untuk menjaga kedaulatan, keutuhan wilayah, dan keselamatan bangsa.'),
(5,1,'Pengertian Demokrasi','Demokrasi adalah sistem pemerintahan di mana kekuasaan tertinggi berada di tangan rakyat.'),
(5,2,'Musyawarah dan Pemilu','Musyawarah mufakat dan pemilihan umum adalah dua wujud utama demokrasi dalam kehidupan berbangsa di Indonesia.');

-- ==== Seed: Soal kuis ====
INSERT INTO soal (bab_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, penjelasan) VALUES
(1,'Apa lambang sila pertama Pancasila?','Rantai','Pohon beringin','Bintang','Kepala banteng','c','Bintang melambangkan sila pertama, Ketuhanan Yang Maha Esa, sebagai cahaya kerohanian bagi bangsa Indonesia.'),
(1,'Lembaga apa yang merumuskan dasar negara sebelum kemerdekaan?','DPR','BPUPKI','MPR','KPU','b','BPUPKI (Badan Penyelidik Usaha Persiapan Kemerdekaan Indonesia) merumuskan dasar negara melalui sidang-sidangnya pada tahun 1945.'),
(2,'UUD 1945 berkedudukan sebagai...','Peraturan daerah','Hukum dasar tertinggi','Kebijakan sementara','Adat istiadat','b','UUD 1945 adalah hukum dasar tertinggi yang menjadi acuan seluruh peraturan perundang-undangan di Indonesia.'),
(3,'Sikap yang tepat menghadapi teman berbeda suku dan agama adalah...','Menjauhinya','Menghargai dan bekerja sama','Mengabaikannya','Memaksakan pendapat','b','Bhinneka Tunggal Ika mengajarkan untuk menghargai perbedaan dan tetap bekerja sama demi persatuan.'),
(4,'Sikap bela negara dapat ditunjukkan siswa dengan cara...','Mengabaikan aturan sekolah','Belajar sungguh-sungguh dan menaati aturan','Membuat kegaduhan','Tidak peduli lingkungan sekitar','b','Belajar sungguh-sungguh dan menaati aturan adalah wujud bela negara yang bisa dilakukan siswa sehari-hari.'),
(5,'Wujud demokrasi dalam kehidupan sekolah adalah...','Voting ketua kelas','Menghukum tanpa alasan','Diam saat rapat','Memilih sepihak','a','Pemilihan ketua kelas melalui musyawarah atau voting adalah contoh nyata demokrasi di lingkungan sekolah.');

-- Bank soal tambahan untuk latihan acak lintas materi
INSERT INTO soal (bab_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, penjelasan) VALUES
(1,'Sila ketiga Pancasila berbunyi...','Kemanusiaan yang Adil dan Beradab','Persatuan Indonesia','Keadilan Sosial','Kerakyatan yang Dipimpin oleh Hikmat Kebijaksanaan','b','Sila ketiga adalah Persatuan Indonesia.'),
(1,'Contoh sikap sesuai sila kedua di sekolah adalah...','Mengejek teman','Menolong teman yang kesulitan','Memilih teman berdasarkan suku','Melanggar tata tertib','b','Menolong teman menunjukkan nilai kemanusiaan yang adil dan beradab.'),
(2,'Hak warga negara harus digunakan dengan...','Semau sendiri','Bertanggung jawab','Melanggar aturan','Memaksa orang lain','b','Setiap hak selalu disertai tanggung jawab.'),
(2,'Contoh kewajiban siswa di sekolah adalah...','Mendapat nilai','Menggunakan fasilitas seenaknya','Menaati tata tertib','Meminta hadiah','c','Menaati tata tertib merupakan kewajiban siswa.'),
(3,'Semboyan Bhinneka Tunggal Ika berarti...','Berbeda-beda tetapi tetap satu','Sama dalam segala hal','Setiap daerah berdiri sendiri','Satu bahasa saja','a','Semboyan ini menegaskan persatuan dalam keberagaman.'),
(3,'Saat teman sedang beribadah, sikap yang tepat adalah...','Membuat gaduh','Menghormati dan memberi kesempatan','Mengajak bermain paksa','Mengejeknya','b','Toleransi agama berarti menghormati ibadah orang lain.'),
(4,'Menjaga kebersihan lingkungan sekolah merupakan bentuk...','Bela negara sehari-hari','Pelanggaran aturan','Sikap egois','Tidak peduli negara','a','Menjaga lingkungan adalah kontribusi positif bagi bangsa.'),
(4,'Bentuk negara Indonesia adalah...','Federasi','Kerajaan','Negara Kesatuan Republik Indonesia','Negara kota','c','Indonesia berbentuk Negara Kesatuan Republik Indonesia.'),
(5,'Keputusan hasil musyawarah sebaiknya...','Dijalankan dengan tanggung jawab','Ditolak bila tidak sesuai keinginan','Dibiarkan saja','Dipaksakan pada kelompok lain','a','Hasil musyawarah perlu dihormati dan dilaksanakan bersama.'),
(5,'Sikap saat menyampaikan pendapat dalam rapat adalah...','Berteriak dan memotong','Santun serta menghargai pendapat lain','Mengejek peserta lain','Tidak mau mendengar','b','Demokrasi membutuhkan sikap santun dan saling menghargai.');

INSERT INTO materi_video (bab_id, judul, youtube_id, deskripsi) VALUES
(1,'Mengenal nilai-nilai Pancasila','DcchKCSBL9w','Video pengantar Pancasila untuk pelajar.'),
(2,'UUD 1945 dan konstitusi','kJlqU2HpCbU','Memahami fungsi UUD 1945 dalam kehidupan bernegara.'),
(3,'Makna Bhinneka Tunggal Ika','DWrhuz5Hcts','Belajar menghargai keberagaman Indonesia.'),
(4,'Menjaga keutuhan NKRI','JPg8hUJKgkM','Pengantar persatuan dan bela negara.'),
(5,'Demokrasi di kehidupan sehari-hari','VyqLf5tM0Y0','Contoh demokrasi di sekolah dan rumah.');

-- ==== Seed: Studi kasus (muncul di Bab 3) ====
INSERT INTO studi_kasus (bab_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar, penjelasan_benar, penjelasan_salah) VALUES
(3,'Di kelas terdapat siswa yang berbeda suku dan agama. Beberapa siswa tidak mau bekerja kelompok dengannya. Apa sikap yang sebaiknya kamu lakukan?','Menjauhi siswa tersebut','Mengajaknya bergabung dan bekerja sama','Membiarkannya bekerja sendiri','Mengeluh kepada guru tanpa bertindak','b','Tepat. Mengajak bekerja sama menunjukkan sikap menghargai keberagaman sesuai nilai Bhinneka Tunggal Ika.','Coba pikirkan lagi — sikap terbaik adalah merangkul perbedaan, bukan menghindarinya.');

-- ==== Seed: Akun demo ====
-- guru / guru123  (role guru)
-- fikri / siswa123, andi / andi123, budi / budi123, siti / siti123, rina / rina123 (role siswa)
INSERT INTO users (name, username, password, role, kelas) VALUES
('Guru PPKn', 'guru', '$2b$12$YGjtp9sHByhrMF9sp5OgfO1nlNDshwnQTn1xEtPTcL2pckFviWLwS', 'guru', NULL),
('Fikri Ramadhan', 'fikri', '$2b$12$SuxQRnpahn17SE7IlVit7eEOC7AzToZLX06hOqpEwVzB6sTtr8GL.', 'siswa', 'VIII-C'),
('Andi', 'andi', '$2b$12$Ra5qp6UF11ZwjPiM3mE70OL4DqAAgQbwyiioF7nqcS91jSIWJoctm', 'siswa', 'VIII-A'),
('Budi', 'budi', '$2b$12$TtWojPf2HFEcOOp22OIRFOBrEOsn.eSfAu8cznJmmTNI0hp0t3SUO', 'siswa', 'VIII-B'),
('Siti', 'siti', '$2b$12$Dw.lAK2FHQvCqfYmzd8bk.yOJrbxm0OxGZBHnG91kxrcCw4DSePjS', 'siswa', 'VIII-A'),
('Rina', 'rina', '$2b$12$nYZT99QHmY72b7Ap9hKQ8uajELXkiqApLA5fRUjIQFTZrzr18i3ES', 'siswa', 'VIII-B');

-- Buka Bab 1 untuk semua akun siswa demo
INSERT INTO user_progress (user_id, bab_id, status)
SELECT id, 1, 'progress' FROM users WHERE role = 'siswa';

-- Riwayat poin dummy supaya leaderboard langsung terisi (bukan hasil kuis nyata)
INSERT INTO kuis_hasil (user_id, bab_id, skor, total_soal, poin) VALUES
((SELECT id FROM users WHERE username='andi'), 1, 5, 5, 950),
((SELECT id FROM users WHERE username='budi'), 1, 5, 5, 900),
((SELECT id FROM users WHERE username='siti'), 1, 5, 5, 870),
((SELECT id FROM users WHERE username='fikri'), 1, 5, 5, 850),
((SELECT id FROM users WHERE username='rina'), 1, 5, 5, 800);

-- Contoh tugas untuk langsung dicoba setelah impor database
INSERT INTO tugas (judul, instruksi, tenggat, dibuat_oleh) VALUES
('Refleksi nilai Pancasila', 'Tuliskan 3 contoh penerapan nilai Pancasila yang pernah kamu lakukan di rumah atau sekolah. Jelaskan mengapa tindakan tersebut penting.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), (SELECT id FROM users WHERE username='guru'));
