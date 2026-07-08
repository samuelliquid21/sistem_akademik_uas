<?php
require_once 'config.php';
require_once __DIR__ . '/../engine/inference.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = get_input();
    $user_id = (int)($input['user_id'] ?? 0);
    $nama = $input['nama'] ?? '';
    $nim = $input['nim'] ?? '';
    $P = (int)($input['P'] ?? 0);
    $Q = (int)($input['Q'] ?? 0);
    $R = (int)($input['R'] ?? 0);
    $S = (int)($input['S'] ?? 0);

    if (!$user_id || !$nama || !$nim) {
        json_response(['error' => 'Data tidak lengkap'], 400);
    }

    // Run inference
    $engine = new InferenceEngine($P, $Q, $R, $S);
    $result = $engine->run();

    try {
        // Cek atau buat mahasiswa
        $stmt = $pdo->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
        $stmt->execute([$nim]);
        $mhs_db = $stmt->fetch();

        if (!$mhs_db) {
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $nim, $nama]);
            $mahasiswa_id = (int)$pdo->lastInsertId();
        } else {
            $mahasiswa_id = (int)$mhs_db['id'];
            // Update nama
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nama = ? WHERE id = ?");
            $stmt->execute([$nama, $mahasiswa_id]);
        }

        // Simpan deteksi
        $stmt = $pdo->prepare("INSERT INTO deteksi (mahasiswa_id, user_id, P, Q, R, S, pelanggaran_berat, peringatan_akademik, status_aman, status_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $mahasiswa_id, $user_id,
            $P, $Q, $R, $S,
            (int)$result['pelanggaran_berat'],
            (int)$result['peringatan_akademik'],
            (int)$result['status_aman'],
            $result['status_label']
        ]);
        $deteksi_id = (int)$pdo->lastInsertId();

        // Simpan reasoning path
        $stmt = $pdo->prepare("INSERT INTO deteksi_detail (deteksi_id, langkah, keterangan) VALUES (?, ?, ?)");
        foreach ($result['reasoning_path'] as $i => $step) {
            $stmt->execute([$deteksi_id, $i + 1, $step]);
        }

        $result['id'] = $deteksi_id;
        $result['nama'] = $nama;
        $result['nim'] = $nim;
        json_response($result);
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }

} elseif ($method === 'GET') {
    $user_id = (int)($_GET['user_id'] ?? 0);
    $id = (int)($_GET['id'] ?? 0);
    $role = $_GET['role'] ?? 'mahasiswa';

    try {
        if ($id) {
            // Get single detection with details
            $stmt = $pdo->prepare("
                SELECT d.*, m.nama as nama_mahasiswa, m.nim
                FROM deteksi d
                JOIN mahasiswa m ON d.mahasiswa_id = m.id
                WHERE d.id = ?
            ");
            $stmt->execute([$id]);
            $deteksi = $stmt->fetch();

            if (!$deteksi) {
                json_response(['error' => 'Data tidak ditemukan'], 404);
            }

            $stmt = $pdo->prepare("SELECT * FROM deteksi_detail WHERE deteksi_id = ? ORDER BY langkah ASC");
            $stmt->execute([$id]);
            $deteksi['details'] = $stmt->fetchAll();

            json_response($deteksi);
        } else {
            if ($role === 'admin') {
                $stmt = $pdo->query("
                    SELECT d.*, m.nama as nama_mahasiswa, m.nim
                    FROM deteksi d
                    JOIN mahasiswa m ON d.mahasiswa_id = m.id
                    ORDER BY d.created_at DESC
                    LIMIT 100
                ");
            } else {
                $stmt = $pdo->prepare("
                    SELECT d.*, m.nama as nama_mahasiswa, m.nim
                    FROM deteksi d
                    JOIN mahasiswa m ON d.mahasiswa_id = m.id
                    WHERE d.user_id = ?
                    ORDER BY d.created_at DESC
                    LIMIT 100
                ");
                $stmt->execute([$user_id]);
            }
            json_response($stmt->fetchAll());
        }
    } catch (Exception $e) {
        json_response(['error' => $e->getMessage()], 500);
    }

} else {
    json_response(['error' => 'Method not allowed'], 405);
}
