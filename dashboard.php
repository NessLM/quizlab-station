<?php
// =====================================================================
//  dashboard.php — Halaman utama setelah login (ringkasan)
// =====================================================================
require 'auth.php';      // wajib login
require 'koneksi.php';

// Fungsi bantu: hitung jumlah baris hasil_quiz (opsional difilter lokasi)
function hitung($koneksi, $lokasi = null)
{
    if ($lokasi === null) {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) AS n FROM hasil_quiz');
    } else {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) AS n FROM hasil_quiz WHERE lokasi = ?');
        mysqli_stmt_bind_param($stmt, 's', $lokasi);
    }
    mysqli_stmt_execute($stmt);
    $n = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['n'];
    mysqli_stmt_close($stmt);
    return $n;
}

// Label ramah untuk tiap lokasi (nama scene Unity)
$LABEL_LOKASI = [
    'VRLab'                 => 'VR Lab (Umum)',
    'VRLabSimulation'       => 'Cair & Semi Padat',
    'VRLabSimulation_Padat' => 'Padat',
];

$totalSemua = hitung($koneksi);
$totalVRLab = hitung($koneksi, 'VRLab');
$totalCair  = hitung($koneksi, 'VRLabSimulation');
$totalPadat = hitung($koneksi, 'VRLabSimulation_Padat');

// Ambil 8 data terbaru
$stmt = mysqli_prepare($koneksi, 'SELECT nama, kelas, score, lokasi, waktu FROM hasil_quiz ORDER BY waktu DESC, id DESC LIMIT 8');
mysqli_stmt_execute($stmt);
$terbaru = mysqli_stmt_get_result($stmt);

$judul_halaman = 'Dashboard';
$halaman_aktif = 'dashboard';
require '_header.php';
?>

<div class="stat-grid">
  <div class="stat">
    <div class="angka"><?= $totalSemua ?></div>
    <div class="ket">Total Data Quiz Masuk</div>
  </div>
  <div class="stat">
    <div class="angka"><?= $totalCair ?></div>
    <div class="ket">Cair &amp; Semi Padat<br><small>VRLabSimulation</small></div>
  </div>
  <div class="stat magenta">
    <div class="angka"><?= $totalPadat ?></div>
    <div class="ket">Padat<br><small>VRLabSimulation_Padat</small></div>
  </div>
  <div class="stat">
    <div class="angka"><?= $totalVRLab ?></div>
    <div class="ket">VR Lab (Umum)<br><small>VRLab</small></div>
  </div>
</div>

<div class="kartu">
  <?php if (mysqli_num_rows($terbaru) === 0): ?>
    <div class="kosong">
      <b>Belum ada data quiz masuk.</b>
      Data akan muncul otomatis setelah siswa menyelesaikan quiz di VR.
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>Nama</th><th>Kelas</th><th>Score</th><th>Lokasi</th><th>Waktu</th></tr>
      </thead>
      <tbody>
        <?php while ($b = mysqli_fetch_assoc($terbaru)):
            $lok = $b['lokasi'];
            $labelLok = $LABEL_LOKASI[$lok] ?? $lok;
            $kls = $lok === 'VRLabSimulation' ? 'cair' : ($lok === 'VRLabSimulation_Padat' ? 'padat' : '');
        ?>
          <tr>
            <td class="nama"><?= htmlspecialchars($b['nama']) ?></td>
            <td><?= htmlspecialchars($b['kelas']) ?></td>
            <td><span class="skor"><?= (int) $b['score'] ?></span></td>
            <td><span class="lokasi-badge <?= $kls ?>"><?= htmlspecialchars($labelLok) ?></span></td>
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
