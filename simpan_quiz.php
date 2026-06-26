<?php
// =====================================================================
//  simpan_quiz.php — Endpoint penerima hasil quiz dari Unity (VR)
//
//  Unity (SummaryUI.cs) mengirim POST berisi:
//     nama     -> nama siswa
//     kelas    -> kelas siswa
//     score    -> skor quiz (angka)
//     kategori -> (opsional) 'cair_semipadat' / 'padat'
//                 kalau tidak dikirim, dianggap 'cair_semipadat'
//
//  Disimpan ke tabel hasil_quiz, lalu membalas JSON.
//  (Versi LOKAL: tanpa HTTPS, tanpa kunci rahasia, biar mudah diuji.)
// =====================================================================

header('Content-Type: application/json; charset=utf-8');
require 'koneksi.php';

function balas($status, $pesan, $id = null) {
    echo json_encode(['status' => $status, 'pesan' => $pesan, 'id' => $id]);
    exit;
}

// 1) Hanya menerima POST (kalau dibuka di browser = GET, muncul pesan ini)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    balas('info', 'Endpoint hidup. Kirim data lewat POST (dari Unity).');
}

// 2) Ambil data
$nama     = trim($_POST['nama']     ?? '');
$kelas    = trim($_POST['kelas']    ?? '');
$score    = $_POST['score']         ?? '';
$kategori = $_POST['kategori']      ?? 'cair_semipadat';

// 3) Validasi
if ($nama === '' || $kelas === '') {
    balas('gagal', 'Nama dan kelas tidak boleh kosong.');
}
if (!ctype_digit((string) $score)) {
    balas('gagal', 'Score harus berupa angka. Diterima: "' . $score . '"');
}
$score = (int) $score;

// kategori hanya boleh 2 nilai ini
if (!in_array($kategori, ['cair_semipadat', 'padat'], true)) {
    $kategori = 'cair_semipadat';
}

// 4) Simpan (prepared statement)
$stmt = mysqli_prepare($koneksi, 'INSERT INTO hasil_quiz (nama, kelas, score, kategori) VALUES (?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'ssis', $nama, $kelas, $score, $kategori);

if (!mysqli_stmt_execute($stmt)) {
    balas('gagal', 'Gagal menyimpan: ' . mysqli_stmt_error($stmt));
}
$id = mysqli_insert_id($koneksi);
mysqli_stmt_close($stmt);

// 5) Berhasil
balas('sukses', "Data tersimpan: nama=$nama, kelas=$kelas, score=$score, kategori=$kategori", $id);
