<?php
// =====================================================================
//  update.php — Endpoint API untuk menerima hasil quiz dari Unity (VR)
//
//  Game VR mengirim POST dengan field:
//     id    -> ID siswa (dari pendaftaran di index.php)
//     benar -> jumlah jawaban benar
//     salah -> jumlah jawaban salah
//     kunci -> kunci rahasia (harus cocok dengan KUNCI_RAHASIA)
//
//  Balasan selalu berupa JSON:  { "sukses": true/false, "pesan": "..." }
// =====================================================================

header('Content-Type: application/json; charset=utf-8');
require 'koneksi.php';

// Kunci rahasia: harus SAMA dengan yang ada di script Unity (QuizResultSender.cs).
// Gantilah dengan kunci Anda sendiri sebelum dipakai sungguhan.
const KUNCI_RAHASIA = 'quizlab-rahasia-2026';

// Fungsi bantu: kirim balasan JSON lalu hentikan program
function balas(bool $sukses, string $pesan): void
{
    echo json_encode(['sukses' => $sukses, 'pesan' => $pesan]);
    exit;
}

// 1) Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    balas(false, 'Endpoint ini hanya menerima POST.');
}

// 2) Periksa kunci rahasia (hash_equals = perbandingan aman)
$kunci = $_POST['kunci'] ?? '';
if (!hash_equals(KUNCI_RAHASIA, $kunci)) {
    http_response_code(403); // Forbidden
    balas(false, 'Kunci rahasia tidak valid.');
}

// 3) Validasi id, benar, salah harus berupa angka bulat non-negatif
$idMentah    = $_POST['id']    ?? '';
$benarMentah = $_POST['benar'] ?? '';
$salahMentah = $_POST['salah'] ?? '';

if (
    !ctype_digit((string) $idMentah)
    || !ctype_digit((string) $benarMentah)
    || !ctype_digit((string) $salahMentah)
) {
    http_response_code(400); // Bad Request
    balas(false, 'Data tidak valid: id, benar, dan salah harus berupa angka.');
}

$id    = (int) $idMentah;
$benar = (int) $benarMentah;
$salah = (int) $salahMentah;

// 4) Pastikan siswa dengan id tersebut benar-benar ada (prepared statement)
$cek = mysqli_prepare($koneksi, 'SELECT id FROM siswa WHERE id = ?');
mysqli_stmt_bind_param($cek, 'i', $id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
$ditemukan = mysqli_stmt_num_rows($cek) > 0;
mysqli_stmt_close($cek);

if (!$ditemukan) {
    http_response_code(404); // Not Found
    balas(false, 'Siswa dengan id ' . $id . ' tidak ditemukan.');
}

// 5) Simpan nilai benar & salah (prepared statement)
$stmt = mysqli_prepare($koneksi, 'UPDATE siswa SET benar = ?, salah = ? WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'iii', $benar, $salah, $id);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    http_response_code(500); // Server Error
    balas(false, 'Gagal menyimpan hasil ke database.');
}
mysqli_stmt_close($stmt);

// 6) Berhasil
balas(true, 'Hasil tersimpan untuk siswa id ' . $id . ' (benar=' . $benar . ', salah=' . $salah . ').');
