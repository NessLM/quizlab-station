<?php
// =====================================================================
//  simpan.php — Memproses form pendaftaran dari index.php
//
//  Alur:
//   1. Pastikan request memang POST.
//   2. Validasi: nama & kelas tidak boleh kosong.
//   3. INSERT siswa baru (benar=0, salah=0) dengan prepared statement.
//   4. Redirect kembali ke index.php membawa ?id=<id baru>.
// =====================================================================

require 'koneksi.php';

// 1) Hanya menerima metode POST. Kalau dibuka langsung, balik ke form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 2) Ambil & rapikan input, lalu validasi tidak kosong
$nama  = trim($_POST['nama']  ?? '');
$kelas = trim($_POST['kelas'] ?? '');

if ($nama === '' || $kelas === '') {
    // Kembali ke form dengan penanda error
    header('Location: index.php?error=kosong');
    exit;
}

// 3) Simpan siswa baru memakai prepared statement (aman dari SQL injection)
$stmt = mysqli_prepare(
    $koneksi,
    'INSERT INTO siswa (nama, kelas, benar, salah) VALUES (?, ?, 0, 0)'
);
mysqli_stmt_bind_param($stmt, 'ss', $nama, $kelas);

if (!mysqli_stmt_execute($stmt)) {
    // Kalau gagal menyimpan, tampilkan pesan singkat lalu berhenti
    mysqli_stmt_close($stmt);
    die('Gagal menyimpan data siswa. Silakan coba lagi.');
}

// Ambil ID (auto increment) milik baris yang baru saja dibuat
$idBaru = mysqli_insert_id($koneksi);
mysqli_stmt_close($stmt);

// 4) Redirect ke index.php sambil membawa ID baru untuk pesan sukses
header('Location: index.php?id=' . $idBaru);
exit;
