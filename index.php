<?php
// =====================================================================
//  index.php — Halaman utama / stasiun pendaftaran siswa
//
//  Tugas halaman ini:
//   1. Menampilkan form Nama + Kelas (dikirim POST ke simpan.php).
//   2. Setelah berhasil daftar, simpan.php redirect ke sini membawa
//      ?id=...  -> kita tampilkan pesan sukses berisi ID siswa.
//   3. Menyediakan tautan ke halaman hasil (untuk guru).
// =====================================================================

require 'koneksi.php';

// --- Jika ada ?id=... berarti baru saja daftar; ambil datanya untuk ditampilkan
$siswaBaru = null;
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $idBaru = (int) $_GET['id'];

    // SELECT dengan prepared statement (wajib, walau input dari URL)
    $stmt = mysqli_prepare($koneksi, 'SELECT id, nama, kelas FROM siswa WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idBaru);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $siswaBaru = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);
}

// --- Cek apakah simpan.php mengirim pesan error (nama/kelas kosong)
$adaError = isset($_GET['error']) && $_GET['error'] === 'kosong';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QuizLab Station — Pendaftaran</title>
  <style>
    /* ============================================================
       Palet warna bertema VR / kacamata 3D anaglyph
       (cyan = lensa kiri, magenta = lensa kanan)
       ============================================================ */
    :root {
      --bg:        #06070f;                       /* hitam-navy */
      --cyan:      #2de2ff;
      --magenta:   #ff3df0;
      --teks:      #eaf0ff;
      --teks-redup:#8b93b8;
      --kartu:     rgba(18, 22, 42, 0.66);
      --garis:     rgba(125, 145, 210, 0.20);
      --sukses:    #36f5b0;
      --error:     #ff6b8b;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
      color: var(--teks);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 28px 18px;
      /* Latar gelap + dua glow gradien (cyan & magenta) */
      background:
        radial-gradient(58% 70% at 18% 8%,  rgba(45, 226, 255, 0.14), transparent 60%),
        radial-gradient(55% 65% at 85% 92%, rgba(255, 61, 240, 0.14), transparent 60%),
        var(--bg);
      background-attachment: fixed;
    }

    /* Garis-garis grid samar agar terasa "futuristik" */
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(125, 145, 210, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(125, 145, 210, 0.05) 1px, transparent 1px);
      background-size: 44px 44px;
      mask-image: radial-gradient(circle at 50% 40%, #000 30%, transparent 80%);
      pointer-events: none;
      z-index: 0;
    }

    .wadah { position: relative; z-index: 1; width: 100%; max-width: 460px; }

    /* ---------- Bagian judul ---------- */
    .merek {
      text-align: center;
      margin-bottom: 22px;
      animation: melayang 6s ease-in-out infinite;
    }

    .label-sekolah {
      display: inline-block;
      font-size: 11px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--teks-redup);
      border: 1px solid var(--garis);
      border-radius: 999px;
      padding: 5px 14px;
      margin-bottom: 16px;
    }

    /* Efek anaglyph: bayangan cyan di kiri, magenta di kanan */
    .judul {
      font-size: clamp(34px, 9vw, 52px);
      font-weight: 800;
      letter-spacing: 1px;
      line-height: 1.05;
      color: #f6f8ff;
      text-shadow:
        -2px 0 0 rgba(45, 226, 255, 0.85),
         2px 0 0 rgba(255, 61, 240, 0.85);
      animation: anaglyph 5s ease-in-out infinite;
    }
    .judul span { display: block; }

    .subjudul {
      margin-top: 12px;
      font-size: 14px;
      color: var(--teks-redup);
      line-height: 1.5;
    }

    /* ---------- Kartu form melayang ---------- */
    .kartu {
      background: var(--kartu);
      border: 1px solid var(--garis);
      border-radius: 18px;
      padding: 26px 24px;
      backdrop-filter: blur(10px);
      /* Bayangan luar (melayang) + bayangan dalam (inner) */
      box-shadow:
        0 24px 60px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        inset 0 0 36px rgba(45, 226, 255, 0.04);
    }

    .judul-kartu {
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--cyan);
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 9px;
    }
    .judul-kartu::before {
      content: "";
      width: 9px; height: 9px;
      border-radius: 50%;
      background: var(--cyan);
      box-shadow: 0 0 12px var(--cyan);
    }

    .kelompok { margin-bottom: 16px; }

    label {
      display: block;
      font-size: 13px;
      color: var(--teks-redup);
      margin-bottom: 7px;
    }

    input[type="text"] {
      width: 100%;
      padding: 13px 15px;
      font-size: 15px;
      color: var(--teks);
      background: rgba(6, 8, 18, 0.7);
      border: 1px solid var(--garis);
      border-radius: 11px;
      outline: none;
      transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
    }
    input[type="text"]::placeholder { color: #5a6188; }

    /* Focus state yang jelas: border cyan + glow */
    input[type="text"]:focus {
      border-color: var(--cyan);
      background: rgba(6, 8, 18, 0.95);
      box-shadow: 0 0 0 3px rgba(45, 226, 255, 0.18);
    }

    /* ---------- Tombol gradien cyan ---------- */
    .tombol {
      width: 100%;
      margin-top: 6px;
      padding: 14px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #04121a;
      border: none;
      border-radius: 11px;
      cursor: pointer;
      background: linear-gradient(135deg, #5af0ff 0%, #2de2ff 45%, #14b8d4 100%);
      box-shadow: 0 8px 24px rgba(45, 226, 255, 0.3);
      transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
    }
    .tombol:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(45, 226, 255, 0.45);
      filter: brightness(1.05);
    }
    .tombol:active { transform: translateY(0); }

    /* ---------- Kotak pesan (sukses / error) ---------- */
    .pesan {
      border-radius: 12px;
      padding: 15px 16px;
      margin-bottom: 20px;
      font-size: 14px;
      line-height: 1.5;
    }
    .pesan-sukses {
      background: rgba(54, 245, 176, 0.10);
      border: 1px solid rgba(54, 245, 176, 0.35);
      color: #b9ffe6;
    }
    .pesan-error {
      background: rgba(255, 107, 139, 0.10);
      border: 1px solid rgba(255, 107, 139, 0.35);
      color: #ffc6d1;
    }
    .badge-id {
      display: inline-block;
      font-weight: 800;
      font-size: 18px;
      color: var(--sukses);
      background: rgba(54, 245, 176, 0.12);
      border: 1px solid rgba(54, 245, 176, 0.4);
      border-radius: 9px;
      padding: 2px 12px;
      margin: 0 2px;
      letter-spacing: 0.5px;
    }
    .catatan-id {
      margin-top: 8px;
      font-size: 12.5px;
      color: var(--teks-redup);
    }

    /* ---------- Tautan ke halaman hasil ---------- */
    .kaki {
      text-align: center;
      margin-top: 22px;
    }
    .tautan-hasil {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: var(--magenta);
      text-decoration: none;
      border: 1px solid rgba(255, 61, 240, 0.3);
      border-radius: 999px;
      padding: 9px 18px;
      transition: background 0.18s, box-shadow 0.18s;
    }
    .tautan-hasil:hover {
      background: rgba(255, 61, 240, 0.10);
      box-shadow: 0 0 18px rgba(255, 61, 240, 0.25);
    }

    /* ---------- Animasi ---------- */
    @keyframes anaglyph {
      0%, 100% { text-shadow: -2px 0 0 rgba(45,226,255,0.85),  2px 0 0 rgba(255,61,240,0.85); }
      50%      { text-shadow: -3px 0 0 rgba(45,226,255,0.9),   3px 0 0 rgba(255,61,240,0.9); }
    }
    @keyframes melayang {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-6px); }
    }

    /* Hormati pengguna yang meminta animasi diminimalkan */
    @media (prefers-reduced-motion: reduce) {
      .merek, .judul { animation: none; }
      .tombol, .tautan-hasil, input { transition: none; }
    }

    /* ---------- Responsif HP ---------- */
    @media (max-width: 480px) {
      .kartu { padding: 22px 18px; }
      .label-sekolah { letter-spacing: 2px; }
    }
  </style>
