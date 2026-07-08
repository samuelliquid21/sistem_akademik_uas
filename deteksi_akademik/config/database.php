<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_pelanggaran_akademik';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function base_url() {
    return '/logika_akademik/deteksi_akademik';
}

function redirect($path) {
    header("Location: $path");
    exit;
}

function cek_login() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }
}
