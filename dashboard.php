<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('siswa');
$user = current_user();
ensure_first_bab($pdo, $user['id']);

$bab = get_bab_with_status($pdo, $user['id']);
$doneCount = count(array_filter($bab, fn($b) => $b['user_status'] === 'done'));
$progressPct = count($bab) > 0 ? round(($doneCount / count($bab)) * 100) : 0;
$poin = total_poin($pdo, $user['id']);
$kuisSelesai = kuis_selesai_count($pdo, $user['id']);
$nilaiTerakhir = nilai_kuis_terakhir($pdo, $user['id']);

$activeTab = 'dashboard';
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar_siswa.php';
?>
    <div class="dash-head">
      <div><h2>Halo, <?= h(explode(' ', $user['name'])[0]) ?> 👋</h2><p>Ini ringkasan progres belajar PPKn-mu.</p></div>
      <a class="btn btn-gold btn-sm" href="materi.php">Lanjut belajar</a>
    </div>
    <div class="stat-row">
      <div class="stat-card"><span>Progres belajar</span><b><?= $progressPct ?>%</b></div>
      <div class="stat-card"><span>Total poin</span><b><?= $poin ?></b></div>
      <div class="stat-card"><span>Kuis diselesaikan</span><b><?= $kuisSelesai ?> / <?= count($bab) ?></b></div>
      <div class="stat-card"><span>Nilai kuis terakhir</span><b><?= $nilaiTerakhir !== null ? $nilaiTerakhir : '—' ?></b></div>
    </div>
    <div class="panel">
      <h3>Progres per bab</h3>
      <?php foreach ($bab as $b): ?>
        <?php
          $status = $b['user_status'];
          $pct = $status === 'done' ? 100 : ($status === 'progress' ? 45 : 0);
          $label = $status === 'done' ? 'Selesai' : ($status === 'progress' ? 'Sedang berjalan' : 'Terkunci');
        ?>
        <div style="margin-bottom:14px;">
          <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:6px;">
            <span><?= h($b['tag']) ?> &mdash; <?= h($b['title']) ?></span>
            <span style="color:#8a8270;"><?= $label ?></span>
          </div>
          <div class="progress-outer"><div class="progress-inner" style="width:<?= $pct ?>%; background:<?= $status === 'locked' ? '#d8d0ba' : 'var(--leaf)' ?>;"></div></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="panel">
      <h3>Lencana</h3>
      <div class="badge-row">
        <div class="badge-chip <?= $doneCount >= 1 ? '' : 'locked' ?>">🏅 Pelajar Pancasila</div>
        <div class="badge-chip <?= $nilaiTerakhir === 100 ? '' : 'locked' ?>">⭐ Nilai 100 Pertama</div>
        <div class="badge-chip <?= $doneCount >= count($bab) ? '' : 'locked' ?>">🥇 Master PPKn<?= $doneCount >= count($bab) ? '' : ' (terkunci)' ?></div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
