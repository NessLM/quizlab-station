-- =====================================================================
--  QuizLab Station — SMKN 5 Pangkalpinang
--  Database panel admin + penampung hasil quiz dari Unity (VR)
-- =====================================================================
--  CARA IMPORT di phpMyAdmin:
--   1. Laragon -> Start All (Apache + MySQL).
--   2. Buka  http://localhost/phpmyadmin  -> tab "Import" -> pilih file ini.
--   3. Lalu buka  http://localhost/quizlab-station/database/setup_admin.php
--      (sekali saja) untuk membuat akun admin default.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS quizlab_station
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE quizlab_station;

-- Tabel akun admin (untuk login ke panel)
CREATE TABLE IF NOT EXISTS admin (
  id       INT          AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,                          -- ter-hash (password_hash / bcrypt)
  dibuat   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel hasil quiz (diisi otomatis oleh Unity lewat simpan_quiz.php)
CREATE TABLE IF NOT EXISTS hasil_quiz (
  id     INT          AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(100) NOT NULL,                          -- nama siswa
  kelas  VARCHAR(50)  NOT NULL,                          -- kelas siswa
  score  INT          NOT NULL DEFAULT 0,                -- skor 0-100 (sudah dinormalisasi)
  benar  INT          NOT NULL DEFAULT 0,                -- jumlah jawaban benar
  total  INT          NOT NULL DEFAULT 0,                -- jumlah total soal
  lokasi VARCHAR(50)  NOT NULL DEFAULT 'VRLab',          -- nama scene Unity:
                                                         --   VRLab / VRLabSimulation / VRLabSimulation_Padat
  waktu  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  UPGRADE data lama: kalau tabel hasil_quiz SUDAH ada tapi belum punya
--  kolom benar/total, jalankan ALTER berikut SEKALI (di tab SQL phpMyAdmin).
--  (Kalau pakai CREATE di atas dari nol, abaikan bagian ini.)
-- =====================================================================
-- ALTER TABLE hasil_quiz
--   ADD COLUMN benar INT NOT NULL DEFAULT 0 AFTER score,
--   ADD COLUMN total INT NOT NULL DEFAULT 0 AFTER benar;
