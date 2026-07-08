<?php
require_once 'engine/inference.php';
$allTests = InferenceEngine::allTestCases();
?>
<div class="space-y-6 fade-in">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 tracking-tight">24 Skenario Pengujian</h3>
            <p class="text-xs text-slate-500 mt-1">Dikelompokkan per Reasoning Path (K1 via Q / R / Q+R)</p>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-medium">
                            <th class="px-4 py-3 text-left">Skenario</th>
                            <th class="px-4 py-3 text-center">P</th>
                            <th class="px-4 py-3 text-center">Q</th>
                            <th class="px-4 py-3 text-center">R</th>
                            <th class="px-4 py-3 text-center">S</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($allTests as $t): 
                            $row_class = '';
                            $status_badge = '';
                            if ($t['pelanggaran_berat'] && $t['peringatan_akademik']) {
                                $status_badge = 'bg-red-100 text-red-700 border-red-200';
                            } elseif ($t['pelanggaran_berat']) {
                                $status_badge = 'bg-red-50 text-red-600 border-red-100';
                            } elseif ($t['peringatan_akademik']) {
                                $status_badge = 'bg-amber-50 text-amber-600 border-amber-100';
                            } else {
                                $status_badge = 'bg-green-50 text-green-600 border-green-100';
                            }
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap"><?= htmlspecialchars($t['label']) ?></td>
                            <td class="px-4 py-3 text-center font-bold <?= $t['P'] ? 'text-green-600' : 'text-red-600' ?>"><?= $t['P'] ? 'T' : 'F' ?></td>
                            <td class="px-4 py-3 text-center font-bold <?= $t['Q'] ? 'text-green-600' : 'text-red-600' ?>"><?= $t['Q'] ? 'T' : 'F' ?></td>
                            <td class="px-4 py-3 text-center font-bold <?= $t['R'] ? 'text-green-600' : 'text-red-600' ?>"><?= $t['R'] ? 'T' : 'F' ?></td>
                            <td class="px-4 py-3 text-center font-bold <?= $t['S'] ? 'text-green-600' : 'text-red-600' ?>"><?= $t['S'] ? 'T' : 'F' ?></td>
                            <td class="px-4 py-3"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $status_badge ?>"><?= htmlspecialchars($t['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-4 bg-slate-50 rounded-xl">
                <h4 class="text-sm font-semibold text-slate-700 mb-2">Keterangan Kelompok:</h4>
                <ul class="text-xs text-slate-500 space-y-1 list-disc list-inside">
                    <li><strong>Status Aman (3 skenario):</strong> Tidak ada rule 1,2,3 terpenuhi</li>
                    <li><strong>Peringatan Akademik (1 skenario):</strong> Hanya Rule 3 terpenuhi (P ∧ ¬S)</li>
                    <li><strong>Pelanggaran Berat via Q (4 skenario):</strong> Rule 1 terpenuhi (Q)</li>
                    <li><strong>Pelanggaran Berat via R (4 skenario):</strong> Rule 2 terpenuhi (R)</li>
                    <li><strong>Pelanggaran Berat via Q+R (4 skenario):</strong> Rule 1 dan 2 terpenuhi</li>
                    <li><strong>Edge Cases (8 skenario):</strong> Kasus batas untuk verifikasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
