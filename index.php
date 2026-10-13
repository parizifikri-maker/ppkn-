<?php
require_once __DIR__ . '/includes/bootstrap.php';
$babList = $pdo->query("SELECT * FROM bab ORDER BY urutan ASC")->fetchAll();
$pageTitle = 'Beranda';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div>
    <div class="hero-eyebrow">MATA PELAJARAN PPKN &middot; KELAS VII&ndash;IX</div>
    <h1>Belajar Pancasila jadi terasa dekat dengan keseharian.</h1>
    <p>Lima bab, studi kasus nyata, dan kuis yang memberi umpan balik langsung &mdash; supaya nilai kewarganegaraan bukan sekadar dihafal, tapi dipraktikkan.</p>
    <div class="hero-actions">
      <a class="btn btn-gold" href="register.php">Mulai Belajar</a>
      <a class="btn btn-ghost" href="login.php">Saya sudah punya akun</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><b><?= count($babList) ?></b><span>Bab materi inti</span></div>
      <div class="hero-stat"><b>25+</b><span>Soal &amp; studi kasus</span></div>
      <div class="hero-stat"><b>950</b><span>Poin tertinggi minggu ini</span></div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-star">★</div>
    <h3>Peta belajar semester ini</h3>
    <p>Selesaikan tiap bab secara berurutan untuk membuka bab berikutnya dan mengumpulkan lencana.</p>
    <div class="hero-bab-list">
      <?php foreach ($babList as $b): ?>
        <div class="hero-bab"><span class="hero-bab-num"><?= h($b['urutan']) ?></span> <?= h($b['title']) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="block">
  <div class="block-head">
    <h2>Tujuan pembelajaran</h2>
    <p>Tiga hal yang ingin dicapai lewat platform ini</p>
  </div>
  <div class="tujuan-grid">
    <div class="tujuan-card"><div class="tujuan-num">01</div><h4>Memahami, bukan menghafal</h4><p>Materi disampaikan dengan contoh kehidupan sehari-hari, bukan hanya definisi dan tanggal.</p></div>
    <div class="tujuan-card"><div class="tujuan-num">02</div><h4>Berlatih lewat studi kasus</h4><p>Siswa dihadapkan pada situasi nyata dan diminta menentukan sikap, lalu mendapat pembahasan.</p></div>
    <div class="tujuan-card"><div class="tujuan-num">03</div><h4>Melihat perkembangan sendiri</h4><p>Progres, nilai kuis, dan lencana membuat proses belajar terasa jelas arahnya.</p></div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
