<?php
require_once 'config.php';
$session = validate_token();
$userId = (int)$session['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT id, username, nama, role FROM users WHERE id=?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) json_response(['error' => 'User not found'], 404);

    if ($user['role'] === 'dosen') {
        $d = $pdo->prepare("SELECT d.nip, d.prodi, k.nama_kelas FROM dosen d LEFT JOIN kelas k ON k.dosen_id=d.id WHERE d.user_id=?");
        $d->execute([$userId]);
        $user = array_merge($user, $d->fetch() ?: []);
    } elseif ($user['role'] === 'mahasiswa') {
        $m = $pdo->prepare("SELECT m.nim, k.nama_kelas FROM mahasiswa m JOIN kelas k ON k.id=m.kelas_id WHERE m.user_id=?");
        $m->execute([$userId]);
        $user = array_merge($user, $m->fetch() ?: []);
    }

    json_response($user);
}

if ($method === 'PUT') {
    $input = get_input();
    $oldPw = $input['old_password'] ?? '';
    $newPw = $input['new_password'] ?? '';

    if (!$oldPw || !$newPw) json_response(['error' => 'Password lama dan baru harus diisi'], 400);
    if (strlen($newPw) < 4) json_response(['error' => 'Password baru minimal 4 karakter'], 400);

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!password_verify($oldPw, $user['password'])) {
        json_response(['error' => 'Password lama salah'], 400);
    }

    $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->execute([password_hash($newPw, PASSWORD_DEFAULT), $userId]);

    json_response(['success' => true, 'message' => 'Password berhasil diganti']);
}

json_response(['error' => 'Method not allowed'], 405);