<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('siswa');
$user = current_user();
$leaderboard = get_leaderboard($pdo, 10);
$myPoin = total_poin($pdo, $user['id']);
$inTop = false;
foreach ($leaderboard as $row) {
    if ($row['id'] == $user['id']) $inTop = true;
}

$activeTab = 'leaderboard';
$pageTitle = 'Leaderboard';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar_siswa.php';
?>
    <div class="dash-head"><div><h2>Leaderboard</h2><p>Peringkat siswa berdasarkan total poin.</p></div></div>
    <div>
      <?php foreach ($leaderboard as $i => $s): ?>
        <div class="lb-row <?= $s['id'] == $user['id'] ? 'me' : '' ?>">
          <div class="lb-rank <?= $i === 0 ? 'gold' : '' ?>"><?= $i === 0 ? '🏆' : ($i + 1) ?></div>
          <div class="lb-name"><?= h($s['name']) ?><span class="lb-class"><?= h($s['kelas'] ?? '') ?><?= $s['id'] == $user['id'] ? ' · kamu' : '' ?></span></div>
          <div class="lb-points"><?= (int)$s['total_poin'] ?></div>
        </div>
      <?php endforeach; ?>
      <?php if (!$inTop): ?>
        <div class="lb-row me">
          <div class="lb-rank">&mdash;</div>
          <div class="lb-name"><?= h($user['name']) ?><span class="lb-class"><?= h($user['kelas'] ?? '') ?> &middot; kamu</span></div>
          <div class="lb-points"><?= $myPoin ?></div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
