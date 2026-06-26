<?php
// =====================================================================
//  data_quiz.php — Daftar hasil quiz
//  Fitur: filter per LOKASI (nama scene) + filter TANGGAL (default hari ini)
//         + centang untuk hapus (pilih / pilih semua, dengan konfirmasi)
// =====================================================================
require __DIR__ . '/includes/auth.php';      // wajib login (+ csrf)
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/includes/fungsi.php';

// ---------- Filter lokasi ----------
$lokasi = $_GET['lokasi'] ?? 'semua';
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

// ---------- Filter tanggal (default: hari ini) ----------
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
if ($tanggal !== 'semua' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = date('Y-m-d');           // jaga-jaga kalau URL diutak-atik
}

// ---------- Bangun query dinamis (prepared statement) ----------
$where = [];
$params = [];
$types = '';
if ($lokasi !== 'semua') { $where[] = 'lokasi = ?';      $params[] = $lokasi;  $types .= 's'; }
if ($tanggal !== 'semua') { $where[] = 'DATE(waktu) = ?'; $params[] = $tanggal; $types .= 's'; }

$sql = 'SELECT id, nama, kelas, score, benar, total, lokasi, waktu FROM hasil_quiz';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY waktu DESC, id DESC';

$stmt = mysqli_prepare($koneksi, $sql);
if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$jumlah = mysqli_num_rows($data);

// Pilihan tombol filter lokasi
$pilihanFilter = [
    'semua'                 => 'Semua',
    'VRLab'                 => 'VRLab',
    'VRLabSimulation'       => 'VRLabSimulation',
    'VRLabSimulation_Padat' => 'VRLabSimulation_Padat',
];

// Banner status setelah hapus
$banner = '';
if (($_GET['pesan'] ?? '') === 'terhapus') {
    $n = (int) ($_GET['n'] ?? 0);
    $banner = '<div class="banner banner-ok">' . $n . ' data berhasil dihapus.</div>';
} elseif (($_GET['pesan'] ?? '') === 'kosong') {
    $banner = '<div class="banner banner-info">Tidak ada data yang dicentang untuk dihapus.</div>';
}

// Link filter lokasi yang tetap membawa tanggal aktif
function urlLokasi($lok, $tanggal)
{
    return 'data_quiz.php?lokasi=' . urlencode($lok) . '&tanggal=' . urlencode($tanggal);
}

require __DIR__ . '/includes/header.php';
?>

<?= $banner ?>

<!-- Baris atas: filter lokasi (kiri) + filter tanggal (kanan) -->
<div class="baris-filter">
  <div class="pills">
    <?php foreach ($pilihanFilter as $nilai => $teks): ?>
      <a class="filter-pill <?= $lokasi === $nilai ? 'aktif' : '' ?>"
         href="<?= urlLokasi($nilai, $tanggal) ?>"><?= htmlspecialchars($teks) ?></a>
    <?php endforeach; ?>
  </div>

  <form class="filter-tanggal" method="get" action="data_quiz.php">
    <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
    <label for="tgl">Tanggal</label>
    <input type="date" id="tgl" name="tanggal" value="<?= $tanggal === 'semua' ? '' : htmlspecialchars($tanggal) ?>">
    <button type="submit" class="btn btn-oranye">Terapkan</button>
    <a class="btn" href="data_quiz.php?lokasi=<?= urlencode($lokasi) ?>&tanggal=semua">Semua tanggal</a>
  </form>
</div>

