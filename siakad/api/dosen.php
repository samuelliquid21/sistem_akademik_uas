<?php
require_once 'config.php';
$session = validate_token();

$stmt = $pdo->query("SELECT d.*, u.nama as nama_user, u.username, k.nama_kelas FROM dosen d JOIN users u ON u.id=d.user_id LEFT JOIN kelas k ON k.dosen_id=d.id ORDER BY d.nama ASC");
json_response($stmt->fetchAll());