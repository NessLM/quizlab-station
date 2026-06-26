<?php
// =====================================================================
//  setup_admin.php — Buat akun admin default (JALANKAN SEKALI SAJA)
//  Buka di browser: http://localhost/quizlab-station/setup_admin.php
//  Setelah akun jadi, HAPUS file ini demi keamanan.
// =====================================================================

require 'koneksi.php';

// Akun default — GANTI password setelah berhasil login!
$username      = 'admin';
$passwordPlain = 'admin123';

// Pastikan tabel admin ada (jaga-jaga kalau database.sql belum di-import)
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  dibuat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Cek apakah akun sudah ada
$stmt = mysqli_prepare($koneksi, 'SELECT id FROM admin WHERE username = ?');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$sudahAda = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

header('Content-Type: text/plain; charset=utf-8');

if ($sudahAda) {
    echo "Akun admin '$username' sudah ada. Tidak dibuat ulang.\n";
} else {
    $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, 'INSERT INTO admin (username, password) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo "Akun admin berhasil dibuat!\n";
        echo "  Username : $username\n";
        echo "  Password : $passwordPlain\n";
    } else {
        echo "Gagal membuat akun admin: " . mysqli_error($koneksi) . "\n";
    }
}

echo "\nPENTING: hapus file setup_admin.php ini setelah selesai, lalu login di login.php.\n";
