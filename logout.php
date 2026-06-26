<?php
// =====================================================================
//  logout.php — Keluar / akhiri sesi admin
// =====================================================================
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
