<div class="space-y-6 fade-in">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 tracking-tight">Tabel Kebenaran — Setiap Rule</h3>
        </div>
        <div class="p-5 space-y-8">
            <!-- Rule 1 -->
            <div>
                <h4 class="font-semibold text-slate-900 mb-3">Rule 1: Q → Pelanggaran Berat</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-medium">
                                <th class="px-4 py-3 border-r border-slate-200">Q</th>
                                <th class="px-4 py-3">Pelanggaran Berat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td><td class="px-4 py-3 font-bold text-green-600">TRUE</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td><td class="px-4 py-3 font-bold text-red-600">FALSE</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rule 2 -->
            <div>
                <h4 class="font-semibold text-slate-900 mb-3">Rule 2: R → Pelanggaran Berat</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-medium">
                                <th class="px-4 py-3 border-r border-slate-200">R</th>
                                <th class="px-4 py-3">Pelanggaran Berat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td><td class="px-4 py-3 font-bold text-green-600">TRUE</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td><td class="px-4 py-3 font-bold text-red-600">FALSE</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rule 3 -->
            <div>
                <h4 class="font-semibold text-slate-900 mb-3">Rule 3: (P ∧ ¬S) → Peringatan Akademik</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-medium">
                                <th class="px-4 py-3 border-r border-slate-200">P</th>
                                <th class="px-4 py-3 border-r border-slate-200">S</th>
                                <th class="px-4 py-3 border-r border-slate-200">¬S</th>
                                <th class="px-4 py-3 border-r border-slate-200">P ∧ ¬S</th>
                                <th class="px-4 py-3">Peringatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3">FALSE</td>
                            </tr>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 font-bold text-green-600">TRUE</td>
                            </tr>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3">FALSE</td>
                            </tr>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 font-bold text-red-600 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3 font-bold text-green-600 border-r border-slate-200">TRUE</td>
                                <td class="px-4 py-3 border-r border-slate-200">FALSE</td>
                                <td class="px-4 py-3">FALSE</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rule 4 -->
            <div>
                <h4 class="font-semibold text-slate-900 mb-3">Rule 4: (¬Q ∧ ¬R ∧ ¬(P ∧ ¬S)) → Status Aman</h4>
                <p class="text-sm text-slate-500 mb-2">Status Aman = TRUE jika tidak ada Rule 1, 2, atau 3 yang terpenuhi.</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-medium">
                                <th class="px-4 py-3 border-r border-slate-200">Q</th>
                                <th class="px-4 py-3 border-r border-slate-200">R</th>
                                <th class="px-4 py-3 border-r border-slate-200">P∧¬S</th>
                                <th class="px-4 py-3">Status Aman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 font-bold text-green-600">T</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">F</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                            <tr class="hover:bg-slate-50/80"><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 border-r border-slate-200">T</td><td class="px-4 py-3 font-bold text-red-600">F</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
