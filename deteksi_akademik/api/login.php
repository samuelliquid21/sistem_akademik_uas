<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$input = get_input();
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (!$username || !$password) {
    json_response(['error' => 'Username dan password harus diisi'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    json_response([
        'success' => true,
        'user' => [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'nama' => $user['nama'],
            'role' => $user['role']
        ]
    ]);
} else {
    json_response(['error' => 'Username atau password salah'], 401);
}
