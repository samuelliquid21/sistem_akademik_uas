<div class="space-y-6 fade-in">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 tracking-tight">Riwayat Deteksi</h3>
        </div>
        <div class="p-5">
            <?php
            if ($_SESSION['role'] === 'admin') {
                $stmt = $pdo->query("
                    SELECT d.*, m.nama as nama_mahasiswa, m.nim, u.nama as nama_user
                    FROM deteksi d
                    JOIN mahasiswa m ON d.mahasiswa_id = m.id
                    JOIN users u ON d.user_id = u.id
                    ORDER BY d.created_at DESC
                    LIMIT 100
                ");
            } else {
                $stmt = $pdo->prepare("
                    SELECT d.*, m.nama as nama_mahasiswa, m.nim
                    FROM deteksi d
                    JOIN mahasiswa m ON d.mahasiswa_id = m.id
                    WHERE d.user_id = ?
                    ORDER BY d.created_at DESC
                    LIMIT 100
                ");
                $stmt->execute([$_SESSION['user_id']]);
            }
            $riwayat = $stmt->fetchAll();
            ?>

            <?php if (empty($riwayat)): ?>
                <div class="text-center py-16 text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-lg font-medium text-slate-500">Belum ada riwayat deteksi</p>
                    <p class="text-sm mt-1">Lakukan deteksi pelanggaran terlebih dahulu.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-medium">
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Mahasiswa</th>
                                <th class="px-4 py-3 text-left">NIM</th>
                                <th class="px-4 py-3 text-center">P</th>
                                <th class="px-4 py-3 text-center">Q</th>
                                <th class="px-4 py-3 text-center">R</th>
                                <th class="px-4 py-3 text-center">S</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($riwayat as $r): 
                                $badge_color = match(true) {
                                    $r['pelanggaran_berat'] && $r['peringatan_akademik'] => 'bg-red-100 text-red-700 border-red-200',
                                    $r['pelanggaran_berat'] => 'bg-red-50 text-red-600 border-red-100',
                                    $r['peringatan_akademik'] => 'bg-amber-50 text-amber-600 border-amber-100',
                                    default => 'bg-green-50 text-green-600 border-green-100'
                                };
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                <td class="px-4 py-3 font-medium text-slate-900"><?= htmlspecialchars($r['nama_mahasiswa']) ?></td>
                                <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars($r['nim']) ?></td>
                                <td class="px-4 py-3 text-center font-bold <?= $r['P'] ? 'text-green-600' : 'text-red-600' ?>"><?= $r['P'] ? 1 : 0 ?></td>
                                <td class="px-4 py-3 text-center font-bold <?= $r['Q'] ? 'text-green-600' : 'text-red-600' ?>"><?= $r['Q'] ? 1 : 0 ?></td>
                                <td class="px-4 py-3 text-center font-bold <?= $r['R'] ? 'text-green-600' : 'text-red-600' ?>"><?= $r['R'] ? 1 : 0 ?></td>
                                <td class="px-4 py-3 text-center font-bold <?= $r['S'] ? 'text-green-600' : 'text-red-600' ?>"><?= $r['S'] ? 1 : 0 ?></td>
                                <td class="px-4 py-3"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $badge_color ?>"><?= htmlspecialchars($r['status_label']) ?></span></td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="showDetail(<?= $r['id'] ?>)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Lihat</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="font-semibold text-lg text-slate-900">Detail Reasoning Path</h3>
            <button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto" id="detailContent">
            Loading...
        </div>
    </div>
</div>

<script>
function showDetail(id) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
    document.getElementById('detailContent').innerHTML = 'Loading...';

    fetch('ajax_detail.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('detailContent').innerHTML = html;
        });
}

function closeDetail() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});
</script>
