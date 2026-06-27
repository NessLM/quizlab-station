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
    // ---------- HOSTING (InfinityFree) ----------
    // ISI dari Control Panel InfinityFree -> "MySQL Databases":
    $host = 'sqlXXX.infinityfree.com';     // MySQL Host Name
    $user = 'epiz_XXXXXXX';                // MySQL User Name
    $pass = 'PASSWORD_AKUN_INFINITYFREE';  // password akun InfinityFree
    $db   = 'epiz_XXXXXXX_quizlab';        // MySQL Database Name
}

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error()
        . '<br>Cek kredensial di config/koneksi.php (atau pastikan MySQL nyala & database sudah di-import).');
}

mysqli_set_charset($koneksi, 'utf8mb4');
