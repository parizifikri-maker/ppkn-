<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('siswa');
$user = current_user();
ensure_first_bab($pdo, $user['id']);
$bab = get_bab_with_status($pdo, $user['id']);

$activeTab = 'materi';
$pageTitle = 'Materi';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar_siswa.php';
?>
    <div class="dash-head"><div><h2>Materi pembelajaran</h2><p>Selesaikan bab secara berurutan untuk membuka bab berikutnya.</p></div></div>
    <div class="bab-grid">
      <?php foreach ($bab as $b): ?>
        <?php
          $count = $pdo->prepare("SELECT COUNT(*) FROM subbab WHERE bab_id = ?");
          $count->execute([$b['id']]);
          $subCount = $count->fetchColumn();
          $status = $b['user_status'];
        ?>
        <div class="bab-card">
          <div class="bab-top">
            <span class="bab-tag"><?= h($b['tag']) ?></span>
            <span class="bab-status <?= $status === 'done' ? 'done' : '' ?>">
              <?= $status === 'done' ? 'Selesai' : ($status === 'progress' ? 'Berjalan' : 'Siap dibaca') ?>
            </span>
          </div>
          <h4><?= h($b['title']) ?></h4>
          <p class="bab-sub"><?= h($b['sub']) ?></p>
          <div class="bab-actions">
            <span style="font-size:12px;color:#8a8270;"><?= $subCount ?> subbab</span>
            <a class="btn btn-primary btn-sm" href="materi_detail.php?id=<?= $b['id'] ?>">Buka materi</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
