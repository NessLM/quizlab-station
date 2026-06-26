<?php
// =====================================================================
//  hasil.php — Halaman untuk guru
//
//  Menampilkan tabel seluruh siswa (ID, Nama, Kelas, Benar, Salah, Waktu)
//  diurutkan dari pendaftaran terbaru. Nilai benar/salah berasal dari VR
//  (dikirim lewat update.php). Semua data ditampilkan dengan
//  htmlspecialchars agar aman.
// =====================================================================

require 'koneksi.php';

// Ambil semua siswa, terbaru di atas.
// Memakai prepared statement agar konsisten dengan aturan keamanan.
$stmt = mysqli_prepare(
    $koneksi,
    'SELECT id, nama, kelas, benar, salah, waktu
       FROM siswa
   ORDER BY waktu DESC, id DESC'
);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);

$jumlahSiswa = mysqli_num_rows($data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QuizLab Station — Hasil Quiz Siswa</title>
  <style>
    :root {
      --bg:        #06070f;
      --cyan:      #2de2ff;
      --magenta:   #ff3df0;
      --teks:      #eaf0ff;
      --teks-redup:#8b93b8;
      --kartu:     rgba(18, 22, 42, 0.66);
      --garis:     rgba(125, 145, 210, 0.20);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
      color: var(--teks);
      min-height: 100vh;
      padding: 36px 18px;
      background:
        radial-gradient(58% 70% at 12% 6%,  rgba(45, 226, 255, 0.13), transparent 60%),
        radial-gradient(55% 65% at 90% 96%, rgba(255, 61, 240, 0.13), transparent 60%),
        var(--bg);
      background-attachment: fixed;
    }

    .wadah { max-width: 980px; margin: 0 auto; }

    /* ---------- Header halaman ---------- */
    header { text-align: center; margin-bottom: 26px; }

    .label-sekolah {
      display: inline-block;
      font-size: 11px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--teks-redup);
      border: 1px solid var(--garis);
      border-radius: 999px;
      padding: 5px 14px;
      margin-bottom: 14px;
    }

    /* Judul dengan efek anaglyph (cyan kiri, magenta kanan) */
    .judul {
      font-size: clamp(26px, 6vw, 38px);
      font-weight: 800;
      color: #f6f8ff;
      text-shadow:
        -2px 0 0 rgba(45, 226, 255, 0.85),
         2px 0 0 rgba(255, 61, 240, 0.85);
    }
    .subjudul { margin-top: 8px; color: var(--teks-redup); font-size: 14px; }

    /* ---------- Baris aksi (tombol kembali + ringkasan) ---------- */
    .baris-aksi {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    .tautan-kembali {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: var(--cyan);
      text-decoration: none;
      border: 1px solid rgba(45, 226, 255, 0.3);
      border-radius: 999px;
      padding: 9px 18px;
      transition: background 0.18s, box-shadow 0.18s;
    }
    .tautan-kembali:hover {
      background: rgba(45, 226, 255, 0.10);
      box-shadow: 0 0 18px rgba(45, 226, 255, 0.25);
    }
    .ringkasan {
      font-size: 13px;
      color: var(--teks-redup);
    }
    .ringkasan strong { color: var(--teks); }

    /* ---------- Kartu tabel ---------- */
    .kartu {
      background: var(--kartu);
      border: 1px solid var(--garis);
      border-radius: 18px;
      padding: 8px;
      backdrop-filter: blur(10px);
      box-shadow:
        0 24px 60px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.06);
      overflow-x: auto;            /* tabel bisa digeser di layar HP */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      min-width: 620px;            /* jaga keterbacaan; container yang scroll */
    }

    thead th {
      text-align: left;
      font-size: 11px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--cyan);
      padding: 14px 14px;
      border-bottom: 1px solid var(--garis);
      white-space: nowrap;
    }

    tbody td {
      padding: 13px 14px;
      border-bottom: 1px solid rgba(125, 145, 210, 0.10);
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: rgba(45, 226, 255, 0.05); }

    .col-id { color: var(--teks-redup); font-variant-numeric: tabular-nums; }
    .nama   { font-weight: 600; }

    /* Pil nilai: benar = cyan, salah = magenta (warna lensa anaglyph) */
    .pil {
      display: inline-block;
      min-width: 34px;
      text-align: center;
      font-weight: 700;
      font-variant-numeric: tabular-nums;
      border-radius: 8px;
      padding: 3px 10px;
    }
    .pil-benar {
      color: #aef6ff;
      background: rgba(45, 226, 255, 0.12);
      border: 1px solid rgba(45, 226, 255, 0.4);
    }
    .pil-salah {
      color: #ffc6f6;
      background: rgba(255, 61, 240, 0.12);
      border: 1px solid rgba(255, 61, 240, 0.4);
    }
    .waktu { color: var(--teks-redup); white-space: nowrap; }

    /* ---------- Keadaan kosong ---------- */
    .kosong {
      text-align: center;
      padding: 48px 20px;
      color: var(--teks-redup);
    }
    .kosong strong { color: var(--teks); display: block; margin-bottom: 6px; font-size: 16px; }

    @media (prefers-reduced-motion: reduce) {
      .tautan-kembali, tbody tr { transition: none; }
    }
  </style>
</head>
<body>
  <div class="wadah">

    <header>
      <span class="label-sekolah">SMKN 5 Pangkalpinang</span>
      <h1 class="judul">Hasil Quiz Siswa</h1>
      <p class="subjudul">Rekap nilai peserta quiz VR — QuizLab Station</p>
    </header>

    <div class="baris-aksi">
      <a class="tautan-kembali" href="index.php">&larr; Kembali ke Pendaftaran</a>
      <span class="ringkasan">Total siswa terdaftar: <strong><?= (int) $jumlahSiswa ?></strong></span>
    </div>

    <div class="kartu">
      <?php if ($jumlahSiswa === 0): ?>
        <div class="kosong">
          <strong>Belum ada siswa terdaftar.</strong>
          Silakan daftar dulu lewat halaman pendaftaran.
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Benar</th>
              <th>Salah</th>
              <th>Waktu Daftar</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($baris = mysqli_fetch_assoc($data)): ?>
              <tr>
                <td class="col-id">#<?= (int) $baris['id'] ?></td>
                <td class="nama"><?= htmlspecialchars($baris['nama']) ?></td>
                <td><?= htmlspecialchars($baris['kelas']) ?></td>
                <td><span class="pil pil-benar"><?= (int) $baris['benar'] ?></span></td>
                <td><span class="pil pil-salah"><?= (int) $baris['salah'] ?></span></td>
                <td class="waktu">
                  <?= htmlspecialchars(date('d M Y, H:i', strtotime($baris['waktu']))) ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
