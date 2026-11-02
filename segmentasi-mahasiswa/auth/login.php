<?php
session_start();
require_once __DIR__.'/../config/app.php';
require_once __DIR__.'/../includes/functions.php';
if(!empty($_SESSION['user_id'])) { header('Location: '.APP_BASE.'/index.php'); exit; }
$error=$_SESSION['login_error']??''; unset($_SESSION['login_error']);
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Masuk · SIMA Kompetensi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_BASE ?>/assets/css/style.css"><script defer src="<?= APP_BASE ?>/assets/js/script.js"></script></head>
<body class="login-body"><div class="login-layout"><section class="login-intro"><div class="login-logo"><span class="brand-mark">S</span> SIMA Kompetensi</div><div class="intro-content"><div class="eyebrow">SISTEM PENELITIAN AKADEMIK</div><h1>Memahami pola kompetensi melalui data.</h1><p>Platform segmentasi mahasiswa berbasis algoritma K-Means untuk mendukung analisis akademik yang terukur.</p><div class="intro-meta"><span>Data Mining</span><span>K-Means</span><span>Evaluasi Model</span></div></div><div class="intro-bottom">Program Studi Sistem Informasi · UIN Imam Bonjol Padang</div></section>
<section class="login-panel"><form class="login-card" action="<?= APP_BASE ?>/process/login-process.php" method="post"><?= csrf_field() ?><div class="eyebrow">SELAMAT DATANG</div><h2>Masuk ke akun</h2><p class="muted">Gunakan akun administrator untuk melanjutkan.</p>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<label for="identity">Username atau email</label><input id="identity" name="identity" autocomplete="username" required maxlength="190" placeholder="Masukkan username atau email">
<label for="password">Password</label><div class="password-wrap"><input id="password" type="password" name="password" autocomplete="current-password" required><button type="button" class="show-password" data-toggle-password="password">Lihat</button></div>
<button class="btn btn-primary btn-full" type="submit">Masuk ke dashboard <span>→</span></button><div class="login-note">Pastikan database sudah diimpor melalui phpMyAdmin.</div></form></section></div></body></html>
