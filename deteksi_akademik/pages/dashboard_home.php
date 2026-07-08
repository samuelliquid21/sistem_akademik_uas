<div class="space-y-6 fade-in">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM mahasiswa");
        $total_mahasiswa = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi");
        $total_deteksi = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi WHERE pelanggaran_berat = 1");
        $total_berat = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM deteksi WHERE status_aman = 1");
        $total_aman = $stmt->fetch()['total'];
        ?>
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Total Mahasiswa</p>
                    <h4 class="text-2xl font-bold text-slate-800"><?= $total_mahasiswa ?></h4>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Total Deteksi</p>
                    <h4 class="text-2xl font-bold text-slate-800"><?= $total_deteksi ?></h4>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Pelanggaran Berat</p>
                    <h4 class="text-2xl font-bold text-red-600"><?= $total_berat ?></h4>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Status Aman</p>
                    <h4 class="text-2xl font-bold text-green-600"><?= $total_aman ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 tracking-tight">Sistem Deteksi Pelanggaran Akademik</h3>
            </div>
            <div class="p-5">
                <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                    <p><strong>Sistem ini menggunakan Logika Proposisional</strong> untuk mendeteksi pelanggaran akademik mahasiswa berdasarkan 4 variabel:</p>
                    <div class="bg-slate-50 rounded-xl p-4 space-y-2 font-mono text-sm">
                        <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">P</span> Kehadiran mahasiswa &lt; 75%</div>
                        <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">Q</span> Terbukti melakukan plagiarisme</div>
                        <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">R</span> Terbukti menyontek saat ujian</div>
                        <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-green-100 text-green-700 flex items-center justify-center font-bold">S</span> Mengumpulkan tugas tepat waktu</div>
                    </div>
                    <p><strong>Metode Inferensi:</strong> Forward Chaining — sistem mengevaluasi setiap aturan secara berurutan dan menghasilkan keputusan berdasarkan fakta yang diberikan.</p>
                    <p><strong>Aturan Logika:</strong></p>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        <li>Rule 1: Q → Pelanggaran Berat</li>
                        <li>Rule 2: R → Pelanggaran Berat</li>
                        <li>Rule 3: (P ∧ ¬S) → Peringatan Akademik</li>
                        <li>Rule 4: ¬Q ∧ ¬R ∧ ¬(P ∧ ¬S) → Status Aman</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 tracking-tight">Aktivitas Terbaru</h3>
            </div>
            <div class="p-5">
                <?php
                $stmt = $pdo->query("
                    SELECT d.*, m.nama as nama_mahasiswa, m.nim 
                    FROM deteksi d 
                    JOIN mahasiswa m ON d.mahasiswa_id = m.id 
                    ORDER BY d.created_at DESC 
                    LIMIT 10
                ");
                $riwayat = $stmt->fetchAll();
                ?>
                <?php if (empty($riwayat)): ?>
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Belum ada aktivitas deteksi</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($riwayat as $r): ?>
                            <div class="flex items-start gap-3 pb-3 border-b border-slate-50 last:border-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold
                                    <?= $r['pelanggaran_berat'] ? 'bg-red-500' : ($r['peringatan_akademik'] ? 'bg-amber-500' : 'bg-green-500') ?>">
                                    <?= strtoupper(substr($r['nama_mahasiswa'], 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900 font-medium truncate"><?= htmlspecialchars($r['nama_mahasiswa']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($r['status_label']) ?></p>
                                </div>
                                <span class="text-xs text-slate-400 flex-shrink-0"><?= date('d/m H:i', strtotime($r['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
