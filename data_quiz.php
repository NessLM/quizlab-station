<?php
// =====================================================================
//  data_quiz.php — Daftar hasil quiz per kategori
//  Dipakai oleh 2 menu sidebar:
//     data_quiz.php?kategori=cair_semipadat
//     data_quiz.php?kategori=padat
// =====================================================================
require 'auth.php';      // wajib login
require 'koneksi.php';

// Ambil & validasi kategori dari URL (hanya boleh 2 nilai ini)
$kategori = $_GET['kategori'] ?? 'cair_semipadat';
$daftarKategori = [
    'cair_semipadat' => ['judul' => 'Data Quiz Cair & Semi Padat', 'aktif' => 'cair'],
    'padat'          => ['judul' => 'Data Quiz Padat',             'aktif' => 'padat'],
];
if (!isset($daftarKategori[$kategori])) {
    $kategori = 'cair_semipadat';   // jaga-jaga kalau URL diutak-atik
}

// Ambil data kategori tsb (prepared statement, terbaru di atas)
$stmt = mysqli_prepare(
    $koneksi,
    'SELECT id, nama, kelas, score, waktu FROM hasil_quiz WHERE kategori = ? ORDER BY waktu DESC, id DESC'
);
mysqli_stmt_bind_param($stmt, 's', $kategori);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$jumlah = mysqli_num_rows($data);

$judul_halaman = $daftarKategori[$kategori]['judul'];
$halaman_aktif = $daftarKategori[$kategori]['aktif'];
require '_header.php';
?>

<div class="kartu">
  <?php if ($jumlah === 0): ?>
    <div class="kosong">
      <b>Belum ada data untuk kategori ini.</b>
      Data muncul otomatis saat siswa menyelesaikan quiz
      <?= $kategori === 'padat' ? 'Padat' : 'Cair &amp; Semi Padat' ?> di VR.
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr><th>ID</th><th>Nama</th><th>Kelas</th><th>Score</th><th>Waktu</th></tr>
      </thead>
      <tbody>
        <?php while ($b = mysqli_fetch_assoc($data)): ?>
          <tr>
            <td class="id">#<?= (int) $b['id'] ?></td>
            <td class="nama"><?= htmlspecialchars($b['nama']) ?></td>
            <td><?= htmlspecialchars($b['kelas']) ?></td>
            <td><span class="skor"><?= (int) $b['score'] ?></span></td>
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
