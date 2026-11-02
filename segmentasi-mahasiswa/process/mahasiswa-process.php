<?php
session_start();require_once __DIR__.'/../config/app.php';require_once __DIR__.'/../includes/functions.php';require_once __DIR__.'/../config/database.php';
if(empty($_SESSION['user_id'])){header('Location: '.APP_BASE.'/auth/login.php');exit;}if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('mahasiswa');}verify_csrf();
$action=$_POST['action']??'';$id=(int)($_POST['id']??0);
try{
 if($action==='delete'){$s=$pdo->prepare('DELETE FROM mahasiswa WHERE id=?');$s->execute([$id]);log_activity($pdo,'HAPUS_MAHASISWA','ID '.$id);flash('success','Data mahasiswa berhasil dihapus.');}
 elseif(in_array($action,['create','update'],true)){
  $kode=strtoupper(trim((string)($_POST['kode_mahasiswa']??'')));$angkatan=(int)($_POST['angkatan']??0);
  if(!preg_match('/^[A-Z0-9-]{3,20}$/',$kode)||$angkatan<2000||$angkatan>2100)throw new RuntimeException('Kode mahasiswa atau angkatan tidak valid.');
  $values=$_POST['nilai']??[];$subs=subjects($pdo);if(count($values)!==count($subs))throw new RuntimeException('Semua 13 nilai mata kuliah harus diisi.');
  foreach($values as $v)if($v===''||!is_numeric($v)||(float)$v<0||(float)$v>100)throw new RuntimeException('Nilai harus berupa angka 0 sampai 100.');
  $pdo->beginTransaction();
  if($action==='create'){$s=$pdo->prepare('INSERT INTO mahasiswa(kode_mahasiswa,angkatan) VALUES(?,?)');$s->execute([$kode,$angkatan]);$id=(int)$pdo->lastInsertId();}
  else{$s=$pdo->prepare('UPDATE mahasiswa SET kode_mahasiswa=?,angkatan=?,updated_at=NOW() WHERE id=?');$s->execute([$kode,$angkatan,$id]);}
  $del=$pdo->prepare('DELETE FROM nilai_mahasiswa WHERE mahasiswa_id=?');$del->execute([$id]);
  $ins=$pdo->prepare('INSERT INTO nilai_mahasiswa(mahasiswa_id,mata_kuliah_id,nilai) VALUES(?,?,?)');foreach($subs as $i=>$sub)$ins->execute([$id,$sub['id'],(float)$values[$sub['id']]]);
  $pdo->commit();log_activity($pdo,strtoupper($action).'_MAHASISWA',$kode);flash('success',$action==='create'?'Data mahasiswa berhasil ditambahkan.':'Data mahasiswa berhasil diperbarui.');
 }
 redirect('mahasiswa');
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('danger',$e instanceof PDOException?'Data gagal disimpan. Pastikan kode mahasiswa tidak duplikat.':$e->getMessage());redirect('mahasiswa');}
