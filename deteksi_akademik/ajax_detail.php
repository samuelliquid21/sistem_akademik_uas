<?php
require_once 'config/database.php';
cek_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT dd.langkah, dd.keterangan FROM deteksi_detail dd WHERE dd.deteksi_id = ? ORDER BY dd.langkah ASC");
$stmt->execute([$id]);
$details = $stmt->fetchAll();

if (empty($details)) {
    echo '<p class="text-slate-400 text-center py-8">Tidak ada detail reasoning path.</p>';
    exit;
}

echo '<div class="space-y-1 font-mono text-sm">';
foreach ($details as $d) {
    $k = $d['keterangan'];
    if (strpos($k, '===') === 0) {
        echo '<p class="font-bold text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg -mx-3 mt-2 mb-1">' . htmlspecialchars($k) . '</p>';
    } elseif (strpos($k, 'Rule') === 0) {
        echo '<p class="text-blue-700 font-medium">' . htmlspecialchars($k) . '</p>';
    } elseif (strpos($k, 'TERPENUHI') !== false) {
        echo '<p class="text-green-700 ml-4">' . htmlspecialchars($k) . '</p>';
    } elseif (strpos($k, 'tidak') !== false) {
        echo '<p class="text-slate-500 ml-4">' . htmlspecialchars($k) . '</p>';
    } else {
        echo '<p class="text-slate-600">' . htmlspecialchars($k) . '</p>';
    }
}
echo '</div>';
