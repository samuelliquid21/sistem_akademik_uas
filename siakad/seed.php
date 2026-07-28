<?php
// Run this ONCE on Railway Console after deploy:
//   php siakad/seed.php
// This will create tables and seed data.

$host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: getenv('MARIADB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: getenv('MARIADB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: getenv('MYSQL_PASSWORD') ?: getenv('MARIADB_PASSWORD') ?: 'sardenggan123';
$db   = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: getenv('MARIADB_DATABASE') ?: 'db_siakad';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");

    // Prevent re-execution on every deploy
    $check = $pdo->query("SELECT COUNT(*) as c FROM users");
    if ($check->fetch()['c'] > 0) {
        echo "Database already seeded. Skipping.\n";
        exit(0);
    }

    // Tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nama VARCHAR(100) NOT NULL,
            role ENUM('admin','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS dosen (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            nip VARCHAR(20) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            prodi VARCHAR(100),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kelas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_kelas VARCHAR(10) NOT NULL UNIQUE,
            dosen_id INT NOT NULL,
            FOREIGN KEY (dosen_id) REFERENCES dosen(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mahasiswa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            nim VARCHAR(20) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            kelas_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gejala (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kode VARCHAR(10) NOT NULL UNIQUE,
            proposisi VARCHAR(255) NOT NULL,
            deskripsi TEXT
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_rule VARCHAR(100) NOT NULL,
            ekspresi VARCHAR(255) NOT NULL,
            deskripsi TEXT
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS deteksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mahasiswa_id INT NOT NULL,
            user_id INT NOT NULL,
            dosen_id INT NOT NULL,
            P TINYINT(1) DEFAULT 0,
            Q TINYINT(1) DEFAULT 0,
            R TINYINT(1) DEFAULT 0,
            S TINYINT(1) DEFAULT 0,
            pelanggaran_berat TINYINT(1) DEFAULT 0,
            peringatan_akademik TINYINT(1) DEFAULT 0,
            status_aman TINYINT(1) DEFAULT 1,
            status_label VARCHAR(100) DEFAULT 'Status Aman',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS deteksi_detail (
            id INT AUTO_INCREMENT PRIMARY KEY,
            deteksi_id INT NOT NULL,
            langkah INT NOT NULL,
            keterangan TEXT NOT NULL,
            FOREIGN KEY (deteksi_id) REFERENCES deteksi(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "✅ Tables created\n";

    // Seed users
    $pw = password_hash('admin', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (username, password, nama, role) VALUES ('admin','$pw','Admin SIAKAD','admin')");
    $pw = password_hash('kelas_a', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (username, password, nama, role) VALUES ('dosen_a','$pw','Dr. Ahmad Fauzi','dosen')");
    $pw = password_hash('kelas_b', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (username, password, nama, role) VALUES ('dosen_b','$pw','Dr. Budi Santoso','dosen')");
    $pw = password_hash('kelas_c', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (username, password, nama, role) VALUES ('dosen_c','$pw','Dr. Citra Dewi','dosen')");

    // Seed dosen
    $pdo->exec("INSERT IGNORE INTO dosen (user_id, nip, nama, prodi) VALUES (2,'198001012010011001','Dr. Ahmad Fauzi','Teknik Informatika')");
    $pdo->exec("INSERT IGNORE INTO dosen (user_id, nip, nama, prodi) VALUES (3,'198505152011012002','Dr. Budi Santoso','Sistem Informasi')");
    $pdo->exec("INSERT IGNORE INTO dosen (user_id, nip, nama, prodi) VALUES (4,'199003032012011003','Dr. Citra Dewi','Teknik Komputer')");

    // Seed kelas
    $pdo->exec("INSERT IGNORE INTO kelas (nama_kelas, dosen_id) VALUES ('A',1)");
    $pdo->exec("INSERT IGNORE INTO kelas (nama_kelas, dosen_id) VALUES ('B',2)");
    $pdo->exec("INSERT IGNORE INTO kelas (nama_kelas, dosen_id) VALUES ('C',3)");

    // Seed mahasiswa (30)
    $nama = ['Adi Pratama','Bunga Lestari','Citra Ayu','Dimas Saputra','Eka Putri','Farhan Ramadhan','Gita Permata','Hadi Suprapto','Intan Permatasari','Joko Widodo',
             'Kartika Sari','Lukman Hakim','Maya Anggraini','Novi Andriani','Oscar Wirawan','Putri Maharani','Qori Aisyah','Rudi Hartono','Siska Dewi','Toni Gunawan',
             'Umar Khayam','Vina Agustin','Wawan Setiawan','Xena Aprilia','Yoga Pratama','Zara Amalia','Arief Rahman','Dewi Sartika','Eko Prasetyo','Fitri Handayani'];
    for ($i = 0; $i < 30; $i++) {
        $nim = 2510511000 + $i;
        $uname = 'mhs_' . chr(ord('a') + intdiv($i, 10)) . (($i % 10) + 1);
        $pw = password_hash(substr($nim, -5), PASSWORD_DEFAULT);
        $kls = intdiv($i, 10) + 1;
        $uid = $i + 5;
        $pdo->exec("INSERT IGNORE INTO users (id, username, password, nama, role) VALUES ($uid,'$uname','$pw','{$nama[$i]}','mahasiswa')");
        $pdo->exec("INSERT IGNORE INTO mahasiswa (user_id, nim, nama, kelas_id) VALUES ($uid,$nim,'{$nama[$i]}',$kls)");
    }

    echo "✅ Users, Dosen, Kelas, Mahasiswa seeded (30 mahasiswa)\n";

    // Seed gejala
    $pdo->exec("INSERT IGNORE INTO gejala (kode, proposisi, deskripsi) VALUES ('P','Kehadiran < 75%','Kehadiran mahasiswa kurang dari 75% total pertemuan')");
    $pdo->exec("INSERT IGNORE INTO gejala (kode, proposisi, deskripsi) VALUES ('Q','Plagiarisme','Mahasiswa terbukti melakukan plagiarisme dalam tugas akhir')");
    $pdo->exec("INSERT IGNORE INTO gejala (kode, proposisi, deskripsi) VALUES ('R','Menyontek','Mahasiswa terbukti menyontek saat ujian')");
    $pdo->exec("INSERT IGNORE INTO gejala (kode, proposisi, deskripsi) VALUES ('S','Tugas Tepat Waktu','Mahasiswa mengumpulkan tugas tepat waktu')");

    // Seed rules
    $pdo->exec("INSERT IGNORE INTO rules (nama_rule, ekspresi, deskripsi) VALUES ('Rule 1: Plagiarisme','Q → Pelanggaran Berat','Jika mahasiswa melakukan plagiarisme, maka termasuk pelanggaran berat')");
    $pdo->exec("INSERT IGNORE INTO rules (nama_rule, ekspresi, deskripsi) VALUES ('Rule 2: Menyontek','R → Pelanggaran Berat','Jika mahasiswa menyontek saat ujian, maka termasuk pelanggaran berat')");
    $pdo->exec("INSERT IGNORE INTO rules (nama_rule, ekspresi, deskripsi) VALUES ('Rule 3: Kehadiran Kurang & Tugas Telat','(P ∧ ¬S) → Peringatan Akademik','Jika kehadiran < 75% dan tugas tidak tepat waktu, maka peringatan akademik')");
    $pdo->exec("INSERT IGNORE INTO rules (nama_rule, ekspresi, deskripsi) VALUES ('Rule 4: Status Aman','(¬Q ∧ ¬R ∧ ¬(P∧¬S)) → Status Aman','Tidak ada pelanggaran -> status aman')");

    echo "✅ Gejala and Rules seeded\n";
    echo "\n🎉 Database siap! Buka app.html untuk login.\n";
    echo "   Admin: admin / admin\n";
    echo "   Dosen: dosen_a / kelas_a\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}