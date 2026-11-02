<?php
declare(strict_types=1);
$host = '127.0.0.1';
$db   = 'segmentasi_mahasiswa';
$user = 'root';
$pass = ''; // Sesuaikan dengan konfigurasi MySQL lokal Anda.
$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa config/database.php dan pastikan database sudah diimpor.');
}
