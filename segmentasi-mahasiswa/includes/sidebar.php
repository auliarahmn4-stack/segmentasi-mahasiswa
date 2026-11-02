<aside class="sidebar" id="sidebar">
  <a class="brand" href="<?= url('dashboard') ?>"><span class="brand-mark">S</span><span><strong>SIMA</strong><small>Kompetensi Mahasiswa</small></span></a>
  <div class="side-label">MENU UTAMA</div>
  <?php $nav=[['dashboard','grid-2x2','Dashboard'],['mahasiswa','users','Data Mahasiswa'],['clustering','git-branch','Proses Clustering'],['hasil-clustering','chart-no-axes-combined','Hasil Clustering'],['evaluasi','chart-line','Evaluasi Model'],['laporan','file-text','Laporan']]; ?>
  <nav><?php foreach($nav as [$key,$icon,$label]): ?>
    <a class="nav-link <?= ($activePage??'')===$key?'active':'' ?>" href="<?= url($key) ?>"><?= e($label) ?></a>
  <?php endforeach; ?></nav>
  <div class="side-label side-label-bottom">PREFERENSI</div>
  <nav>  <a class="nav-link <?= ($activePage??'')==='profil'?'active':'' ?>" href="<?= url('profil') ?>">Profil Akun</a>
  <a class="nav-link <?= ($activePage??'')==='pengaturan'?'active':'' ?>" href="<?= url('pengaturan') ?>">Pengaturan</a></nav>
</aside>
