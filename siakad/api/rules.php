<?php
require_once 'config.php';
$session = validate_token();

$stmt = $pdo->query("SELECT * FROM gejala ORDER BY kode ASC");
$gejala = $stmt->fetchAll();
$stmt = $pdo->query("SELECT * FROM rules ORDER BY id ASC");
$rules = $stmt->fetchAll();
json_response(['gejala' => $gejala, 'rules' => $rules]);