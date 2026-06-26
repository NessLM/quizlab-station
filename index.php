<?php
// =====================================================================
//  index.php — Pintu masuk. Arahkan ke dashboard (kalau sudah login)
//  atau ke halaman login (kalau belum).
// =====================================================================
session_start();
header('Location: ' . (isset($_SESSION['admin_id']) ? 'dashboard.php' : 'login.php'));
exit;
