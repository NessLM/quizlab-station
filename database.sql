-- =====================================================================
--  QuizLab Station — SMKN 5 Pangkalpinang
--  Skema database untuk dashboard pendamping game VR edukasi
-- =====================================================================
--
--  CARA MENJALANKAN di phpMyAdmin:
--  1. Pastikan Laragon menyala (Apache + MySQL "Start All").
--  2. Buka browser ke  http://localhost/phpmyadmin
--  3. Klik tab "Import" di menu atas.
--  4. Klik "Choose File" / "Pilih File", pilih file database.sql ini.
--  5. Scroll ke bawah lalu klik tombol "Import" / "Kirim".
--
--  Alternatif (tanpa import file):
--  - Klik tab "SQL", lalu salin–tempel seluruh isi file ini,
--    kemudian klik "Go" / "Kirim".
--
--  Setelah berhasil, akan terbentuk database "quizlab_station"
--  beserta tabel "siswa".
-- =====================================================================

-- 1) Buat database (kalau belum ada) dengan charset Unicode penuh
CREATE DATABASE IF NOT EXISTS quizlab_station
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- 2) Pakai database tersebut untuk perintah berikutnya
USE quizlab_station;

-- 3) Buat tabel siswa
CREATE TABLE IF NOT EXISTS siswa (
  id     INT          AUTO_INCREMENT PRIMARY KEY,        -- ID unik tiap siswa
  nama   VARCHAR(100) NOT NULL,                          -- Nama lengkap siswa
  kelas  VARCHAR(50)  NOT NULL,                          -- Kelas, mis. "XI RPL 1"
  benar  INT          NOT NULL DEFAULT 0,                -- Jumlah jawaban benar (diisi dari VR)
  salah  INT          NOT NULL DEFAULT 0,                -- Jumlah jawaban salah (diisi dari VR)
  waktu  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP -- Waktu pendaftaran otomatis
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
