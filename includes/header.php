<?php
// =====================================================================
//  includes/header.php — Kerangka atas semua halaman panel (sidebar)
//  Sebelum require, set:
//     $judul_halaman  -> judul di topbar (mis. 'Dashboard')
//     $halaman_aktif  -> 'dashboard' | 'cair' | 'padat' (untuk highlight menu)
// =====================================================================
if (!isset($judul_halaman)) $judul_halaman = 'QuizLab Station';
if (!isset($halaman_aktif)) $halaman_aktif = '';
$namaAdmin = $_SESSION['admin_username'] ?? 'admin';

// Bantu menandai menu yang sedang aktif
function aktif($id, $sekarang)
{
    return $id === $sekarang ? 'aktif' : '';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($judul_halaman) ?> — QuizLab Station</title>
  <link rel="stylesheet" href="assets/css/style.css">
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
        <a class="item <?= aktif('cair', $halaman_aktif) ?>" href="data_quiz.php?lokasi=VRLabSimulation">
          <span class="ikon">&#9680;</span> Data Quiz Cair &amp; Semi Padat
        </a>
        <a class="item <?= aktif('padat', $halaman_aktif) ?>" href="data_quiz.php?lokasi=VRLabSimulation_Padat">
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
