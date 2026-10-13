<?php
require_once __DIR__.'/includes/bootstrap.php'; require_login('siswa');
$videos=$pdo->query("SELECT v.*,b.title AS bab_title,b.tag FROM materi_video v JOIN bab b ON b.id=v.bab_id ORDER BY b.urutan")->fetchAll();
$activeTab='video'; $pageTitle='Video Belajar'; require __DIR__.'/includes/header.php'; require __DIR__.'/includes/sidebar_siswa.php'; ?>
<div class="dash-head"><div><h2>Video belajar PPKn 📺</h2><p>Tonton, pahami, lalu coba hubungkan dengan kehidupan sehari-hari.</p></div></div>
<div class="video-grid"><?php foreach($videos as $v): ?><article class="video-card"><div class="video-frame"><iframe src="https://www.youtube-nocookie.com/embed/<?= h($v['youtube_id']) ?>" title="<?= h($v['judul']) ?>" allowfullscreen></iframe></div><span class="video-tag"><?= h($v['tag']) ?></span><h3><?= h($v['judul']) ?></h3><p><?= h($v['deskripsi']) ?></p></article><?php endforeach; ?></div>
</div></div><?php require __DIR__.'/includes/footer.php'; ?>
