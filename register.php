<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    header('Location: ' . (current_user()['role'] === 'guru' ? 'guru/dashboard.php' : 'dashboard.php'));
    exit;
}

$error = '';
$kelasOptions = ['VII-A', 'VIII-A', 'VIII-B', 'VIII-C', 'IX-A'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $kelas    = $_POST['kelas'] ?? '';
    $role     = ($_POST['role'] ?? 'siswa') === 'guru' ? 'guru' : 'siswa';

    if ($name === '' || $username === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = 'Username sudah digunakan, coba yang lain.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role, kelas) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $username, $hash, $role, $role === 'siswa' ? $kelas : null]);
            $userId = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'username' => $username,
                'role' => $role,
                'kelas' => $role === 'siswa' ? $kelas : null,
            ];

            if ($role === 'siswa') {
                ensure_first_bab($pdo, $userId);
            }

            header('Location: ' . ($role === 'guru' ? 'guru/dashboard.php' : 'dashboard.php'));
            exit;
        }
    }
}

$pageTitle = 'Daftar';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>Buat akun</h2>
    <p>Isi data berikut untuk mulai belajar.</p>
    <?php if ($error): ?><div class="auth-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="field"><label>Nama lengkap</label><input type="text" name="name" placeholder="Contoh: Fikri Ramadhan" value="<?= h($_POST['name'] ?? '') ?>" required></div>
      <div class="field"><label>Daftar sebagai</label>
        <select name="role" id="role-select" onchange="document.getElementById('kelas-field').style.display = this.value==='siswa' ? 'block':'none';">
          <option value="siswa" <?= (($_POST['role'] ?? '') === 'siswa') ? 'selected' : '' ?>>Siswa</option>
          <option value="guru" <?= (($_POST['role'] ?? '') === 'guru') ? 'selected' : '' ?>>Guru / Admin</option>
        </select>
      </div>
      <div class="field" id="kelas-field">
        <label>Kelas</label>
        <select name="kelas">
          <?php foreach ($kelasOptions as $k): ?>
            <option value="<?= h($k) ?>" <?= (($_POST['kelas'] ?? '') === $k) ? 'selected' : '' ?>><?= h($k) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field"><label>Username</label><input type="text" name="username" placeholder="username" value="<?= h($_POST['username'] ?? '') ?>" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" required></div>
      <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;" type="submit">Daftar &amp; Mulai Belajar</button>
    </form>
    <div class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
  </div>
</div>
<script>
  // Sembunyikan kolom kelas kalau daftar sebagai guru (progressive enhancement, tetap jalan tanpa JS)
  document.getElementById('role-select').dispatchEvent(new Event('change'));
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
