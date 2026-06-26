-- =====================================================================
--  QuizLab Station — SMKN 5 Pangkalpinang
--  Database untuk panel admin + penampung hasil quiz dari Unity (VR)
-- =====================================================================
--  CARA IMPORT di phpMyAdmin:
--   1. Laragon -> Start All (Apache + MySQL).
--   2. Buka  http://localhost/phpmyadmin  -> tab "Import".
--   3. Pilih file ini -> klik "Go"/"Kirim".
--   4. Lalu buka  http://localhost/quizlab-station/setup_admin.php  (sekali saja)
--      untuk membuat akun admin default.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS quizlab_station
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE quizlab_station;

-- Tabel akun admin (untuk login ke panel)
CREATE TABLE IF NOT EXISTS admin (
  id       INT          AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,                          -- disimpan ter-hash (password_hash)
  dibuat   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel hasil quiz (diisi otomatis oleh Unity lewat simpan_quiz.php)
CREATE TABLE IF NOT EXISTS hasil_quiz (
  id     INT          AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(100) NOT NULL,                          -- nama siswa
  kelas  VARCHAR(50)  NOT NULL,                          -- kelas siswa
  score  INT          NOT NULL DEFAULT 0,                -- skor quiz
  lokasi VARCHAR(50)  NOT NULL DEFAULT 'VRLab',          -- nama scene Unity:
                                                         --   VRLab / VRLabSimulation / VRLabSimulation_Padat
  waktu  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (Opsional) kalau ada sisa tabel lama dari versi sebelumnya, boleh dibuang:
-- DROP TABLE IF EXISTS siswa;
