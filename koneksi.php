<?php
// =====================================================================
//  koneksi.php — Koneksi ke database MySQL (QuizLab Station)
//
//  Dipakai oleh semua file PHP lain lewat:  require 'koneksi.php';
//  Pengaturan ini sesuai bawaan Laragon (user root, password kosong).
// =====================================================================

$host = 'localhost';        // Server database (Laragon = localhost)
$user = 'root';             // Username MySQL bawaan Laragon
$pass = '';                 // Password kosong (bawaan Laragon)
$db   = 'quizlab_station';  // Nama database (lihat database.sql)

// Buat koneksi memakai mysqli (gaya prosedural)
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Hentikan program kalau koneksi gagal, tampilkan alasannya
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Pakai charset utf8mb4 agar huruf/karakter Unicode tersimpan benar
mysqli_set_charset($koneksi, 'utf8mb4');
