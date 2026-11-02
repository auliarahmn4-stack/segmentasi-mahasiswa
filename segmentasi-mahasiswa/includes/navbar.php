<header class="topbar">
  <button class="icon-btn mobile-menu" id="menuToggle" aria-label="Buka menu">☰</button>
  <div class="breadcrumb">Sistem Informasi <span>/</span> <strong><?= e($pageTitle ?? 'Dashboard') ?></strong></div>
  <div class="top-user"><div class="avatar"><?= e(strtoupper(substr($_SESSION['nama']??'A',0,1))) ?></div><div class="user-copy"><strong><?= e($_SESSION['nama']??'Administrator') ?></strong><small>Administrator</small></div><a class="logout-link" href="<?= APP_BASE ?>/process/logout-process.php" title="Keluar">Keluar</a></div>
</header>
<main class="main-content">
<?php if($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?><button class="alert-close" data-dismiss>×</button></div><?php endif; ?>
