<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('siswa');
$user = current_user();
if (isset($_GET['reset'])) unset($_SESSION['latihan_acak']);
if (isset($_GET['mulai'])) {
    $soal = [];
    foreach ($pdo->query("SELECT id FROM bab ORDER BY urutan")->fetchAll() as $bab) {
        $stmt = $pdo->prepare("SELECT s.*, b.title AS materi FROM soal s JOIN bab b ON b.id=s.bab_id WHERE s.bab_id=? ORDER BY RAND() LIMIT 2");
        $stmt->execute([$bab['id']]);
        $soal = array_merge($soal, $stmt->fetchAll());
    }
    shuffle($soal);
    $_SESSION['latihan_acak'] = ['soal'=>$soal,'index'=>0,'score'=>0,'answered'=>false,'chosen'=>null,'finished'=>false,'started_at'=>time(),'saved'=>false];
}

$activeTab='kuis'; $pageTitle='Latihan Acak'; require __DIR__.'/includes/header.php'; require __DIR__.'/includes/sidebar_siswa.php';
if (empty($_SESSION['latihan_acak'])): ?>
<div class="quiz-wrap"><div class="quiz-card quiz-result challenge-start"><div class="result-icon">🧠</div><span class="eyebrow">TANTANGAN CAMPURAN</span><h2>Siap uji pemahamanmu?</h2><p>Kerjakan <b>10 soal dalam 10 menit</b>. Setiap bab PPKn akan muncul dua kali, jadi semua materi terwakili.</p><a class="btn btn-primary" href="kuis.php?mulai=1">▶ Mulai latihan</a><p class="mini-note">Tenang, contoh soal pada halaman Materi tidak memakai batas waktu.</p></div></div></div></div><?php require __DIR__.'/includes/footer.php'; exit; ?>
<?php endif;
$quiz = &$_SESSION['latihan_acak'];
$expired = time() >= $quiz['started_at'] + 600;
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='jawab' && !$quiz['answered'] && !$quiz['finished'] && !$expired) {
    $choice=$_POST['pilihan']??'';
    if(in_array($choice,['a','b','c','d'],true)){ $quiz['answered']=true; $quiz['chosen']=$choice; if($choice===$quiz['soal'][$quiz['index']]['jawaban_benar']) $quiz['score']++; }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='lanjut' && $quiz['answered']) {
    if($quiz['index']+1>=count($quiz['soal'])) $quiz['finished']=true;
    else { $quiz['index']++; $quiz['answered']=false; $quiz['chosen']=null; }
}
if($expired) $quiz['finished']=true;
if($quiz['finished'] && !$quiz['saved']) { $total=count($quiz['soal']); $stmt=$pdo->prepare("INSERT INTO kuis_hasil (user_id,bab_id,skor,total_soal,poin) VALUES (?,1,?,?,?)"); $stmt->execute([$user['id'],$quiz['score'],$total,$quiz['score']*10]); $quiz['saved']=true; }
if(empty($quiz['soal'])): ?>
<div class="panel"><h3>Bank soal belum cukup</h3><p>Guru perlu menambahkan minimal 10 soal.</p></div>
<?php elseif($quiz['finished']): $total=count($quiz['soal']); $pct=round($quiz['score']/$total*100); ?>
<div class="quiz-wrap"><div class="quiz-card quiz-result"><div class="result-icon">🏆</div><div style="font-size:13px;color:#776f60">Tantangan selesai<?= $expired?' — waktu habis':'' ?></div><div class="score"><?= $pct ?></div><p>Kamu benar <?= $quiz['score'] ?> dari <?= $total ?> soal dan mendapat <b>+<?= $quiz['score']*10 ?> poin</b>.</p><a class="btn btn-primary" href="kuis.php?mulai=1">🔄 Main lagi</a></div></div>
<?php else: $item=$quiz['soal'][$quiz['index']]; $seconds=max(0,600-(time()-$quiz['started_at'])); ?>
<div class="quiz-wrap"><div class="challenge-head"><div><span class="eyebrow">TANTANGAN CAMPURAN</span><h2>10 soal, 10 menit!</h2></div><div class="timer" id="timer" data-seconds="<?= $seconds ?>">10:00</div></div><div class="quiz-progress"><span>Soal <?= $quiz['index']+1 ?> / <?= count($quiz['soal']) ?> · <?= h($item['materi']) ?></span><span>⭐ <?= $quiz['score']*10 ?> poin</span></div><div class="quiz-card"><p class="quiz-q"><?= h($item['pertanyaan']) ?></p><form method="post"><input type="hidden" name="action" value="jawab"><?php foreach(['a','b','c','d'] as $opt): $cls=''; if($quiz['answered']) $cls=$opt===$item['jawaban_benar']?'correct':($opt===$quiz['chosen']?'wrong':''); ?><button class="quiz-opt <?= $cls ?>" type="submit" name="pilihan" value="<?= $opt ?>" <?= $quiz['answered']?'disabled':'' ?>><b><?= strtoupper($opt) ?></b> <?= h($item['pilihan_'.$opt]) ?></button><?php endforeach; ?></form><?php if($quiz['answered']): ?><div class="quiz-explain"><?= $quiz['chosen']===$item['jawaban_benar']?'✅ Keren! ':'💡 Yuk ingat lagi. ' ?><?= h($item['penjelasan']) ?></div><form method="post"><input type="hidden" name="action" value="lanjut"><button class="btn btn-primary" style="margin-top:16px"><?= $quiz['index']===count($quiz['soal'])-1?'Lihat hasil':'Lanjut soal' ?> →</button></form><?php endif; ?></div><a class="link-action" style="display:block;margin-top:12px;text-align:center" href="kuis.php?reset=1">Batalkan latihan</a></div>
<script>const t=document.querySelector('#timer');let s=Number(t.dataset.seconds);function tick(){t.textContent=Math.floor(s/60)+':'+String(s%60).padStart(2,'0');if(s--<=0)location.reload()}tick();setInterval(tick,1000)</script>
<?php endif; ?></div></div><?php require __DIR__.'/includes/footer.php'; ?>
