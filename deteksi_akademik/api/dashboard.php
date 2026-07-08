<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM mahasiswa");
    $total_mahasiswa = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi");
    $total_deteksi = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi WHERE pelanggaran_berat = 1");
    $total_berat = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi WHERE peringatan_akademik = 1 AND pelanggaran_berat = 0");
    $total_peringatan = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi WHERE status_aman = 1");
    $total_aman = (int)$stmt->fetch()['total'];

    $stmt = $pdo->query("
        SELECT d.*, m.nama as nama_mahasiswa, m.nim 
        FROM deteksi d 
        JOIN mahasiswa m ON d.mahasiswa_id = m.id 
        ORDER BY d.created_at DESC 
        LIMIT 10
    ");
    $recent = $stmt->fetchAll();

    json_response([
        'stats' => [
            'total_mahasiswa' => $total_mahasiswa,
            'total_deteksi' => $total_deteksi,
            'total_berat' => $total_berat,
            'total_peringatan' => $total_peringatan,
            'total_aman' => $total_aman
        ],
        'recent' => $recent
    ]);
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
