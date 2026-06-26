<?php
// =====================================================================
//  dashboard.php — Halaman utama setelah login (ringkasan)
// =====================================================================
require 'auth.php';      // wajib login
require 'koneksi.php';

// Fungsi bantu: hitung jumlah baris hasil_quiz (opsional difilter kategori)
function hitung($koneksi, $kategori = null) {
    if ($kategori === null) {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) AS n FROM hasil_quiz');
    } else {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) AS n FROM hasil_quiz WHERE kategori = ?');
        mysqli_stmt_bind_param($stmt, 's', $kategori);
    }
    mysqli_stmt_execute($stmt);
    $n = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['n'];
    mysqli_stmt_close($stmt);
    return $n;
}

$totalSemua = hitung($koneksi);
$totalCair  = hitung($koneksi, 'cair_semipadat');
$totalPadat = hitung($koneksi, 'padat');

// Ambil 5 data terbaru
$stmt = mysqli_prepare($koneksi, 'SELECT nama, kelas, score, kategori, waktu FROM hasil_quiz ORDER BY waktu DESC, id DESC LIMIT 5');
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
    <div class="ket">Data Quiz Cair &amp; Semi Padat</div>
  </div>
  <div class="stat magenta">
    <div class="angka"><?= $totalPadat ?></div>
    <div class="ket">Data Quiz Padat</div>
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
        <tr><th>Nama</th><th>Kelas</th><th>Score</th><th>Kategori</th><th>Waktu</th></tr>
      </thead>
      <tbody>
        <?php while ($b = mysqli_fetch_assoc($terbaru)): ?>
          <tr>
            <td class="nama"><?= htmlspecialchars($b['nama']) ?></td>
            <td><?= htmlspecialchars($b['kelas']) ?></td>
            <td><span class="skor"><?= (int) $b['score'] ?></span></td>
            <td><?= $b['kategori'] === 'padat' ? 'Padat' : 'Cair &amp; Semi Padat' ?></td>
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
