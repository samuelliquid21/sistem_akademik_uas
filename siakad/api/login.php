<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$input = get_input();
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (!$username || !$password) json_response(['error' => 'Username dan password harus diisi'], 400);

$user = null;

$stmt = $pdo->prepare("SELECT u.*, m.nim FROM users u JOIN mahasiswa m ON m.user_id = u.id WHERE m.nim = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
}

if ($user && password_verify($password, $user['password'])) {
    $token = generate_token($user['id']);
    $data = [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'nama' => $user['nama'],
        'role' => $user['role'],
        'token' => $token
    ];

    if ($user['role'] === 'dosen') {
        $stmt = $pdo->prepare("SELECT d.id as dosen_id, d.nip, d.prodi, k.id as kelas_id, k.nama_kelas FROM dosen d LEFT JOIN kelas k ON k.dosen_id = d.id WHERE d.user_id = ?");
        $stmt->execute([$user['id']]);
        $dosen = $stmt->fetch();
        $data['dosen_id'] = (int)$dosen['dosen_id'];
        $data['nip'] = $dosen['nip'];
        $data['prodi'] = $dosen['prodi'];
        $data['kelas_id'] = (int)$dosen['kelas_id'];
        $data['nama_kelas'] = $dosen['nama_kelas'];
    }

    if ($user['role'] === 'mahasiswa') {
        $stmt = $pdo->prepare("SELECT m.id as mahasiswa_id, m.nim, m.kelas_id, k.nama_kelas FROM mahasiswa m JOIN kelas k ON k.id = m.kelas_id WHERE m.user_id = ?");
        $stmt->execute([$user['id']]);
        $mhs = $stmt->fetch();
        $data['mahasiswa_id'] = (int)$mhs['mahasiswa_id'];
        $data['nim'] = $mhs['nim'];
        $data['kelas_id'] = (int)$mhs['kelas_id'];
        $data['nama_kelas'] = $mhs['nama_kelas'];
        $data['username'] = $mhs['nim'];
    }

    json_response(['success' => true, 'user' => $data]);
} else {
    json_response(['error' => 'Username atau password salah'], 401);
}