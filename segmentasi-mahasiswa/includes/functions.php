<?php
declare(strict_types=1);
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $page = 'dashboard', array $params = []): string {
    return APP_BASE . '/index.php?' . http_build_query(array_merge(['page'=>$page], $params));
}
function redirect(string $page = 'dashboard', array $params = []): never { header('Location: '.url($page,$params)); exit; }
function flash(string $type, string $message): void { $_SESSION['flash'] = ['type'=>$type,'message'=>$message]; }
function get_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419); exit('Token keamanan tidak valid. Muat ulang halaman lalu coba kembali.');
    }
}
function log_activity(PDO $pdo, string $action, string $detail=''): void {
    try {
        $s=$pdo->prepare('INSERT INTO log_aktivitas(user_id,aksi,detail,ip_address) VALUES(?,?,?,?)');
        $s->execute([$_SESSION['user_id']??null,$action,$detail,$_SERVER['REMOTE_ADDR']??'local']);
    } catch (Throwable $e) { /* logging tidak boleh menggagalkan proses utama */ }
}
function fmt(float|int|null $n, int $dec=2): string { return $n===null ? '—' : number_format((float)$n,$dec,',','.'); }
function subjects(PDO $pdo): array { return $pdo->query('SELECT * FROM mata_kuliah ORDER BY id')->fetchAll(); }
function complete_student_ids(PDO $pdo): array {
    $count=(int)$pdo->query('SELECT COUNT(*) FROM mata_kuliah')->fetchColumn();
    $s=$pdo->prepare('SELECT mahasiswa_id FROM nilai_mahasiswa GROUP BY mahasiswa_id HAVING COUNT(DISTINCT mata_kuliah_id)=? ORDER BY mahasiswa_id');
    $s->execute([$count]); return array_map('intval',$s->fetchAll(PDO::FETCH_COLUMN));
}
function student_matrix(PDO $pdo, ?array $ids=null): array {
    $sql='SELECT m.id,m.kode_mahasiswa,m.angkatan,mk.id AS mk_id,n.nilai FROM mahasiswa m CROSS JOIN mata_kuliah mk LEFT JOIN nilai_mahasiswa n ON n.mahasiswa_id=m.id AND n.mata_kuliah_id=mk.id';
    $params=[];
    if ($ids!==null) {
        if (!$ids) return [];
        $sql .= ' WHERE m.id IN ('.implode(',',array_fill(0,count($ids),'?')).')';
        $params=$ids;
    }
    $sql.=' ORDER BY m.id,mk.id';
    $st=$pdo->prepare($sql); $st->execute($params); $rows=$st->fetchAll();
    $out=[];
    foreach($rows as $r) {
        $id=(int)$r['id'];
        if(!isset($out[$id])) $out[$id]=['id'=>$id,'kode_mahasiswa'=>$r['kode_mahasiswa'],'angkatan'=>(int)$r['angkatan'],'values'=>[]];
        $out[$id]['values'][]=$r['nilai']===null?null:(float)$r['nilai'];
    }
    return array_values($out);
}
function latest_run(PDO $pdo): ?array {
    $r=$pdo->query("SELECT * FROM riwayat_clustering WHERE status='berhasil' ORDER BY id DESC LIMIT 1")->fetch();
    return $r ?: null;
}
function run_is_stale(PDO $pdo, int $runId): bool {
    $q=$pdo->prepare('SELECT created_at FROM riwayat_clustering WHERE id=?'); $q->execute([$runId]); $created=$q->fetchColumn();
    if(!$created) return true;
    $q=$pdo->prepare('SELECT MAX(updated_at) FROM nilai_mahasiswa'); $q->execute(); $last=$q->fetchColumn();
    $q=$pdo->query('SELECT MAX(updated_at) FROM mahasiswa')->fetchColumn();
    $lastStudent=$q ?: null;
    return ($last && $last > $created) || ($lastStudent && $lastStudent > $created);
}
function all_runs(PDO $pdo): array { return $pdo->query("SELECT r.*,u.nama AS pembuat FROM riwayat_clustering r LEFT JOIN users u ON u.id=r.created_by ORDER BY r.id DESC")->fetchAll(); }
