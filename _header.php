<?php
// =====================================================================
//  _header.php — Kerangka atas semua halaman panel (head + sidebar)
//  Sebelum require file ini, set:
//     $judul_halaman  -> judul di topbar (mis. 'Dashboard')
//     $halaman_aktif  -> 'dashboard' | 'cair' | 'padat' (untuk highlight menu)
// =====================================================================
if (!isset($judul_halaman)) $judul_halaman = 'QuizLab Station';
if (!isset($halaman_aktif)) $halaman_aktif = '';
$namaAdmin = $_SESSION['admin_username'] ?? 'admin';

// Bantu menandai menu yang sedang aktif
function aktif($id, $sekarang) { return $id === $sekarang ? 'aktif' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($judul_halaman) ?> — QuizLab Station</title>
  <style>
    :root{
      --bg:#06070f; --cyan:#2de2ff; --magenta:#ff3df0;
      --teks:#eaf0ff; --redup:#8b93b8; --kartu:rgba(18,22,42,.7); --garis:rgba(125,145,210,.18);
    }
    *{ box-sizing:border-box; margin:0; padding:0; }
    body{
      font-family:"Segoe UI",system-ui,Arial,sans-serif; color:var(--teks); min-height:100vh;
      background:
        radial-gradient(50% 60% at 100% 0, rgba(255,61,240,.10), transparent 60%),
        radial-gradient(50% 60% at 0 100%, rgba(45,226,255,.10), transparent 60%), var(--bg);
    }
    .app{ display:flex; min-height:100vh; }

    /* ---------- Sidebar ---------- */
    .sidebar{
      width:262px; flex-shrink:0; background:rgba(8,10,20,.88); border-right:1px solid var(--garis);
      display:flex; flex-direction:column; padding:22px 16px; position:sticky; top:0; height:100vh;
    }
    .brand{ text-align:center; margin-bottom:26px; }
    .brand .nama{
      font-size:22px; font-weight:800; color:#f6f8ff;
      text-shadow:-2px 0 0 rgba(45,226,255,.85), 2px 0 0 rgba(255,61,240,.85);
    }
    .brand .sub{ display:block; font-size:10px; letter-spacing:3px; color:var(--redup); margin-top:6px; }

    .menu{ display:flex; flex-direction:column; gap:6px; flex:1; }
    .item{
      display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:11px;
      color:var(--redup); text-decoration:none; font-size:14px; border:1px solid transparent; transition:.15s;
    }
    .item:hover{ background:rgba(45,226,255,.06); color:var(--teks); }
    .item.aktif{
      background:rgba(45,226,255,.10); color:#aef6ff; border-color:rgba(45,226,255,.35);
      box-shadow:inset 3px 0 0 var(--cyan);
    }
    .item .ikon{ font-size:15px; width:20px; text-align:center; }

    .sidebar-bawah{ border-top:1px solid var(--garis); padding-top:14px; margin-top:14px; }
    .user{ font-size:13px; color:var(--redup); padding:4px 14px 10px; }
    .user b{ color:var(--teks); }
    .keluar{
      display:flex; align-items:center; gap:10px; padding:11px 14px; border-radius:11px;
      color:#ffc6d1; text-decoration:none; font-size:14px; border:1px solid rgba(255,61,240,.25);
    }
    .keluar:hover{ background:rgba(255,61,240,.10); }

    /* ---------- Konten ---------- */
    .konten{ flex:1; display:flex; flex-direction:column; min-width:0; }
    .topbar{
      padding:20px 28px; border-bottom:1px solid var(--garis);
      display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .topbar h1{ font-size:21px; font-weight:700; }
    .topbar .sekolah{ font-size:11px; letter-spacing:2px; color:var(--redup); text-transform:uppercase; }
    .isi{ padding:26px 28px; flex:1; }

    /* ---------- Kartu statistik ---------- */
    .stat-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin-bottom:24px; }
    .stat{
      background:var(--kartu); border:1px solid var(--garis); border-radius:16px; padding:20px;
      box-shadow:0 16px 40px rgba(0,0,0,.4);
    }
    .stat .angka{ font-size:34px; font-weight:800; color:var(--cyan); line-height:1; }
    .stat.magenta .angka{ color:var(--magenta); }
    .stat .ket{ font-size:13px; color:var(--redup); margin-top:8px; }

    /* ---------- Tabel ---------- */
    .kartu{
      background:var(--kartu); border:1px solid var(--garis); border-radius:16px; padding:8px;
      box-shadow:0 16px 40px rgba(0,0,0,.4); overflow-x:auto;
    }
    table{ width:100%; border-collapse:collapse; font-size:14px; min-width:520px; }
    th{
      text-align:left; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color:var(--cyan);
      padding:13px 14px; border-bottom:1px solid var(--garis); white-space:nowrap;
    }
    td{ padding:12px 14px; border-bottom:1px solid rgba(125,145,210,.10); }
    tr:last-child td{ border-bottom:none; }
    tr:hover td{ background:rgba(45,226,255,.05); }
    .id{ color:var(--redup); } .nama{ font-weight:600; }
    .skor{
      display:inline-block; min-width:36px; text-align:center; font-weight:700; color:#aef6ff;
      background:rgba(45,226,255,.12); border:1px solid rgba(45,226,255,.4); border-radius:8px; padding:3px 10px;
    }
    .waktu{ color:var(--redup); white-space:nowrap; }
    .kosong{ text-align:center; padding:48px 20px; color:var(--redup); }
    .kosong b{ display:block; color:var(--teks); font-size:16px; margin-bottom:6px; }

    /* ---------- Responsif HP ---------- */
    @media(max-width:760px){
      .app{ flex-direction:column; }
      .sidebar{ width:100%; height:auto; position:static; }
      .menu{ gap:4px; }
    }
    @media (prefers-reduced-motion: reduce){ .item,.keluar{ transition:none; } }
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <div class="brand">
        <div class="nama">QuizLab Station</div>
        <span class="sub">SMKN 5 PANGKALPINANG</span>
      </div>

      <nav class="menu">
        <a class="item <?= aktif('dashboard', $halaman_aktif) ?>" href="dashboard.php">
          <span class="ikon">&#9638;</span> Dashboard
        </a>
        <a class="item <?= aktif('cair', $halaman_aktif) ?>" href="data_quiz.php?kategori=cair_semipadat">
          <span class="ikon">&#9680;</span> Data Quiz Cair &amp; Semi Padat
        </a>
        <a class="item <?= aktif('padat', $halaman_aktif) ?>" href="data_quiz.php?kategori=padat">
          <span class="ikon">&#9679;</span> Data Quiz Padat
        </a>
      </nav>

      <div class="sidebar-bawah">
        <div class="user">Masuk sebagai <b><?= htmlspecialchars($namaAdmin) ?></b></div>
        <a class="keluar" href="logout.php"><span class="ikon">&#10150;</span> Keluar</a>
      </div>
    </aside>

    <main class="konten">
      <header class="topbar">
        <h1><?= htmlspecialchars($judul_halaman) ?></h1>
        <span class="sekolah">VR Pharmaceutical Lab</span>
      </header>
      <section class="isi">
