<?php
require_once 'engine/inference.php';

$result = null;
$mhs_nama = '';
$mhs_nim = '';
$input_P = 0; $input_Q = 0; $input_R = 0; $input_S = 0;

// Cari mahasiswa berdasarkan user login
$stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$mahasiswa = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mhs_nama = $_POST['nama'] ?? '';
    $mhs_nim = $_POST['nim'] ?? '';
    $input_P = (int)($_POST['P'] ?? 0);
    $input_Q = (int)($_POST['Q'] ?? 0);
    $input_R = (int)($_POST['R'] ?? 0);
    $input_S = (int)($_POST['S'] ?? 0);

    // Jalankan inference engine
    $engine = new InferenceEngine($input_P, $input_Q, $input_R, $input_S);
    $result = $engine->run();

    // Simpan ke database
    try {
        // Cek atau buat mahasiswa
        $stmt = $pdo->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
        $stmt->execute([$mhs_nim]);
        $mhs_db = $stmt->fetch();

        if (!$mhs_db) {
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $mhs_nim, $mhs_nama]);
            $mahasiswa_id = $pdo->lastInsertId();
        } else {
            $mahasiswa_id = $mhs_db['id'];
        }

        // Simpan deteksi
        $stmt = $pdo->prepare("INSERT INTO deteksi (mahasiswa_id, user_id, P, Q, R, S, pelanggaran_berat, peringatan_akademik, status_aman, status_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $mahasiswa_id, $_SESSION['user_id'],
            $input_P, $input_Q, $input_R, $input_S,
            (int)$result['pelanggaran_berat'],
            (int)$result['peringatan_akademik'],
            (int)$result['status_aman'],
            $result['status_label']
        ]);
        $deteksi_id = $pdo->lastInsertId();

        // Simpan reasoning path
        $stmt = $pdo->prepare("INSERT INTO deteksi_detail (deteksi_id, langkah, keterangan) VALUES (?, ?, ?)");
        foreach ($result['reasoning_path'] as $i => $step) {
            $stmt->execute([$deteksi_id, $i + 1, $step]);
        }
    } catch (Exception $e) {
        // Silent fail untuk demo
    }
}
?>
<div class="space-y-6 fade-in">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- FORM INPUT -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Input Data Mahasiswa
                </h3>
            </div>
            <div class="p-5">
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Nama Mahasiswa</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($mhs_nama ?: ($mahasiswa['nama'] ?? '')) ?>" required
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                                placeholder="Nama lengkap">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">NIM</label>
                            <input type="text" name="nim" value="<?= htmlspecialchars($mhs_nim ?: ($mahasiswa['nim'] ?? '')) ?>" required
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                                placeholder="NIM">
                        </div>
                    </div>

                    <hr class="border-slate-200">

                    <p class="text-sm font-medium text-slate-700">Gejala (0 = Tidak, 1 = Ya)</p>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">P</span>
                                <span class="text-sm text-slate-700">Kehadiran mahasiswa &lt; 75%</span>
                            </div>
                            <select name="P" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="0" <?= $input_P == 0 ? 'selected' : '' ?>>0 (Tidak)</option>
                                <option value="1" <?= $input_P == 1 ? 'selected' : '' ?>>1 (Ya)</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold text-sm">Q</span>
                                <span class="text-sm text-slate-700">Terbukti melakukan plagiarisme</span>
                            </div>
                            <select name="Q" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="0" <?= $input_Q == 0 ? 'selected' : '' ?>>0 (Tidak)</option>
                                <option value="1" <?= $input_Q == 1 ? 'selected' : '' ?>>1 (Ya)</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold text-sm">R</span>
                                <span class="text-sm text-slate-700">Terbukti menyontek saat ujian</span>
                            </div>
                            <select name="R" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="0" <?= $input_R == 0 ? 'selected' : '' ?>>0 (Tidak)</option>
                                <option value="1" <?= $input_R == 1 ? 'selected' : '' ?>>1 (Ya)</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">S</span>
                                <span class="text-sm text-slate-700">Mengumpulkan tugas tepat waktu</span>
                            </div>
                            <select name="S" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="0" <?= $input_S == 0 ? 'selected' : '' ?>>0 (Tidak)</option>
                                <option value="1" <?= $input_S == 1 ? 'selected' : '' ?>>1 (Ya)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-900 text-white rounded-lg font-medium hover:bg-blue-800 transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        DETEKSI PELANGGARAN
                    </button>
                </form>
            </div>
        </div>

        <!-- HASIL -->
        <div class="space-y-6">
            <?php if ($result): ?>
                <!-- STATUS BADGE -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5 text-center">
                        <?php
                        $badge_class = 'bg-green-100 text-green-800 border-green-200';
                        $icon = 'check';
                        if ($result['pelanggaran_berat']) {
                            $badge_class = 'bg-red-100 text-red-800 border-red-200';
                            $icon = 'x';
                        } elseif ($result['peringatan_akademik']) {
                            $badge_class = 'bg-amber-100 text-amber-800 border-amber-200';
                            $icon = 'warning';
                        }
                        ?>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border <?= $badge_class ?>">
                            <?php if ($icon === 'check'): ?>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php elseif ($icon === 'x'): ?>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php endif; ?>
                            <?= $result['status_label'] ?>
                        </span>
                        <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($mhs_nama) ?> — <?= htmlspecialchars($mhs_nim) ?></p>
                    </div>
                </div>

                <!-- VISUALISASI LOGIKA -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-semibold text-slate-800 tracking-tight">Visualisasi Logika</h3>
                    </div>
                    <div class="p-5">
                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Input Fakta</h4>
                        <div class="overflow-x-auto mb-4">
                            <table class="w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
                                <thead><tr class="bg-slate-50 text-slate-500 font-medium"><th class="px-4 py-2 border-r">Kode</th><th class="px-4 py-2 border-r">Proposisi</th><th class="px-4 py-2">Nilai</th></tr></thead>
                                <tbody class="divide-y divide-slate-100 text-center">
                                    <tr><td class="px-4 py-2 font-bold border-r">P</td><td class="px-4 py-2 border-r text-left">Kehadiran &lt; 75%</td><td class="px-4 py-2 font-bold <?= $input_P ? 'text-green-600' : 'text-red-600' ?>"><?= $input_P ? 'TRUE' : 'FALSE' ?></td></tr>
                                    <tr><td class="px-4 py-2 font-bold border-r">Q</td><td class="px-4 py-2 border-r text-left">Plagiarisme</td><td class="px-4 py-2 font-bold <?= $input_Q ? 'text-green-600' : 'text-red-600' ?>"><?= $input_Q ? 'TRUE' : 'FALSE' ?></td></tr>
                                    <tr><td class="px-4 py-2 font-bold border-r">R</td><td class="px-4 py-2 border-r text-left">Menyontek</td><td class="px-4 py-2 font-bold <?= $input_R ? 'text-green-600' : 'text-red-600' ?>"><?= $input_R ? 'TRUE' : 'FALSE' ?></td></tr>
                                    <tr><td class="px-4 py-2 font-bold border-r">S</td><td class="px-4 py-2 border-r text-left">Tugas Tepat Waktu</td><td class="px-4 py-2 font-bold <?= $input_S ? 'text-green-600' : 'text-red-600' ?>"><?= $input_S ? 'TRUE' : 'FALSE' ?></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Evaluasi Rule</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
                                <thead><tr class="bg-slate-50 text-slate-500 font-medium"><th class="px-4 py-2 border-r">Rule</th><th class="px-4 py-2 border-r">Ekspresi Logika</th><th class="px-4 py-2">Hasil</th></tr></thead>
                                <tbody class="divide-y divide-slate-100 text-center">
                                    <tr><td class="px-4 py-2 border-r font-medium">R1</td><td class="px-4 py-2 border-r font-mono">Q → Pelanggaran Berat</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-bold <?= $input_Q ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $input_Q ? 'TERPENUHI' : 'TIDAK' ?></span></td></tr>
                                    <tr><td class="px-4 py-2 border-r font-medium">R2</td><td class="px-4 py-2 border-r font-mono">R → Pelanggaran Berat</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-bold <?= $input_R ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $input_R ? 'TERPENUHI' : 'TIDAK' ?></span></td></tr>
                                    <tr><td class="px-4 py-2 border-r font-medium">R3</td><td class="px-4 py-2 border-r font-mono">(P ∧ ¬S) → Peringatan</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-bold <?= ($input_P && !$input_S) ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= ($input_P && !$input_S) ? 'TERPENUHI' : 'TIDAK' ?></span></td></tr>
                                    <tr><td class="px-4 py-2 border-r font-medium">R4</td><td class="px-4 py-2 border-r font-mono">¬semua → Status Aman</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-bold <?= $result['status_aman'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $result['status_aman'] ? 'TERPENUHI' : 'TIDAK' ?></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- REASONING PATH -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-semibold text-slate-800 tracking-tight">Reasoning Path (Forward Chaining)</h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-1 font-mono text-sm">
                            <?php foreach ($result['reasoning_path'] as $step): ?>
                                <?php if (strpos($step, '===') === 0): ?>
                                    <p class="font-bold text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg -mx-3 mt-2 mb-1"><?= htmlspecialchars($step) ?></p>
                                <?php elseif (strpos($step, 'Rule') === 0): ?>
                                    <p class="text-blue-700 font-medium"><?= htmlspecialchars($step) ?></p>
                                <?php elseif (strpos($step, 'TERPENUHI') !== false): ?>
                                    <p class="text-green-700 ml-4"><?= htmlspecialchars($step) ?></p>
                                <?php elseif (strpos($step, 'tidak') !== false): ?>
                                    <p class="text-slate-500 ml-4"><?= htmlspecialchars($step) ?></p>
                                <?php else: ?>
                                    <p class="text-slate-600"><?= htmlspecialchars($step) ?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- OUTPUT -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-semibold text-slate-800 tracking-tight">Output — Hasil Deteksi</h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <p class="text-sm"><strong>Mahasiswa:</strong> <?= htmlspecialchars($mhs_nama) ?> (<?= htmlspecialchars($mhs_nim) ?>)</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border border-slate-200 rounded-lg overflow-hidden">
                                    <thead><tr class="bg-slate-50 text-slate-500 font-medium"><th class="px-4 py-2 border-r">Input</th><th class="px-4 py-2">P</th><th class="px-4 py-2">Q</th><th class="px-4 py-2">R</th><th class="px-4 py-2">S</th></tr></thead>
                                    <tbody class="divide-y divide-slate-100 text-center">
                                        <tr><td class="px-4 py-2 border-r font-medium">Nilai</td><td class="px-4 py-2"><?= $input_P ? 'Ya' : 'Tidak' ?></td><td class="px-4 py-2"><?= $input_Q ? 'Ya' : 'Tidak' ?></td><td class="px-4 py-2"><?= $input_R ? 'Ya' : 'Tidak' ?></td><td class="px-4 py-2"><?= $input_S ? 'Ya' : 'Tidak' ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 space-y-2">
                                <p class="text-sm font-medium">Rule Terpenuhi:</p>
                                <?php if (empty($result['rule_terpenuhi'])): ?>
                                    <p class="text-sm text-slate-500 italic">(tidak ada rule terpenuhi)</p>
                                <?php else: ?>
                                    <?php foreach ($result['rule_terpenuhi'] as $r): ?>
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span><?= htmlspecialchars($r) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3 p-4 rounded-xl font-bold text-center text-lg
                                <?= $result['pelanggaran_berat'] ? 'bg-red-50 text-red-700 border border-red-200' : ($result['peringatan_akademik'] ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-green-50 text-green-700 border border-green-200') ?>">
                                <?= $result['status_label'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-10 text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg font-medium text-slate-500 mb-1">Belum ada deteksi</p>
                    <p class="text-sm">Silakan isi form di samping dan tekan tombol deteksi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
