<?php
// =====================================================================
//  auth.php — Penjaga halaman (wajib login)
//  Letakkan di paling atas halaman yang hanya boleh dibuka admin:
//      require 'auth.php';
//  Kalau belum login, otomatis dilempar ke login.php.
// =====================================================================

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
