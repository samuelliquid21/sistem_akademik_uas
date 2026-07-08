<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

try {
    $stmt = $pdo->query("SELECT * FROM gejala ORDER BY kode ASC");
    $gejala = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT * FROM rules ORDER BY id ASC");
    $rules = $stmt->fetchAll();

    json_response([
        'gejala' => $gejala,
        'rules' => $rules
    ]);
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
