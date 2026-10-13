<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('siswa');
$user = current_user();

$babId = (int)($_GET['id'] ?? 0);
$bab = get_bab_by_id($pdo, $babId);
if (!$bab) {
    header('Location: materi.php');
    exit;
}

// Semua materi dapat dibaca siswa; progres kuis tetap dicatat secara terpisah.

$subbab = get_subbab($pdo, $babId);
$kasus = get_studi_kasus($pdo, $babId);
$contohStmt = $pdo->prepare("SELECT * FROM soal WHERE bab_id = ? ORDER BY RAND() LIMIT 2");
$contohStmt->execute([$babId]);
$contohSoal = $contohStmt->fetchAll();
$videoStmt = $pdo->prepare("SELECT * FROM materi_video WHERE bab_id = ? LIMIT 1");
$videoStmt->execute([$babId]);
$video = $videoStmt->fetch();

// Simpan jawaban studi kasus di session per bab supaya bertahan saat reload
$sessionKey = 'case_choice_' . $babId;
if ($kasus && isset($_GET['jawab']) && in_array($_GET['jawab'], ['a','b','c','d'], true)) {
    $_SESSION[$sessionKey] = $_GET['jawab'];
}
$chosenCase = $_SESSION[$sessionKey] ?? null;

$activeTab = 'materi';
$pageTitle = $bab['title'];
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar_siswa.php';
?>
    <div class="dash-head">
      <div><h2><?= h($bab['tag']) ?>: <?= h($bab['title']) ?></h2><p><?= h($bab['sub']) ?></p></div>
      <a class="btn btn-ghost btn-sm" href="materi.php">&larr; Kembali ke daftar bab</a>
    </div>
    <div class="materi-detail">
      <div class="materi-body">
        <?php foreach ($subbab as $s): ?>
          <div class="subbab">
            <h4><?= h($s['judul']) ?></h4>
            <p><?= h($s['isi']) ?></p>
          </div>
        <?php endforeach; ?>

        <?php if ($video): ?>
          <section class="materi-video">
            <div class="video-copy"><span class="eyebrow">BELAJAR LEWAT VIDEO</span><h3><?= h($video['judul']) ?></h3><p><?= h($video['deskripsi']) ?></p><a href="video.php" class="link-action">Lihat semua video →</a></div>
            <div class="video-frame"><iframe src="https://www.youtube-nocookie.com/embed/<?= h($video['youtube_id']) ?>" title="<?= h($video['judul']) ?>" allowfullscreen></iframe></div>
          </section>
        <?php endif; ?>

        <?php if ($kasus): ?>
          <div class="case-box">
            <b>Studi kasus</b>
            <?= h($kasus['pertanyaan']) ?>
            <div class="case-options">
              <?php foreach (['a','b','c','d'] as $opt): ?>
                <a class="case-opt <?= $chosenCase === $opt ? 'chosen' : '' ?>" href="?id=<?= $babId ?>&jawab=<?= $opt ?>#studi-kasus">
                  <?= h($kasus['opsi_' . $opt]) ?>
                </a>
              <?php endforeach; ?>
            </div>
            <?php if ($chosenCase !== null): ?>
              <div class="case-feedback">
                <?php if ($chosenCase === $kasus['jawaban_benar']): ?>
                  ✅ <?= h($kasus['penjelasan_benar']) ?>
                <?php else: ?>
                  ↺ <?= h($kasus['penjelasan_salah']) ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="practice-box">
          <div class="practice-head"><div><span class="eyebrow">CONTOH SOAL</span><h3>Latihan santai, tanpa waktu</h3></div><span>📝</span></div>
          <p>Pilih jawaban menurutmu. Ini hanya untuk berlatih sebelum mengikuti Tantangan Campuran.</p>
          <?php foreach ($contohSoal as $n => $soal): ?>
            <details class="sample-question"><summary><?= $n + 1 ?>. <?= h($soal['pertanyaan']) ?></summary><div><b>Jawaban: <?= strtoupper($soal['jawaban_benar']) ?>.</b> <?= h($soal['penjelasan']) ?></div></details>
          <?php endforeach; ?>
        </div>

        <a class="btn btn-gold" style="margin-top:10px;display:inline-flex;" href="kuis.php">Selesai membaca &middot; Latihan acak</a>
      </div>
      <div class="materi-side">
        <div class="panel" style="margin-bottom:0;">
          <h3>Daftar isi</h3>
          <?php foreach ($subbab as $i => $s): ?>
            <div class="toc-item"><span><?= $i + 1 ?>.</span> <?= h($s['judul']) ?></div>
          <?php endforeach; ?>
        </div>
        <div class="panel source-panel" style="margin-bottom:0;">
          <h3>📥 Sumber tepercaya</h3>
          <p>Gunakan buku dan modul resmi untuk memperdalam materi.</p>
          <a class="btn btn-ghost btn-sm" href="assets/downloads/ringkasan-ppkn-smp.pdf" download>Ringkasan offline</a>
          <a class="source-link" href="https://buku.kemdikbud.go.id/" target="_blank" rel="noopener">Buku Pendidikan Pancasila SMP/MTs - Kemendikbud</a>
          <a class="source-link" href="https://repositori.kemdikbud.go.id/view/subjects/PED003.type.html" target="_blank" rel="noopener">Modul PPKn SMP - Repositori Kemendikbud</a>
          <small>Periksa kelas/edisi yang sesuai sebelum mengunduh modul resmi.</small>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
