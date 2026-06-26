<?php
// =====================================================================
//  data_quiz.php — Daftar hasil quiz, bisa difilter per LOKASI (nama scene)
//
//  Lokasi = nama scene Unity: VRLab / VRLabSimulation / VRLabSimulation_Padat
//  Dipakai oleh sidebar:
//     data_quiz.php?lokasi=VRLabSimulation        -> Cair & Semi Padat
//     data_quiz.php?lokasi=VRLabSimulation_Padat  -> Padat
//  Bisa juga ?lokasi=VRLab  atau  ?lokasi=semua (tampil semua)
// =====================================================================
require 'auth.php';      // wajib login
require 'koneksi.php';

// Label ramah untuk tiap lokasi
$LABEL_LOKASI = [
    'VRLab'                 => 'VR Lab (Umum)',
    'VRLabSimulation'       => 'Cair & Semi Padat',
    'VRLabSimulation_Padat' => 'Padat',
];

// Ambil filter dari URL
$lokasi = $_GET['lokasi'] ?? 'semua';

// Tentukan judul halaman + menu sidebar yang aktif
if ($lokasi === 'VRLabSimulation') {
    $judul_halaman = 'Data Quiz Cair & Semi Padat';
    $halaman_aktif = 'cair';
} elseif ($lokasi === 'VRLabSimulation_Padat') {
    $judul_halaman = 'Data Quiz Padat';
    $halaman_aktif = 'padat';
} elseif ($lokasi === 'VRLab') {
    $judul_halaman = 'Data Quiz VR Lab (Umum)';
    $halaman_aktif = '';
} else {
    $lokasi = 'semua';
    $judul_halaman = 'Semua Data Quiz';
    $halaman_aktif = '';
}

// Ambil data (prepared statement). 'semua' = tanpa filter.
if ($lokasi === 'semua') {
    $stmt = mysqli_prepare($koneksi, 'SELECT id, nama, kelas, score, lokasi, waktu FROM hasil_quiz ORDER BY waktu DESC, id DESC');
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT id, nama, kelas, score, lokasi, waktu FROM hasil_quiz WHERE lokasi = ? ORDER BY waktu DESC, id DESC');
    mysqli_stmt_bind_param($stmt, 's', $lokasi);
}
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$jumlah = mysqli_num_rows($data);

// Pilihan filter yang tampil sebagai tombol (pill) di atas tabel
$pilihanFilter = [
    'semua'                 => 'Semua',
    'VRLab'                 => 'VRLab',
    'VRLabSimulation'       => 'VRLabSimulation',
    'VRLabSimulation_Padat' => 'VRLabSimulation_Padat',
];

require '_header.php';
?>

<!-- Filter berdasarkan lokasi (nama scene Unity) -->
<div class="filter-bar">
  <?php foreach ($pilihanFilter as $nilai => $teks): ?>
    <a class="filter-pill <?= $lokasi === $nilai ? 'aktif' : '' ?>"
       href="data_quiz.php?lokasi=<?= urlencode($nilai) ?>"><?= htmlspecialchars($teks) ?></a>
  <?php endforeach; ?>
</div>

<div class="kartu">
  <?php if ($jumlah === 0): ?>
    <div class="kosong">
      <b>Belum ada data untuk filter ini.</b>
      Data muncul otomatis saat siswa menyelesaikan quiz di VR.
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>ID</th><th>Nama</th><th>Kelas</th><th>Score</th><th>Lokasi</th><th>Waktu</th></tr>
      </thead>
      <tbody>
        <?php while ($b = mysqli_fetch_assoc($data)):
            $lok = $b['lokasi'];
            $labelLok = $LABEL_LOKASI[$lok] ?? $lok;
            $kls = $lok === 'VRLabSimulation' ? 'cair' : ($lok === 'VRLabSimulation_Padat' ? 'padat' : '');
        ?>
          <tr>
            <td class="id">#<?= (int) $b['id'] ?></td>
            <td class="nama"><?= htmlspecialchars($b['nama']) ?></td>
            <td><?= htmlspecialchars($b['kelas']) ?></td>
            <td><span class="skor"><?= (int) $b['score'] ?></span></td>
            <td>
              <span class="lokasi-badge <?= $kls ?>"><?= htmlspecialchars($labelLok) ?></span>
              <div class="scene"><?= htmlspecialchars($lok) ?></div>
            </td>
            <td class="waktu"><?= htmlspecialchars(date('d M Y, H:i', strtotime($b['waktu']))) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
require '_footer.php';
