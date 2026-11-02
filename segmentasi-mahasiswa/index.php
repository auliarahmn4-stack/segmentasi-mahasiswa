<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/config/database.php';
$allowed=['dashboard','mahasiswa','clustering','hasil-clustering','evaluasi','laporan','profil','pengaturan','404'];
$page=$_GET['page']??'dashboard';
if(!in_array($page,$allowed,true)) $page='404';
$pageTitle=match($page){'dashboard'=>'Dashboard','mahasiswa'=>'Data Mahasiswa','clustering'=>'Proses Clustering','hasil-clustering'=>'Hasil Clustering','evaluasi'=>'Evaluasi Model','laporan'=>'Laporan Penelitian','profil'=>'Profil Akun','pengaturan'=>'Pengaturan',default=>'Halaman Tidak Ditemukan'};
$activePage=$page;
include __DIR__.'/includes/header.php';
include __DIR__.'/includes/sidebar.php';
include __DIR__.'/includes/navbar.php';
include __DIR__.'/pages/'.$page.'.php';
include __DIR__.'/includes/footer.php';
