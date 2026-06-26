<?php
// =====================================================================
//  hapus.php — Menghapus data hasil_quiz yang dipilih (centang)
//  Hanya bisa: sudah login + POST + token CSRF valid.
// =====================================================================

require __DIR__ . '/includes/auth.php';      // wajib login (+ session & csrf)
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/includes/fungsi.php';

// Tujuan kembali (pertahankan filter lokasi & tanggal)
$kembali = $_POST['kembali'] ?? '';
[$lokasi, $tanggal] = array_pad(explode('|', $kembali, 2), 2, '');
if ($lokasi  === '') $lokasi  = 'semua';
if ($tanggal === '') $tanggal = 'semua';
$urlKembali = 'data_quiz.php?lokasi=' . urlencode($lokasi) . '&tanggal=' . urlencode($tanggal);

// Wajib POST + token CSRF valid (cegah penghapusan lewat link/orang iseng)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValid()) {
    header('Location: ' . $urlKembali);
    exit;
}

// Ambil id yang dicentang -> pastikan semua integer > 0
$ids = (array) ($_POST['ids'] ?? []);
$ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));

if (empty($ids)) {
    header('Location: ' . $urlKembali . '&pesan=kosong');
    exit;
}

// DELETE dengan prepared statement (placeholder sebanyak jumlah id)
$placeholder = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$stmt = mysqli_prepare($koneksi, "DELETE FROM hasil_quiz WHERE id IN ($placeholder)");
mysqli_stmt_bind_param($stmt, $types, ...$ids);
mysqli_stmt_execute($stmt);
$terhapus = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

header('Location: ' . $urlKembali . '&pesan=terhapus&n=' . $terhapus);
exit;
