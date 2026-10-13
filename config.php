<?php
// ==== Konfigurasi Database ====
// Sesuaikan dengan pengaturan MySQL/MariaDB di komputer/hosting kamu.
$DB_HOST = 'localhost';
$DB_NAME = 'ppkn_platform';
$DB_USER = 'root';
$DB_PASS = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan schema.sql sudah diimpor. Detail: ' . $e->getMessage());
}
