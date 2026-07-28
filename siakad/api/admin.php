<?php
require_once 'config.php';
$session = validate_token();
if ($session['role'] !== 'admin') json_response(['error' => 'Forbidden'], 403);

$entity = $_GET['entity'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    // ============ MAHASISWA ============
    if ($entity === 'mahasiswa') {
        if ($method === 'POST') {
            $input = get_input();
            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, 'mahasiswa')");
            $stmt->execute([$input['username'], password_hash($input['password'], PASSWORD_DEFAULT), $input['nama']]);
            $userId = (int)$pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama, kelas_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $input['nim'], $input['nama'], (int)$input['kelas_id']]);
            json_response(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
        }
        if ($method === 'PUT') {
            $input = get_input();
            $id = (int)($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nim=?, nama=?, kelas_id=? WHERE id=?");
            $stmt->execute([$input['nim'], $input['nama'], (int)$input['kelas_id'], $id]);
            $q = $pdo->prepare("SELECT user_id FROM mahasiswa WHERE id=?");
            $q->execute([$id]);
            $mhs = $q->fetch();
            if ($mhs) {
                $stmt = $pdo->prepare("UPDATE users SET nama=? WHERE id=?");
                $stmt->execute([$input['nama'], $mhs['user_id']]);
                if (!empty($input['password'])) {
                    $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
                    $stmt->execute([password_hash($input['password'], PASSWORD_DEFAULT), $mhs['user_id']]);
                }
            }
            json_response(['success' => true]);
        }
        if ($method === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $mhs = $pdo->prepare("SELECT user_id FROM mahasiswa WHERE id=?");
            $mhs->execute([$id]);
            $m = $mhs->fetch();
            if ($m) {
                $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$m['user_id']]);
            }
            json_response(['success' => true]);
        }
    }

    // ============ DOSEN ============
    if ($entity === 'dosen') {
        if ($method === 'POST') {
            $input = get_input();
            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, 'dosen')");
            $stmt->execute([$input['username'], password_hash($input['password'], PASSWORD_DEFAULT), $input['nama']]);
            $userId = (int)$pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO dosen (user_id, nip, nama, prodi) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $input['nip'], $input['nama'], $input['prodi'] ?? '']);
            $dosenId = (int)$pdo->lastInsertId();
            if (!empty($input['kelas_id'])) {
                $pdo->prepare("UPDATE kelas SET dosen_id=? WHERE id=?")->execute([$dosenId, (int)$input['kelas_id']]);
            }
            json_response(['success' => true, 'id' => $dosenId]);
        }
        if ($method === 'PUT') {
            $input = get_input();
            $id = (int)($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE dosen SET nip=?, nama=?, prodi=? WHERE id=?");
            $stmt->execute([$input['nip'], $input['nama'], $input['prodi'] ?? '', $id]);
            $d = $pdo->prepare("SELECT user_id FROM dosen WHERE id=?");
            $d->execute([$id]);
            $dos = $d->fetch();
            if ($dos) {
                $stmt = $pdo->prepare("UPDATE users SET nama=? WHERE id=?");
                $stmt->execute([$input['nama'], $dos['user_id']]);
                if (!empty($input['password'])) {
                    $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
                    $stmt->execute([password_hash($input['password'], PASSWORD_DEFAULT), $dos['user_id']]);
                }
            }
            if (!empty($input['kelas_id'])) {
                $pdo->prepare("UPDATE kelas SET dosen_id=? WHERE id=?")->execute([$id, (int)$input['kelas_id']]);
            }
            json_response(['success' => true]);
        }
        if ($method === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $d = $pdo->prepare("SELECT user_id FROM dosen WHERE id=?");
            $d->execute([$id]);
            $dos = $d->fetch();
            if ($dos) {
                $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$dos['user_id']]);
            }
            json_response(['success' => true]);
        }
    }

    // ============ KELAS ============
    if ($entity === 'kelas') {
        if ($method === 'POST') {
            $input = get_input();
            $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, dosen_id) VALUES (?, ?)");
            $stmt->execute([$input['nama_kelas'], (int)$input['dosen_id']]);
            json_response(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
        }
        if ($method === 'PUT') {
            $input = get_input();
            $stmt = $pdo->prepare("UPDATE kelas SET nama_kelas=?, dosen_id=? WHERE id=?");
            $stmt->execute([$input['nama_kelas'], (int)$input['dosen_id'], (int)$input['id']]);
            json_response(['success' => true]);
        }
        if ($method === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $pdo->prepare("DELETE FROM kelas WHERE id=?")->execute([$id]);
            json_response(['success' => true]);
        }
    }

    json_response(['error' => 'Invalid entity or method'], 400);
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}