</head>
<body>
  <main class="wadah">

    <!-- Judul + label sekolah -->
    <header class="merek">
      <span class="label-sekolah">SMKN 5 Pangkalpinang</span>
      <h1 class="judul"><span>QuizLab</span><span>Station</span></h1>
      <p class="subjudul">Stasiun pendaftaran peserta quiz VR.<br>
         Daftar di sini, lalu kerjakan quiz di dalam headset.</p>
    </header>

    <!-- Kartu form -->
    <section class="kartu">

      <?php if ($siswaBaru): ?>
        <!-- Pesan sukses setelah pendaftaran -->
        <div class="pesan pesan-sukses">
          Pendaftaran berhasil! Halo
          <strong><?= htmlspecialchars($siswaBaru['nama']) ?></strong>
          (<?= htmlspecialchars($siswaBaru['kelas']) ?>).<br>
          ID kamu: <span class="badge-id">#<?= (int) $siswaBaru['id'] ?></span>
          <div class="catatan-id">
            Ingat / catat ID ini. Masukkan ID tersebut saat memulai quiz di VR
            agar nilaimu tersimpan otomatis.
          </div>
        </div>
      <?php endif; ?>

      <?php if ($adaError): ?>
        <!-- Pesan error validasi -->
        <div class="pesan pesan-error">
          Nama dan Kelas tidak boleh kosong. Silakan isi kembali.
        </div>
      <?php endif; ?>

      <div class="judul-kartu">Form Pendaftaran</div>

      <form action="simpan.php" method="post">
        <div class="kelompok">
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" placeholder="mis. Budi Santoso"
                 maxlength="100" autocomplete="off" required>
        </div>

        <div class="kelompok">
          <label for="kelas">Kelas</label>
          <input type="text" id="kelas" name="kelas" placeholder="mis. XI RPL 1"
                 maxlength="50" autocomplete="off" required>
        </div>

        <button type="submit" class="tombol">Daftar &amp; Dapatkan ID &rarr;</button>
      </form>
    </section>

    <!-- Tautan ke halaman hasil (untuk guru) -->
    <div class="kaki">
      <a class="tautan-hasil" href="hasil.php">Lihat Hasil Quiz Siswa &rarr;</a>
    </div>

  </main>
</body>
</html>
