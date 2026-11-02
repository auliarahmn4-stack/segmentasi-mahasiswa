<?php
$total=(int)$pdo->query('SELECT COUNT(*) FROM mahasiswa')->fetchColumn();
$subCount=(int)$pdo->query('SELECT COUNT(*) FROM mata_kuliah')->fetchColumn();
$complete=(int)$pdo->query("SELECT COUNT(*) FROM (SELECT mahasiswa_id FROM nilai_mahasiswa GROUP BY mahasiswa_id HAVING COUNT(DISTINCT mata_kuliah_id)=$subCount) t")->fetchColumn();
$run=latest_run($pdo);$counts=[];
if($run){$s=$pdo->prepare('SELECT label_kompetensi,COUNT(*) jumlah FROM hasil_clustering WHERE riwayat_clustering_id=? GROUP BY label_kompetensi');$s->execute([$run['id']]);$counts=$s->fetchAll();}
$recent=array_slice(all_runs($pdo),0,5);
?>
<div class="page-heading"><div><div class="eyebrow">RINGKASAN SISTEM</div><h1>Dashboard</h1><p>Pantau data akademik simulasi dan proses segmentasi kompetensi.</p></div><a class="btn btn-primary" href="<?= url('clustering') ?>">Jalankan clustering <span>→</span></a></div>
<div class="notice"><strong>Mode data simulasi</strong><span>Seluruh data awal merupakan data dummy untuk pengembangan, bukan data akademik asli UIN Imam Bonjol Padang.</span></div>
<div class="stats-grid">
<div class="stat-card"><div class="stat-top"><span>Total mahasiswa</span><span class="stat-icon">M</span></div><div class="stat-number"><?= number_format($total) ?></div><div class="stat-foot">Data terdaftar di database</div></div>
<div class="stat-card"><div class="stat-top"><span>Nilai lengkap</span><span class="stat-icon">✓</span></div><div class="stat-number"><?= number_format($complete) ?><small> / <?= number_format($total) ?></small></div><div class="stat-foot">Memiliki seluruh <?= $subCount ?> atribut</div></div>
<div class="stat-card"><div class="stat-top"><span>Mata kuliah</span><span class="stat-icon">13</span></div><div class="stat-number"><?= $subCount ?></div><div class="stat-foot">Atribut analisis clustering</div></div>
<div class="stat-card"><div class="stat-top"><span>Cluster terakhir</span><span class="stat-icon">◎</span></div><div class="stat-number"><?= $run?(int)$run['jumlah_cluster']:'—' ?></div><div class="stat-foot"><?= $run?'Proses '.e(date('d M Y H:i',strtotime($run['created_at']))):'Clustering belum dijalankan' ?></div></div>
</div>
<div class="content-grid">
<section class="panel panel-wide"><div class="panel-heading"><div><h2>Distribusi kompetensi</h2><p>Hasil dari proses clustering terakhir</p></div><?php if($run): ?><a class="text-link" href="<?= url('hasil-clustering',['run'=>$run['id']]) ?>">Lihat hasil →</a><?php endif; ?></div>
<?php if(!$run): ?><div class="empty-state"><div class="empty-symbol">◎</div><h3>Belum ada hasil clustering</h3><p>Jalankan K-Means untuk menampilkan distribusi mahasiswa berdasarkan kompetensi.</p><a class="btn btn-primary" href="<?= url('clustering') ?>">Mulai proses</a></div>
<?php else: ?><div class="chart-wrap"><canvas id="clusterChart" aria-label="Grafik distribusi cluster"></canvas></div><div class="cluster-legend"><?php foreach($counts as $c): ?><div><span class="legend-dot"></span><span><?= e($c['label_kompetensi']) ?></span><strong><?= (int)$c['jumlah'] ?></strong></div><?php endforeach; ?></div><script type="application/json" id="clusterData"><?= json_encode(['labels'=>array_column($counts,'label_kompetensi'),'values'=>array_map('intval',array_column($counts,'jumlah'))]) ?></script><?php endif; ?>
</section>
<section class="panel"><div class="panel-heading"><div><h2>Metrik terakhir</h2><p>Evaluasi model terpilih</p></div></div>
<div class="metric-line"><span>Sum of Squared Errors</span><strong><?= $run?fmt((float)$run['sse'],3):'—' ?></strong></div><div class="metric-line"><span>Silhouette Coefficient</span><strong><?= $run&&$run['silhouette']!==null?fmt((float)$run['silhouette'],3):'—' ?></strong></div><div class="metric-line"><span>Jumlah data diproses</span><strong><?= $run?(int)$run['jumlah_data']:'—' ?></strong></div><div class="metric-line"><span>Status</span><strong><span class="pill <?= $run?'pill-success':'pill-muted' ?>"><?= $run?'Tersedia':'Belum ada' ?></span></strong></div><a class="btn btn-outline btn-full mt-16" href="<?= url('evaluasi') ?>">Buka evaluasi model</a></section>
</div>
<section class="panel mt-24"><div class="panel-heading"><div><h2>Riwayat proses</h2><p>Lima proses clustering terbaru</p></div><a class="text-link" href="<?= url('hasil-clustering') ?>">Semua hasil →</a></div>
<?php if(!$recent): ?><div class="empty-inline">Belum ada proses yang tercatat.</div><?php else: ?><div class="table-scroll"><table><thead><tr><th>Nama proses</th><th>Metode</th><th>K</th><th>Data</th><th>SSE</th><th>Waktu</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td><a class="table-link" href="<?= url('hasil-clustering',['run'=>$r['id']]) ?>"><?= e($r['nama_proses']) ?></a></td><td><?= e($r['algoritma']) ?></td><td><?= (int)$r['jumlah_cluster'] ?></td><td><?= (int)$r['jumlah_data'] ?></td><td><?= fmt((float)$r['sse'],3) ?></td><td><?= e(date('d M Y H:i',strtotime($r['created_at']))) ?></td><td><span class="pill pill-success"><?= e(ucfirst($r['status'])) ?></span></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
