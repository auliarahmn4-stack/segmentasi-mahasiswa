<?php
session_start();require_once __DIR__.'/../config/app.php';require_once __DIR__.'/../includes/functions.php';require_once __DIR__.'/../config/database.php';
if(empty($_SESSION['user_id'])){header('Location: '.APP_BASE.'/auth/login.php');exit;}if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('mahasiswa');}verify_csrf();
if(empty($_FILES['csv']['tmp_name'])||$_FILES['csv']['error']!==UPLOAD_ERR_OK){flash('danger','Pilih file CSV yang valid.');redirect('mahasiswa');}
$fh=fopen($_FILES['csv']['tmp_name'],'r');$header=fgetcsv($fh);$subjects=subjects($pdo);
if(!$header||count($header)!==15||strtolower(trim($header[0]))!=='kode_mahasiswa'||strtolower(trim($header[1]))!=='angkatan'){fclose($fh);flash('danger','CSV harus memiliki 15 kolom: kode_mahasiswa, angkatan, lalu 13 nilai sesuai urutan mata kuliah.');redirect('mahasiswa');}
$line=1;$good=0;$errors=[];$pdo->beginTransaction();
try{$up=$pdo->prepare('INSERT INTO mahasiswa(kode_mahasiswa,angkatan) VALUES(?,?) ON DUPLICATE KEY UPDATE angkatan=VALUES(angkatan),updated_at=NOW()');
$sel=$pdo->prepare('SELECT id FROM mahasiswa WHERE kode_mahasiswa=?');$del=$pdo->prepare('DELETE FROM nilai_mahasiswa WHERE mahasiswa_id=?');$ins=$pdo->prepare('INSERT INTO nilai_mahasiswa(mahasiswa_id,mata_kuliah_id,nilai) VALUES(?,?,?)');
while(($row=fgetcsv($fh))!==false){$line++;if(count($row)!==15){$errors[]="Baris $line: jumlah kolom tidak sesuai.";continue;}$kode=strtoupper(trim($row[0]));$angkatan=(int)$row[1];$vals=array_slice($row,2);$valid=preg_match('/^[A-Z0-9-]{3,20}$/',$kode)&&$angkatan>=2000&&$angkatan<=2100;foreach($vals as $v)if(!is_numeric($v)||(float)$v<0||(float)$v>100)$valid=false;if(!$valid){$errors[]="Baris $line: kode, angkatan, atau nilai tidak valid.";continue;}
$up->execute([$kode,$angkatan]);$sel->execute([$kode]);$id=(int)$sel->fetchColumn();$del->execute([$id]);foreach($subjects as $i=>$sub)$ins->execute([$id,$sub['id'],(float)$vals[$i]]);$good++;}
fclose($fh);$pdo->commit();log_activity($pdo,'IMPOR_CSV',"$good baris berhasil");$msg="$good baris berhasil diimpor.";if($errors)$msg.=' '.count($errors).' baris dilewati: '.implode(' ',$errors);flash($errors?'warning':'success',$msg);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('danger','Impor dibatalkan karena terjadi kesalahan. Periksa format CSV dan coba kembali.');}
redirect('mahasiswa');
