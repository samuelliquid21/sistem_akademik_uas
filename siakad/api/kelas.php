<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT k.*, d.nama as nama_dosen, d.nip, u.nama as nama_user FROM kelas k JOIN dosen d ON d.id=k.dosen_id JOIN users u ON u.id=d.user_id ORDER BY k.nama_kelas ASC");
$kelas = $stmt->fetchAll();
foreach ($kelas as &$k) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as t FROM mahasiswa WHERE kelas_id=?");
    $stmt->execute([$k['id']]);
    $k['total_mahasiswa'] = (int)$stmt->fetch()['t'];
}
json_response($kelas);
