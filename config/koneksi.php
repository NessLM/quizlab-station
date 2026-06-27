<?php
// =====================================================================
//  config/koneksi.php — Koneksi MySQL (otomatis pilih LOKAL vs HOSTING)
//   - Diakses dari localhost / LAN / CLI -> MySQL Laragon (lokal)
//   - Diakses dari domain hosting        -> MySQL InfinityFree
//  Dipakai semua halaman:  require __DIR__ . '/config/koneksi.php';
// =====================================================================

$namaHost = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? '');

$isLokal = $namaHost === ''                       // dijalankan via CLI
    || $namaHost === 'localhost'
    || $namaHost === '127.0.0.1'
    || str_starts_with($namaHost, '192.168.')     // akses lewat IP LAN (mis. dari Quest)
    || str_starts_with($namaHost, '10.');

if ($isLokal) {
    // ---------- LOKAL (Laragon) ----------
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'quizlab_station';
} else {
    // ---------- HOSTING (AlwaysData, akun: smkn) ----------
    $host = 'mysql-quizlab-station.alwaysdata.net';  // host MySQL AlwaysData
    $user = 'quizlab-station';                       // user MySQL (= nama akun)
    $pass = 'pangkalpinanganakkerenSMKVR';        // <-- ISI password user MySQL 'smkn'
    $db   = 'quizlab-station_quizlab-smkn5';               // database yang barusan kamu buat
}

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error()
        . '<br>Cek kredensial di config/koneksi.php (atau pastikan MySQL nyala & database sudah di-import).');
}

mysqli_set_charset($koneksi, 'utf8mb4');
