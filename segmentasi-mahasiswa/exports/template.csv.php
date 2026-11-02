<?php
session_start();require_once __DIR__.'/../config/app.php';require_once __DIR__.'/../includes/functions.php';require_once __DIR__.'/../config/database.php';if(empty($_SESSION['user_id'])){http_response_code(403);exit('Forbidden');}
header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="template_segmentasi_mahasiswa.csv"');$out=fopen('php://output','w');$header=['kode_mahasiswa','angkatan'];foreach(subjects($pdo) as $s)$header[]=$s['nama_mata_kuliah'];fputcsv($out,$header);fclose($out);
