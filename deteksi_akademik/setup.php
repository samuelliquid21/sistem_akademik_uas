<?php
// ============================================================
// SETUP DATABASE - SISTEM DETEKSI PELANGGARAN AKADEMIK
// JALANKAN SEKALI SAJA: http://localhost/.../setup.php
// ============================================================

$host = 'localhost';
$user = 'root';
$pass = 'sardenggan123';

try {
    // Buat database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS db_pelanggaran_akademik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'db_pelanggaran_akademik' berhasil dibuat.<br>";

    $pdo->exec("USE db_pelanggaran_akademik");

    // Buat tabel users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'users' berhasil dibuat.<br>";

    // Buat tabel mahasiswa
    $pdo->exec("CREATE TABLE IF NOT EXISTS mahasiswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nim VARCHAR(20) NOT NULL UNIQUE,
        nama VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'mahasiswa' berhasil dibuat.<br>";

    // Buat tabel gejala
    $pdo->exec("CREATE TABLE IF NOT EXISTS gejala (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kode VARCHAR(5) NOT NULL UNIQUE,
        proposisi VARCHAR(255) NOT NULL,
        deskripsi VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'gejala' berhasil dibuat.<br>";

    // Buat tabel rules
    $pdo->exec("CREATE TABLE IF NOT EXISTS rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_rule VARCHAR(50) NOT NULL,
        premis TEXT NOT NULL,
        kesimpulan VARCHAR(100) NOT NULL,
        ekspresi VARCHAR(255) NOT NULL,
        deskripsi TEXT
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'rules' berhasil dibuat.<br>";

    // Buat tabel deteksi
    $pdo->exec("CREATE TABLE IF NOT EXISTS deteksi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mahasiswa_id INT NOT NULL,
        user_id INT NOT NULL,
        P TINYINT(1) DEFAULT 0,
        Q TINYINT(1) DEFAULT 0,
        R TINYINT(1) DEFAULT 0,
        S TINYINT(1) DEFAULT 0,
        pelanggaran_berat TINYINT(1) DEFAULT 0,
        peringatan_akademik TINYINT(1) DEFAULT 0,
        status_aman TINYINT(1) DEFAULT 0,
        status_label VARCHAR(50) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'deteksi' berhasil dibuat.<br>";

    // Buat tabel deteksi_detail
    $pdo->exec("CREATE TABLE IF NOT EXISTS deteksi_detail (
        id INT AUTO_INCREMENT PRIMARY KEY,
        deteksi_id INT NOT NULL,
        langkah INT NOT NULL,
        keterangan TEXT NOT NULL,
        FOREIGN KEY (deteksi_id) REFERENCES deteksi(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "✅ Tabel 'deteksi_detail' berhasil dibuat.<br>";

    // Seed gejala
    $pdo->exec("DELETE FROM gejala");
    $pdo->exec("INSERT INTO gejala (kode, proposisi, deskripsi) VALUES
        ('P', 'Kehadiran < 75%', 'Kehadiran mahasiswa kurang dari 75 persen'),
        ('Q', 'Terbukti Plagiarisme', 'Mahasiswa terbukti melakukan plagiarisme'),
        ('R', 'Menyontek saat Ujian', 'Mahasiswa terbukti menyontek saat ujian'),
        ('S', 'Tugas Tepat Waktu', 'Mahasiswa mengumpulkan tugas tepat waktu')");
    echo "✅ Data 'gejala' berhasil diisi.<br>";

    // Seed rules
    $pdo->exec("DELETE FROM rules");
    $pdo->exec("INSERT INTO rules (nama_rule, premis, kesimpulan, ekspresi, deskripsi) VALUES
        ('Rule 1', 'Q', 'Pelanggaran Berat', 'Q → Pelanggaran Berat', 'Jika terbukti plagiarisme maka pelanggaran berat.'),
        ('Rule 2', 'R', 'Pelanggaran Berat', 'R → Pelanggaran Berat', 'Jika terbukti menyontek maka pelanggaran berat.'),
        ('Rule 3', 'P, ¬S', 'Peringatan Akademik', '(P ∧ ¬S) → Peringatan Akademik', 'Jika kehadiran kurang dan tugas tidak tepat waktu maka peringatan akademik.'),
        ('Rule 4', '¬Q, ¬R, ¬(P ∧ ¬S)', 'Status Aman', '(¬Q ∧ ¬R ∧ ¬(P ∧ ¬S)) → Status Aman', 'Jika tidak ada pelanggaran maka status aman.')");
    echo "✅ Data 'rules' berhasil diisi.<br>";

    // Seed users (password: admin123)
    $pdo->exec("DELETE FROM users");
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)")->execute(['admin', $adminPass, 'Admin Akademik', 'admin']);
    $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)")->execute(['mahasiswa1', $adminPass, 'Budi Santoso', 'mahasiswa']);
    $pdo->prepare("INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)")->execute(['mahasiswa2', $adminPass, 'Siti Rahmawati', 'mahasiswa']);
    echo "✅ Data 'users' berhasil diisi (password: admin123).<br>";

    // Seed mahasiswa
    $pdo->exec("DELETE FROM mahasiswa");
    $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)")->execute([2, '22012345', 'Budi Santoso']);
    $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (?, ?, ?)")->execute([3, '22012346', 'Siti Rahmawati']);
    echo "✅ Data 'mahasiswa' berhasil diisi.<br>";

    echo "<br><strong style='color:green;'>✅ SETUP SELESAI! Semua tabel dan data berhasil dibuat.</strong>";
    echo "<br><br>";
    echo "<a href='index.php' style='display:inline-block;padding:10px 20px;background:#1e3a5f;color:white;text-decoration:none;border-radius:8px;'>→ Login ke Aplikasi</a>";

} catch (PDOException $e) {
    echo "<strong style='color:red;'>❌ ERROR:</strong> " . $e->getMessage();
}
