<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("
            SELECT m.*, (SELECT COUNT(*) FROM deteksi d WHERE d.mahasiswa_id = m.id) as total_deteksi 
            FROM mahasiswa m 
            ORDER BY m.nama ASC
        ");
        json_response($stmt->fetchAll());

    } elseif ($method === 'POST') {
        $input = get_input();
        $action = $input['action'] ?? '';

        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id = ?");
            $stmt->execute([$input['id']]);
            json_response(['success' => true, 'message' => 'Mahasiswa berhasil dihapus']);
        } else {
            $id = (int)($input['id'] ?? 0);
            $nim = $input['nim'] ?? '';
            $nama = $input['nama'] ?? '';

            if (!$nim || !$nama) {
                json_response(['error' => 'Data tidak lengkap'], 400);
            }

            if ($id) {
                $stmt = $pdo->prepare("UPDATE mahasiswa SET nim = ?, nama = ? WHERE id = ?");
                $stmt->execute([$nim, $nama, $id]);
                json_response(['success' => true, 'message' => 'Data mahasiswa diperbarui']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)");
                $stmt->execute([$input['user_id'] ?? 1, $nim, $nama]);
                json_response(['success' => true, 'message' => 'Mahasiswa berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
            }
        }
    } else {
        json_response(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
