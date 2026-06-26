<?php
// =====================================================================
//  login.php — Halaman login admin
//  Cek username/password ke tabel admin (password di-hash), lalu buat sesi.
// =====================================================================

session_start();
require 'koneksi.php';

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Cari admin (prepared statement)
        $stmt = mysqli_prepare($koneksi, 'SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        // Verifikasi password ter-hash
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);                 // cegah session fixation
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — QuizLab Station</title>
  <style>
    :root{ --bg:#06070f; --cyan:#2de2ff; --magenta:#ff3df0; --teks:#eaf0ff; --redup:#8b93b8;
           --kartu:rgba(18,22,42,.66); --garis:rgba(125,145,210,.20); --error:#ff6b8b; }
    *{ box-sizing:border-box; margin:0; padding:0; }
    body{
      font-family:"Segoe UI",system-ui,Arial,sans-serif; color:var(--teks); min-height:100vh;
      display:flex; align-items:center; justify-content:center; padding:24px;
      background:
        radial-gradient(58% 70% at 18% 8%, rgba(45,226,255,.14), transparent 60%),
        radial-gradient(55% 65% at 85% 92%, rgba(255,61,240,.14), transparent 60%), var(--bg);
    }
    .wadah{ width:100%; max-width:400px; }
    .merek{ text-align:center; margin-bottom:22px; }
    .label{ display:inline-block; font-size:11px; letter-spacing:3px; text-transform:uppercase;
            color:var(--redup); border:1px solid var(--garis); border-radius:999px; padding:5px 14px; margin-bottom:14px; }
    .judul{ font-size:34px; font-weight:800; color:#f6f8ff;
            text-shadow:-2px 0 0 rgba(45,226,255,.85), 2px 0 0 rgba(255,61,240,.85); }
    .sub{ margin-top:8px; color:var(--redup); font-size:13px; }

    .kartu{
      background:var(--kartu); border:1px solid var(--garis); border-radius:18px; padding:26px 24px;
      backdrop-filter:blur(10px);
      box-shadow:0 24px 60px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.06);
    }
    .judul-kartu{ font-size:13px; letter-spacing:2px; text-transform:uppercase; color:var(--cyan);
                  margin-bottom:18px; display:flex; align-items:center; gap:9px; }
    .judul-kartu::before{ content:""; width:9px; height:9px; border-radius:50%; background:var(--cyan);
                          box-shadow:0 0 12px var(--cyan); }
    .kelompok{ margin-bottom:16px; }
    label{ display:block; font-size:13px; color:var(--redup); margin-bottom:7px; }
    input{
      width:100%; padding:13px 15px; font-size:15px; color:var(--teks);
      background:rgba(6,8,18,.7); border:1px solid var(--garis); border-radius:11px; outline:none;
      transition:border-color .18s, box-shadow .18s, background .18s;
    }
    input::placeholder{ color:#5a6188; }
    input:focus{ border-color:var(--cyan); background:rgba(6,8,18,.95); box-shadow:0 0 0 3px rgba(45,226,255,.18); }
    .tombol{
      width:100%; margin-top:6px; padding:14px; font-size:15px; font-weight:700; letter-spacing:.5px;
      color:#04121a; border:none; border-radius:11px; cursor:pointer;
      background:linear-gradient(135deg,#5af0ff 0%,#2de2ff 45%,#14b8d4 100%);
      box-shadow:0 8px 24px rgba(45,226,255,.3); transition:transform .15s, box-shadow .15s, filter .15s;
    }
    .tombol:hover{ transform:translateY(-2px); box-shadow:0 12px 30px rgba(45,226,255,.45); filter:brightness(1.05); }
    .tombol:active{ transform:translateY(0); }
    .pesan-error{
      background:rgba(255,107,139,.10); border:1px solid rgba(255,107,139,.35); color:#ffc6d1;
      border-radius:12px; padding:13px 15px; margin-bottom:18px; font-size:14px;
    }
    @media (prefers-reduced-motion: reduce){ .tombol, input{ transition:none; } }
  </style>
</head>
<body>
  <main class="wadah">
    <header class="merek">
      <span class="label">SMKN 5 Pangkalpinang</span>
      <h1 class="judul">QuizLab Station</h1>
      <p class="sub">Panel Admin — masuk untuk melihat data quiz</p>
    </header>

    <section class="kartu">
      <div class="judul-kartu">Login Admin</div>

      <?php if ($error): ?>
        <div class="pesan-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="login.php">
        <div class="kelompok">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="admin" autocomplete="username" required>
        </div>
        <div class="kelompok">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password" required>
        </div>
        <button type="submit" class="tombol">Masuk &rarr;</button>
      </form>
    </section>
  </main>
</body>
</html>
