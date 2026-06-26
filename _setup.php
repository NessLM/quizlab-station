<?php
// Setup sementara (akan dihapus): import schema + seed admin
$sql = file_get_contents(__DIR__ . '/database.sql');
$c = mysqli_connect('localhost', 'root', '');
if (!$c) { exit('KONEKSI GAGAL: ' . mysqli_connect_error()); }
if (mysqli_multi_query($c, $sql)) { do {} while (mysqli_next_result($c)); }
if (mysqli_error($c)) { exit('SQL ERROR: ' . mysqli_error($c)); }
mysqli_select_db($c, 'quizlab_station');

// Seed admin/admin123 kalau belum ada
$u = 'admin';
$chk = mysqli_prepare($c, 'SELECT id FROM admin WHERE username = ?');
mysqli_stmt_bind_param($chk, 's', $u);
mysqli_stmt_execute($chk);
mysqli_stmt_store_result($chk);
$ada = mysqli_stmt_num_rows($chk) > 0;
mysqli_stmt_close($chk);
if (!$ada) {
    $h = password_hash('admin123', PASSWORD_DEFAULT);
    $ins = mysqli_prepare($c, 'INSERT INTO admin (username, password) VALUES (?, ?)');
    mysqli_stmt_bind_param($ins, 'ss', $u, $h);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    echo "ADMIN_DIBUAT ";
} else { echo "ADMIN_SUDAH_ADA "; }

$r = mysqli_query($c, 'SHOW TABLES');
echo "| TABEL: ";
while ($row = mysqli_fetch_row($r)) { echo $row[0] . ' '; }
mysqli_close($c);
