<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        redirect('dashboard.php');
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Deteksi Pelanggaran Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .bg-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #0ea5e9 100%); }
        .card-shadow { box-shadow: 0 20px 60px rgba(0,0,0,0.08); }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient skew-y-3 origin-top-left -translate-y-32"></div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 relative z-10 p-8 animate-[fadeIn_0.3s_ease-in-out]">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-blue-50 text-blue-900 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Deteksi Pelanggaran Akademik</h1>
            <p class="text-sm text-slate-500 mt-2">Sistem berbasis Logika Proposisional & Forward Chaining</p>
        </div>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Username</label>
                <input type="text" name="username" required
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all"
                    placeholder="Masukkan username">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all"
                    placeholder="••••••••">
            </div>

            <?php if ($error): ?>
                <div class="p-3 rounded-xl bg-red-50 text-red-600 text-sm flex items-center gap-2 border border-red-100">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="w-full py-2.5 bg-blue-900 text-white rounded-xl font-medium hover:bg-blue-800 transition-all shadow-sm hover:shadow-md">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400 bg-slate-50 p-3 rounded-xl border border-slate-100">
            <p class="font-semibold mb-1 text-slate-500">Akun Demo:</p>
            <p>Admin: <span class="font-mono text-blue-600">admin</span> / <span class="font-mono text-blue-600">admin123</span></p>
            <p>Mahasiswa: <span class="font-mono text-blue-600">mahasiswa1</span> / <span class="font-mono text-blue-600">admin123</span></p>
        </div>
    </div>
</body>
</html>
