<div class="space-y-6 fade-in">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 tracking-tight">Variabel Proposisi</h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-medium">
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Proposisi</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-blue-600">P</td>
                            <td class="px-4 py-3">Kehadiran mahasiswa &lt; 75%</td>
                            <td class="px-4 py-3 text-slate-500">Kehadiran mahasiswa kurang dari 75 persen</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-red-600">Q</td>
                            <td class="px-4 py-3">Terbukti melakukan plagiarisme</td>
                            <td class="px-4 py-3 text-slate-500">Mahasiswa terbukti melakukan plagiarisme</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-red-600">R</td>
                            <td class="px-4 py-3">Terbukti menyontek saat ujian</td>
                            <td class="px-4 py-3 text-slate-500">Mahasiswa terbukti menyontek saat ujian</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-green-600">S</td>
                            <td class="px-4 py-3">Mengumpulkan tugas tepat waktu</td>
                            <td class="px-4 py-3 text-slate-500">Mahasiswa mengumpulkan tugas tepat waktu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 tracking-tight">Knowledge Base — Aturan Logika</h3>
        </div>
        <div class="p-5">
            <div class="space-y-4">
                <div class="border border-slate-200 rounded-xl p-4 hover:shadow-sm transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">R1</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-semibold text-slate-900">Rule 1</h4>
                                <span class="px-2 py-0.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 rounded-full">Q → Pelanggaran Berat</span>
                            </div>
                            <p class="text-sm text-slate-500">Jika terbukti melakukan plagiarisme maka pelanggaran berat.</p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-4 hover:shadow-sm transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">R2</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-semibold text-slate-900">Rule 2</h4>
                                <span class="px-2 py-0.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 rounded-full">R → Pelanggaran Berat</span>
                            </div>
                            <p class="text-sm text-slate-500">Jika terbukti menyontek saat ujian maka pelanggaran berat.</p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-4 hover:shadow-sm transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm flex-shrink-0">R3</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-semibold text-slate-900">Rule 3</h4>
                                <span class="px-2 py-0.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 rounded-full">(P ∧ ¬S) → Peringatan</span>
                            </div>
                            <p class="text-sm text-slate-500">Jika kehadiran kurang dan tugas tidak tepat waktu maka peringatan akademik.</p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-4 hover:shadow-sm transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center font-bold text-sm flex-shrink-0">R4</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-semibold text-slate-900">Rule 4</h4>
                                <span class="px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700 border border-green-100 rounded-full">¬Q∧¬R∧¬(P∧¬S) → Aman</span>
                            </div>
                            <p class="text-sm text-slate-500">Jika tidak ada pelanggaran sama sekali maka status aman.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
