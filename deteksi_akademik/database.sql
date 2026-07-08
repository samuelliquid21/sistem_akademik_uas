-- ============================================================
-- SISTEM DETEKSI PELANGGARAN AKADEMIK
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_pelanggaran_akademik;
USE db_pelanggaran_akademik;

-- 1. TABEL USERS
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABEL MAHASISWA
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. TABEL GEJALA (Proposisi Logika)
CREATE TABLE IF NOT EXISTS gejala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(5) NOT NULL UNIQUE,
    proposisi VARCHAR(255) NOT NULL,
    deskripsi VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 4. TABEL RULES (Knowledge Base)
CREATE TABLE IF NOT EXISTS rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_rule VARCHAR(50) NOT NULL,
    premis TEXT NOT NULL,
    kesimpulan VARCHAR(100) NOT NULL,
    ekspresi VARCHAR(255) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB;

-- 5. TABEL DETEKSI (Hasil Deteksi)
CREATE TABLE IF NOT EXISTS deteksi (
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
) ENGINE=InnoDB;

-- 6. TABEL DETEKSI_DETAIL (Reasoning Path)
CREATE TABLE IF NOT EXISTS deteksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deteksi_id INT NOT NULL,
    langkah INT NOT NULL,
    keterangan TEXT NOT NULL,
    FOREIGN KEY (deteksi_id) REFERENCES deteksi(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- INSERT DATA AWAL (Seeders)
-- ============================================================

-- Gejala
INSERT INTO gejala (kode, proposisi, deskripsi) VALUES
('P', 'Kehadiran < 75%', 'Kehadiran mahasiswa kurang dari 75 persen'),
('Q', 'Terbukti Plagiarisme', 'Mahasiswa terbukti melakukan plagiarisme'),
('R', 'Menyontek saat Ujian', 'Mahasiswa terbukti menyontek saat ujian'),
('S', 'Tugas Tepat Waktu', 'Mahasiswa mengumpulkan tugas tepat waktu');

-- Rules
INSERT INTO rules (nama_rule, premis, kesimpulan, ekspresi, deskripsi) VALUES
('Rule 1', 'Q', 'Pelanggaran Berat', 'Q → Pelanggaran Berat', 'Jika terbukti plagiarisme maka pelanggaran berat.'),
('Rule 2', 'R', 'Pelanggaran Berat', 'R → Pelanggaran Berat', 'Jika terbukti menyontek maka pelanggaran berat.'),
('Rule 3', 'P, ¬S', 'Peringatan Akademik', '(P ∧ ¬S) → Peringatan Akademik', 'Jika kehadiran kurang dan tugas tidak tepat waktu maka peringatan akademik.'),
('Rule 4', '¬Q, ¬R, ¬(P ∧ ¬S)', 'Status Aman', '(¬Q ∧ ¬R ∧ ¬(P ∧ ¬S)) → Status Aman', 'Jika tidak ada pelanggaran maka status aman.');

-- Users (password: admin123)
INSERT INTO users (username, password, nama, role) VALUES
('admin', '$2y$12$ytym5HGiNUbgVr3tzwK/TuuiV40fUkgIGHhq6Gc2gG..gpEgD0swe', 'Admin Akademik', 'admin'),
('mahasiswa1', '$2y$12$ytym5HGiNUbgVr3tzwK/TuuiV40fUkgIGHhq6Gc2gG..gpEgD0swe', 'Budi Santoso', 'mahasiswa'),
('mahasiswa2', '$2y$12$ytym5HGiNUbgVr3tzwK/TuuiV40fUkgIGHhq6Gc2gG..gpEgD0swe', 'Siti Rahmawati', 'mahasiswa');

-- Mahasiswa
INSERT INTO mahasiswa (user_id, nim, nama) VALUES
(2, '22012345', 'Budi Santoso'),
(3, '22012346', 'Siti Rahmawati');
