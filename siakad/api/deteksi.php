<?php
require_once 'config.php';
require_once __DIR__ . '/../engine/inference.php';

$session = validate_token();
$role = $session['role'];
$user_id = (int)$session['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if ($role !== 'dosen' && $role !== 'admin') {
        json_response(['error' => 'Forbidden'], 403);
    }

    $input = get_input();
    $mahasiswa_id = (int)($input['mahasiswa_id'] ?? 0);
    $P = (int)($input['P'] ?? 0);
    $Q = (int)($input['Q'] ?? 0);
    $R = (int)($input['R'] ?? 0);
    $S = (int)($input['S'] ?? 0);

    if (!$mahasiswa_id) {
        json_response(['error' => 'Data tidak lengkap'], 400);
    }

    // Get dosen_id from session
    $stmt = $pdo->prepare("SELECT d.id as dosen_id FROM dosen d WHERE d.user_id=?");
    $stmt->execute([$user_id]);
    $dosenRow = $stmt->fetch();
    $dosen_id = $dosenRow ? (int)$dosenRow['dosen_id'] : 0;

    if (!$dosen_id) {
        json_response(['error' => 'Dosen tidak ditemukan'], 400);
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
        } elseif ($role === 'dosen') {
            $stmt = $pdo->prepare("SELECT d.id as dosen_id FROM dosen d WHERE d.user_id=?");
            $stmt->execute([$user_id]);
            $dosenRow = $stmt->fetch();
            $dosen_id = $dosenRow ? (int)$dosenRow['dosen_id'] : 0;

            $stmt = $pdo->prepare("SELECT d.*, m.nama as nama_mahasiswa, m.nim, k.nama_kelas FROM deteksi d JOIN mahasiswa m ON d.mahasiswa_id=m.id JOIN kelas k ON k.id=m.kelas_id WHERE d.dosen_id=? ORDER BY d.created_at DESC");
            $stmt->execute([$dosen_id]);
            json_response($stmt->fetchAll());
        } elseif ($role === 'mahasiswa') {
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