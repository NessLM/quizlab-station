-- =====================================================================
--  database_hosting.sql — Untuk IMPORT di phpMyAdmin HOSTING (AlwaysData)
-- ---------------------------------------------------------------------
--  Database dibuat lewat panel AlwaysData, bukan lewat SQL, jadi file ini
--  TANPA "CREATE DATABASE" / "USE" — hanya tabelnya saja.
--
--  Cara pakai (AlwaysData):
--   1. Panel -> Databases -> MySQL -> buat database (mis. "quizlab") + user.
--   2. Panel -> Databases -> MySQL -> phpMyAdmin -> pilih DB NAMAAKUN_quizlab.
--   3. Tab "Import" -> pilih file ini -> "Go".
--   4. Lalu buka  https://NAMAAKUN.alwaysdata.net/database/setup_admin.php
--      (sekali) untuk membuat akun admin, kemudian HAPUS file setup_admin.php.
-- =====================================================================

CREATE TABLE IF NOT EXISTS admin (
  id       INT          AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,                 -- ter-hash (password_hash / bcrypt)
  dibuat   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hasil_quiz (
  id     INT          AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(100) NOT NULL,
  kelas  VARCHAR(50)  NOT NULL,
  score  INT          NOT NULL DEFAULT 0,          -- skor 0-100 (sudah dinormalisasi)
  benar  INT          NOT NULL DEFAULT 0,          -- jumlah jawaban benar
  total  INT          NOT NULL DEFAULT 0,          -- jumlah total soal
  lokasi VARCHAR(50)  NOT NULL DEFAULT 'VRLab',    -- VRLab / VRLabSimulation / VRLabSimulation_Padat
  waktu  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
