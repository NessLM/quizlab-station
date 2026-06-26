<?php
// =====================================================================
//  simpan_quiz.php — Endpoint penerima hasil quiz dari Unity (VR)
//
//  Unity (SummaryUI.cs) mengirim POST berisi:
//     nama   -> nama siswa  (Unity kirim "-" kalau kosong)
//     kelas  -> kelas siswa (Unity kirim "-" kalau kosong)
//     score  -> skor 0-100 (sudah dinormalisasi: round(benar/total*100))
//     benar  -> jumlah jawaban benar
//     total  -> jumlah total soal
//     lokasi -> nama scene = penanda tempat quiz
//               (VRLab / VRLabSimulation / VRLabSimulation_Padat)
//
//  Disimpan ke tabel hasil_quiz, lalu membalas JSON {status,pesan,id}.
// =====================================================================

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/config/koneksi.php';

function balas($status, $pesan, $id = null)
{
    echo json_encode(['status' => $status, 'pesan' => $pesan, 'id' => $id]);
    exit;
}

// 1) Hanya menerima POST (kalau dibuka di browser = GET, muncul pesan ini)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    balas('info', 'Endpoint hidup. Kirim data lewat POST (dari Unity).');
}

// 2) Ambil data dari Unity
$nama   = trim($_POST['nama']   ?? '');
$kelas  = trim($_POST['kelas']  ?? '');
$score  = $_POST['score']       ?? '';
$benar  = $_POST['benar']       ?? '0';
$total  = $_POST['total']       ?? '0';
$lokasi = trim($_POST['lokasi'] ?? '');

// 3) Default biar tidak kosong (Unity pun sudah kirim "-")
if ($nama   === '') $nama   = '-';
if ($kelas  === '') $kelas  = '-';
if ($lokasi === '') $lokasi = 'VRLab';

// 4) Angka wajib valid
if (!ctype_digit((string) $score)) {
    balas('gagal', 'Score harus berupa angka. Diterima: "' . $score . '"');
}
if (!ctype_digit((string) $benar)) $benar = '0';
if (!ctype_digit((string) $total)) $total = '0';
$score = (int) $score;
$benar = (int) $benar;
$total = (int) $total;

// 5) Simpan dengan prepared statement (aman dari SQL injection)
$stmt = mysqli_prepare(
    $koneksi,
    'INSERT INTO hasil_quiz (nama, kelas, score, benar, total, lokasi) VALUES (?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'ssiiis', $nama, $kelas, $score, $benar, $total, $lokasi);

if (!mysqli_stmt_execute($stmt)) {
    balas('gagal', 'Gagal menyimpan: ' . mysqli_stmt_error($stmt));
}

$id = mysqli_insert_id($koneksi);
mysqli_stmt_close($stmt);

// 6) Berhasil
balas('sukses', "Data tersimpan: nama=$nama, kelas=$kelas, score=$score, benar=$benar/$total, lokasi=$lokasi", $id);
