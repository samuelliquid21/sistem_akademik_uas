<?php
require_once 'config.php';
require_once __DIR__ . '/../engine/inference.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = get_input();
    $mahasiswa_id = (int)($input['mahasiswa_id'] ?? 0);
    $user_id = (int)($input['user_id'] ?? 0);
    $dosen_id = (int)($input['dosen_id'] ?? 0);
    $P = (int)($input['P'] ?? 0);
    $Q = (int)($input['Q'] ?? 0);
    $R = (int)($input['R'] ?? 0);
    $S = (int)($input['S'] ?? 0);

    if (!$mahasiswa_id || !$user_id || !$dosen_id) {
        json_response(['error' => 'Data tidak lengkap'], 400);
    }

    $engine = new InferenceEngine($P, $Q, $R, $S);
    $result = $engine->run();

    try {
        $stmt = $pdo->prepare("INSERT INTO deteksi (mahasiswa_id, user_id, dosen_id, P, Q, R, S, pelanggaran_berat, peringatan_akademik, status_aman, status_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$mahasiswa_id, $user_id, $dosen_id, $P, $Q, $R, $S, (int)$result['pelanggaran_berat'], (int)$result['peringatan_akademik'], (int)$result['status_aman'], $result['status_label']]);
        $deteksi_id = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO deteksi_detail (deteksi_id, langkah, keterangan) VALUES (?, ?, ?)");
        foreach ($result['reasoning_path'] as $i => $step) {
            $stmt->execute([$deteksi_id, $i + 1, $step]);
        }

        // Get mahasiswa data
        $stmt = $pdo->prepare("SELECT m.*, k.nama_kelas FROM mahasiswa m JOIN kelas k ON k.id=m.kelas_id WHERE m.id=?");
        $stmt->execute([$mahasiswa_id]);
        $mhs = $stmt->fetch();

        $result['id'] = $deteksi_id;
        $result['nama'] = $mhs['nama'];
        $result['nim'] = $mhs['nim'];
        $result['nama_kelas'] = $mhs['nama_kelas'];
        json_response($result);
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }

} elseif ($method === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    $role = $_GET['role'] ?? '';
    $dosen_id = (int)($_GET['dosen_id'] ?? 0);
    $kelas_id = (int)($_GET['kelas_id'] ?? 0);
    $user_id = (int)($_GET['user_id'] ?? 0);

    try {
        if ($id) {
            $stmt = $pdo->prepare("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas, u.nama as nama_dosen FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id JOIN dosen ds ON ds.id=d.dosen_id JOIN users u ON u.id=ds.user_id WHERE d.id=?");
            $stmt->execute([$id]);
            $det = $stmt->fetch();
            if (!$det) json_response(['error' => 'Not found'], 404);
            $stmt = $pdo->prepare("SELECT * FROM deteksi_detail WHERE deteksi_id=? ORDER BY langkah ASC");
            $stmt->execute([$id]);
            $det['details'] = $stmt->fetchAll();
            json_response($det);
        } elseif ($role === 'admin') {
            $stmt = $pdo->query("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas, u.nama as nama_dosen FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id JOIN dosen ds ON ds.id=d.dosen_id JOIN users u ON u.id=ds.user_id ORDER BY d.created_at DESC LIMIT 200");
            json_response($stmt->fetchAll());
        } elseif ($role === 'dosen' && $dosen_id) {
            $stmt = $pdo->prepare("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id WHERE d.dosen_id=? ORDER BY d.created_at DESC");
            $stmt->execute([$dosen_id]);
            json_response($stmt->fetchAll());
        } elseif ($role === 'mahasiswa' && $user_id) {
            $stmt = $pdo->prepare("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id WHERE d.user_id=? ORDER BY d.created_at DESC");
            $stmt->execute([$user_id]);
            json_response($stmt->fetchAll());
        } else {
            json_response([], 400);
        }
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }
} else {
    json_response(['error' => 'Method not allowed'], 405);
}