<!-- Form hapus membungkus tabel (konfirmasi pakai modal UI, lihat bawah) -->
<form method="post" action="hapus.php" id="formHapus">
  <?= csrfField() ?>
  <input type="hidden" name="kembali" value="<?= htmlspecialchars($lokasi . '|' . $tanggal) ?>">

  <div class="aksi">
    <button type="submit" class="btn btn-danger">&#128465; Hapus Terpilih</button>
    <span class="total">Menampilkan <b><?= (int) $jumlah ?></b> data
      <?= $tanggal === 'semua' ? '(semua tanggal)' : '(tanggal ' . htmlspecialchars($tanggal) . ')' ?></span>
  </div>

  <div class="kartu">
    <?php if ($jumlah === 0): ?>
      <div class="kosong">
        <b>Tidak ada data untuk filter ini.</b>
        <?= $tanggal === 'semua'
              ? 'Data muncul otomatis saat siswa menyelesaikan quiz di VR.'
              : 'Coba klik "Semua tanggal" atau pilih tanggal lain.' ?>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th class="kol-cek"><input type="checkbox" class="cek" id="cekSemua" title="Pilih semua"></th>
            <th>ID</th><th>Nama</th><th>Kelas</th><th>Score</th><th>Benar</th><th>Lokasi</th><th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($b = mysqli_fetch_assoc($data)):
              $lok = $b['lokasi'];
          ?>
            <tr>
              <td class="kol-cek"><input type="checkbox" class="cek pilih" name="ids[]" value="<?= (int) $b['id'] ?>"></td>
              <td class="id">#<?= (int) $b['id'] ?></td>
              <td class="nama"><?= htmlspecialchars($b['nama']) ?></td>
              <td><?= htmlspecialchars($b['kelas']) ?></td>
              <td><span class="skor"><?= (int) $b['score'] ?></span></td>
              <td class="benar"><?= (int) $b['benar'] ?><small>/<?= (int) $b['total'] ?></small></td>
              <td>
                <span class="lokasi-badge <?= kelasLokasi($lok) ?>"><?= htmlspecialchars(labelLokasi($lok)) ?></span>
                <div class="scene"><?= htmlspecialchars($lok) ?></div>
              </td>
              <td class="waktu"><?= htmlspecialchars(date('d M Y, H:i', strtotime($b['waktu']))) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</form>

<!-- Modal konfirmasi hapus (pengganti alert bawaan browser) -->
<div class="modal-overlay" id="modalHapus" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true">
    <div class="modal-ikon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
        <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
      </svg>
    </div>
    <h3 id="modalJudul">Hapus data?</h3>
    <p id="modalPesan">Data yang dihapus tidak bisa dikembalikan. Lanjutkan?</p>
    <div class="modal-aksi">
      <button type="button" class="btn" id="batalHapus">Batal</button>
      <button type="button" class="btn btn-danger" id="yaHapus">Ya, Hapus</button>
    </div>
  </div>
</div>

<script>
  (function () {
    const form     = document.getElementById('formHapus');
    const overlay  = document.getElementById('modalHapus');
    const btnBatal = document.getElementById('batalHapus');
    const btnYa    = document.getElementById('yaHapus');
    const judul    = document.getElementById('modalJudul');
    const pesan    = document.getElementById('modalPesan');
    const cekSemua = document.getElementById('cekSemua');

    // Centang "pilih semua" -> centang/lepas semua baris
    if (cekSemua) {
      cekSemua.addEventListener('change', function () {
        document.querySelectorAll('.pilih').forEach(c => c.checked = cekSemua.checked);
      });
    }

    function tutup() { overlay.classList.remove('tampil'); }

    // Klik "Hapus Terpilih" -> tampilkan modal (bukan alert browser)
    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const n = document.querySelectorAll('.pilih:checked').length;
        if (n === 0) {
          judul.textContent = 'Belum ada yang dipilih';
          pesan.textContent = 'Centang dulu kotak di samping data yang ingin dihapus.';
          btnYa.style.display = 'none';
          btnBatal.textContent = 'Mengerti';
        } else {
          judul.textContent = 'Hapus ' + n + ' data?';
          pesan.textContent = 'Data yang dihapus tidak bisa dikembalikan. Lanjutkan?';
          btnYa.style.display = '';
          btnBatal.textContent = 'Batal';
        }
        overlay.classList.add('tampil');
      });
    }

    btnBatal.addEventListener('click', tutup);
    btnYa.addEventListener('click', function () { tutup(); form.submit(); });
    overlay.addEventListener('click', function (e) { if (e.target === overlay) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
  })();
</script>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . '/includes/footer.php';
