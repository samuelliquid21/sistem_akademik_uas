<?php
require_once 'config.php';

$session = validate_token();
$role = $session['role'];
$user_id = (int)$session['user_id'];

try {
    if ($role === 'admin') {
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM mahasiswa"); $total_mhs = (int)$stmt->fetch()['t'];
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM dosen"); $total_dosen = (int)$stmt->fetch()['t'];
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM kelas"); $total_kelas = (int)$stmt->fetch()['t'];
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM deteksi"); $total_deteksi = (int)$stmt->fetch()['t'];
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM deteksi WHERE pelanggaran_berat=1"); $berat = (int)$stmt->fetch()['t'];
        $stmt = $pdo->query("SELECT COUNT(*) as t FROM deteksi WHERE status_aman=1"); $aman = (int)$stmt->fetch()['t'];

        $stmt = $pdo->query("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas, u.nama as nama_dosen FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id JOIN dosen ds ON ds.id=d.dosen_id JOIN users u ON u.id=ds.user_id ORDER BY d.created_at DESC LIMIT 15");
        $recent = $stmt->fetchAll();

        json_response(['stats' => ['total_mahasiswa'=>$total_mhs,'total_dosen'=>$total_dosen,'total_kelas'=>$total_kelas,'total_deteksi'=>$total_deteksi,'total_berat'=>$berat,'total_aman'=>$aman], 'recent' => $recent]);
    } elseif ($role === 'dosen') {
        $stmt = $pdo->prepare("SELECT d.id as dosen_id FROM dosen d WHERE d.user_id=?");
        $stmt->execute([$user_id]);
        $dosenRow = $stmt->fetch();
        $dosen_id = $dosenRow ? (int)$dosenRow['dosen_id'] : 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM mahasiswa WHERE kelas_id=(SELECT kelas_id FROM dosen d JOIN kelas k ON k.dosen_id=d.id WHERE d.id=?)");
        $stmt->execute([$dosen_id]); $total_mhs = (int)$stmt->fetch()['t'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM deteksi WHERE dosen_id=?");
        $stmt->execute([$dosen_id]); $total_deteksi = (int)$stmt->fetch()['t'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM deteksi WHERE dosen_id=? AND pelanggaran_berat=1");
        $stmt->execute([$dosen_id]); $berat = (int)$stmt->fetch()['t'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM deteksi WHERE dosen_id=? AND status_aman=1");
        $stmt->execute([$dosen_id]); $aman = (int)$stmt->fetch()['t'];

        $stmt = $pdo->prepare("SELECT d.*, m.nama as nama_mahasiswa, m.nim FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id WHERE d.dosen_id=? ORDER BY d.created_at DESC LIMIT 15");
        $stmt->execute([$dosen_id]); $recent = $stmt->fetchAll();

        json_response(['stats' => ['total_mahasiswa'=>$total_mhs,'total_deteksi'=>$total_deteksi,'total_berat'=>$berat,'total_aman'=>$aman], 'recent' => $recent]);
    } elseif ($role === 'mahasiswa') {
        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM deteksi WHERE user_id=?"); $stmt->execute([$user_id]); $total = (int)$stmt->fetch()['t'];
        $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM deteksi WHERE user_id=? AND pelanggaran_berat=1"); $stmt->execute([$user_id]); $berat = (int)$stmt->fetch()['t'];
        $stmt = $pdo->prepare("SELECT * FROM deteksi WHERE user_id=? ORDER BY created_at DESC"); $stmt->execute([$user_id]); $recent = $stmt->fetchAll();
        json_response(['stats' => ['total_deteksi'=>$total,'total_berat'=>$berat], 'recent' => $recent]);
    } else {
        json_response(['error' => 'Invalid role'], 400);
    }
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}