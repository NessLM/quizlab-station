<?php
// =====================================================================
//  includes/auth.php — Penjaga halaman (wajib login) + token CSRF
//  Pakai di paling atas halaman admin:
//      require __DIR__ . '/includes/auth.php';
// =====================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Siapkan token CSRF (dipakai form hapus agar aman dari request palsu)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Belum login? Lempar ke halaman login.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
