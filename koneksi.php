<?php
// =====================================================================
//  koneksi.php — Koneksi ke MySQL lokal (Laragon)
//  Dipakai oleh semua halaman lewat:  require 'koneksi.php';
// =====================================================================

$host = 'localhost';        // Laragon = localhost
$user = 'root';             // user bawaan Laragon
$pass = '';                 // password kosong (bawaan Laragon)
$db   = 'quizlab_station';  // nama database (lihat database.sql)

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error()
        . '<br>Pastikan MySQL di Laragon sudah nyala & database.sql sudah di-import.');
}

mysqli_set_charset($koneksi, 'utf8mb4');
