<?php
session_start();require_once __DIR__.'/../config/app.php';require_once __DIR__.'/../includes/functions.php';require_once __DIR__.'/../config/database.php';require_once __DIR__.'/../services/KMeans.php';
if(empty($_SESSION['user_id'])){header('Location: '.APP_BASE.'/auth/login.php');exit;}if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('clustering');}verify_csrf();
$k=(int)($_POST['k']??3);$max=(int)($_POST['max_iter']??100);$tol=(float)($_POST['tolerance']??0.0001);$seed=(int)($_POST['seed']??42);
try{
 if($k<2||$k>6||$max<1||$max>1000||$tol<=0||$tol>1)throw new RuntimeException('Parameter clustering di luar batas yang diperbolehkan.');
 $data=student_matrix($pdo);$valid=array_values(array_filter($data,fn($r)=>!in_array(null,$r['values'],true)));
 if(count($valid)<$k)throw new RuntimeException('Data nilai lengkap tidak cukup untuk jumlah cluster yang dipilih.');
 $raw=array_map(fn($r)=>$r['values'],$valid);$result=KMeans::fit($raw,$k,$max,$tol,$seed);
 $pdo->beginTransaction();$name='K-Means '.date('d/m/Y H:i:s');
 $s=$pdo->prepare("INSERT INTO riwayat_clustering(nama_proses,algoritma,jumlah_cluster,metode_standardisasi,jumlah_data,sse,silhouette,status,parameter_json,created_by) VALUES(?,?,?,?,?,?,?,'berhasil',?,?)");
 $params=['max_iter'=>$max,'tolerance'=>$tol,'seed'=>$seed,'iterations'=>$result['iterations'],'attributes'=>count($raw[0])];
 $s->execute([$name,'K-Means',$k,'Z-score',$valid?count($valid):0,$result['sse'],$result['silhouette']===null?null:$result['silhouette'],json_encode($params),$_SESSION['user_id']]);$run=(int)$pdo->lastInsertId();
 $hr=$pdo->prepare('INSERT INTO hasil_clustering(riwayat_clustering_id,mahasiswa_id,nomor_cluster,label_kompetensi) VALUES(?,?,?,?)');
 foreach($valid as $i=>$student)$hr->execute([$run,$student['id'],$result['labels'][$i]+1,$result['cluster_names'][$result['labels'][$i]]]);
 $cr=$pdo->prepare('INSERT INTO centroid_clustering(riwayat_clustering_id,nomor_cluster,mata_kuliah_id,nilai_centroid) VALUES(?,?,?,?)');$subs=subjects($pdo);
 foreach($result['centroid_raw'] as $c=>$center)foreach($subs as $j=>$sub)$cr->execute([$run,$c+1,$sub['id'],$center[$j]]);
 $pdo->commit();log_activity($pdo,'CLUSTERING',"Run $run; K=$k; n=".count($valid));flash('success',"Clustering berhasil. {$result['iterations']} iterasi dijalankan pada ".count($valid)." mahasiswa lengkap.");redirect('hasil-clustering',['run'=>$run]);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('danger',$e->getMessage());redirect('clustering');}
