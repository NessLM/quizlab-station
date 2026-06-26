<?php
// =====================================================================
//  login.php — Halaman login admin
//  Cek username/password ke tabel admin (password ter-hash), lalu buat sesi.
// =====================================================================

session_start();
require __DIR__ . '/config/koneksi.php';

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
  <link rel="stylesheet" href="assets/css/login.css">
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
