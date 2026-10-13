<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    header('Location: ' . (current_user()['role'] === 'guru' ? 'guru/dashboard.php' : 'dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $u = $stmt->fetch();

        if ($u && password_verify($password, $u['password'])) {
            $_SESSION['user'] = [
                'id' => $u['id'],
                'name' => $u['name'],
                'username' => $u['username'],
                'role' => $u['role'],
                'kelas' => $u['kelas'],
            ];
            if ($u['role'] === 'siswa') {
                ensure_first_bab($pdo, $u['id']);
            }
            header('Location: ' . ($u['role'] === 'guru' ? 'guru/dashboard.php' : 'dashboard.php'));
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

$pageTitle = 'Masuk';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>Masuk ke akunmu</h2>
    <p>Lanjutkan progres belajar PPKn-mu.</p>
    <?php if ($error): ?><div class="auth-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="field"><label>Username</label><input type="text" name="username" value="<?= h($_POST['username'] ?? '') ?>" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" required></div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;" type="submit">Masuk</button>
    </form>
    <div class="auth-switch">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
    <div class="auth-switch" style="margin-top:8px;color:#8a8270;">Demo: guru/guru123 &middot; fikri/siswa123</div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
