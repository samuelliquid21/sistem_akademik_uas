<?php
require_once 'config/database.php';
cek_login();

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Deteksi Pelanggaran Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .sidebar-active { background: #1e3a5f; color: white; box-shadow: 0 4px 12px rgba(30,58,95,0.3); }
        .card-hover { transition: all 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease-out; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex">
    <!-- SIDEBAR -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col">
        <div class="h-16 flex items-center px-5 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-900 flex items-center justify-center text-white mr-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="font-bold text-slate-900 tracking-tight text-sm">Pelanggaran Akademik</span>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="?page=dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'dashboard' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <a href="?page=rules" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'rules' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Manajemen Rule
            </a>

            <a href="?page=truth" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'truth' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Tabel Kebenaran
            </a>

            <a href="?page=input" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'input' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Input & Deteksi
            </a>

            <a href="?page=test" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'test' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Uji Coba (24 Kasus)
            </a>

            <a href="?page=riwayat" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'riwayat' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Deteksi
            </a>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 mt-6">Admin</p>
                <a href="?page=admin_mahasiswa" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all mb-1 <?= $page === 'admin_mahasiswa' ? 'sidebar-active' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Kelola Mahasiswa
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-9 h-9 rounded-full bg-blue-900 flex items-center justify-center text-white text-sm font-bold">
                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($_SESSION['nama']) ?></p>
                    <p class="text-xs text-slate-500 capitalize"><?= $_SESSION['role'] ?></p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 ml-64 flex flex-col min-h-screen">
        <header class="h-16 bg-white/80 backdrop-blur border-b border-slate-200 sticky top-0 z-30 px-8 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800 tracking-tight">
                <?php
                $titles = [
                    'dashboard' => 'Dashboard',
                    'rules' => 'Manajemen Rule (Knowledge Base)',
                    'truth' => 'Tabel Kebenaran',
                    'input' => 'Input Data & Deteksi Pelanggaran',
                    'test' => 'Uji Coba (24 Skenario Pengujian)',
                    'riwayat' => 'Riwayat Deteksi',
                    'admin_mahasiswa' => 'Kelola Mahasiswa',
                ];
                echo $titles[$page] ?? 'Dashboard';
                ?>
            </h2>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <span class="hidden sm:block"><?= date('d M Y') ?></span>
            </div>
        </header>

        <div class="p-6 sm:p-8 flex-1 overflow-auto">
            <?php
            // Route pages
            switch ($page) {
                case 'dashboard':
                    include 'pages/dashboard_home.php';
                    break;
                case 'rules':
                    include 'pages/rules.php';
                    break;
                case 'truth':
                    include 'pages/truth_table.php';
                    break;
                case 'input':
                    include 'pages/input.php';
                    break;
                case 'test':
                    include 'pages/test_cases.php';
                    break;
                case 'riwayat':
                    include 'pages/riwayat.php';
                    break;
                case 'admin_mahasiswa':
                    if ($_SESSION['role'] === 'admin') {
                        include 'pages/admin_mahasiswa.php';
                    } else {
                        echo '<div class="text-center py-20 text-slate-400">Akses ditolak</div>';
                    }
                    break;
                default:
                    include 'pages/dashboard_home.php';
            }
            ?>
        </div>
    </main>
</body>
</html>
