<?php
// =====================================================================
//  config/koneksi.php — Koneksi ke MySQL (database quizlab_station)
//  Dipakai semua halaman:  require __DIR__ . '/config/koneksi.php';
// =====================================================================

$host = 'localhost';        // Laragon = localhost
$user = 'root';             // user bawaan Laragon
$pass = '';                 // password kosong (bawaan Laragon)
$db   = 'quizlab_station';  // nama database (lihat database/database.sql)

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error()
        . '<br>Pastikan MySQL di Laragon nyala & database.sql sudah di-import.');
}

mysqli_set_charset($koneksi, 'utf8mb4');
