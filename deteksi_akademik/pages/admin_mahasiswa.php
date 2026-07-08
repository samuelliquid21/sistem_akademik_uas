<?php
// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus'])) {
        $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id = ?");
        $stmt->execute([$_POST['hapus']]);
        $msg = 'Mahasiswa berhasil dihapus!';
    } elseif (isset($_POST['simpan'])) {
        $id = $_POST['id'] ?? 0;
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        if ($id) {
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nim = ?, nama = ? WHERE id = ?");
            $stmt->execute([$nim, $nama, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $nim, $nama]);
        }
        $msg = 'Data mahasiswa berhasil disimpan!';
    }
}

$stmt = $pdo->query("SELECT m.*, (SELECT COUNT(*) FROM deteksi d WHERE d.mahasiswa_id = m.id) as total_deteksi FROM mahasiswa m ORDER BY m.nama ASC");
$mahasiswa_list = $stmt->fetchAll();
?>
<div class="space-y-6 fade-in">
    <?php if (isset($msg)): ?>
        <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-semibold text-slate-800 tracking-tight">Kelola Data Mahasiswa</h3>
            <button onclick="openModal()" class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Mahasiswa
            </button>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-medium">
                            <th class="px-4 py-3 text-left">NIM</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-center">Total Deteksi</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($mahasiswa_list as $m): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-slate-500"><?= htmlspecialchars($m['nim']) ?></td>
                            <td class="px-4 py-3 font-medium text-slate-900"><?= htmlspecialchars($m['nama']) ?></td>
                            <td class="px-4 py-3 text-center text-slate-500"><?= $m['total_deteksi'] ?></td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openModal(<?= $m['id'] ?>, '<?= htmlspecialchars($m['nim']) ?>', '<?= htmlspecialchars($m['nama']) ?>')" class="text-blue-600 hover:text-blue-800 text-xs font-medium mx-1">Edit</button>
                                <form method="POST" class="inline" onsubmit="return confirm('Hapus mahasiswa ini?')">
                                    <button name="hapus" value="<?= $m['id'] ?>" class="text-red-600 hover:text-red-800 text-xs font-medium mx-1">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mahasiswa_list)): ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data mahasiswa</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="mahasiswaModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="font-semibold text-lg text-slate-900" id="modalTitle">Tambah Mahasiswa</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="editId" value="0">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">NIM</label>
                <input type="text" name="nim" id="editNim" required
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Masukkan NIM">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Nama Mahasiswa</label>
                <input type="text" name="nama" id="editNama" required
                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Masukkan nama lengkap">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" name="simpan" class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id = 0, nim = '', nama = '') {
    document.getElementById('mahasiswaModal').classList.remove('hidden');
    document.getElementById('mahasiswaModal').classList.add('flex');
    document.getElementById('editId').value = id;
    document.getElementById('editNim').value = nim;
    document.getElementById('editNama').value = nama;
    document.getElementById('modalTitle').textContent = id ? 'Edit Mahasiswa' : 'Tambah Mahasiswa';
}

function closeModal() {
    document.getElementById('mahasiswaModal').classList.add('hidden');
    document.getElementById('mahasiswaModal').classList.remove('flex');
}

document.getElementById('mahasiswaModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
