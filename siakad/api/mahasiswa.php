<?php
require_once 'config.php';

$session = validate_token();
$role = $session['role'];
$user_id = (int)$session['user_id'];

$kelas_id = (int)($_GET['kelas_id'] ?? 0);

try {
    if ($role === 'admin') {
        $stmt = $pdo->query("SELECT m.*, k.nama_kelas, u.username FROM mahasiswa m JOIN kelas k ON k.id=m.kelas_id JOIN users u ON u.id=m.user_id ORDER BY m.nim ASC");
        json_response($stmt->fetchAll());
    } elseif ($role === 'dosen') {
        $stmt = $pdo->prepare("SELECT d.id as dosen_id FROM dosen d WHERE d.user_id=?");
        $stmt->execute([$user_id]);
        $dosenRow = $stmt->fetch();
        $dosen_id = $dosenRow ? (int)$dosenRow['dosen_id'] : 0;

        $stmt = $pdo->prepare("SELECT m.*, k.nama_kelas, u.username FROM mahasiswa m JOIN kelas k ON k.id=m.kelas_id JOIN users u ON u.id=m.user_id WHERE m.kelas_id=(SELECT k2.id FROM kelas k2 WHERE k2.dosen_id=?) ORDER BY m.nim ASC");
        $stmt->execute([$dosen_id]);
        json_response($stmt->fetchAll());
    } elseif ($kelas_id) {
        $stmt = $pdo->prepare("SELECT m.*, u.username FROM mahasiswa m JOIN users u ON u.id=m.user_id WHERE m.kelas_id=? ORDER BY m.nim ASC");
        $stmt->execute([$kelas_id]);
        json_response($stmt->fetchAll());
    } else {
        json_response([], 400);
    }
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}