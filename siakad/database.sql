-- ============================================================
-- SIAKAD - SISTEM DETEKSI PELANGGARAN AKADEMIK
-- Multi-Kelas (A/B/C), Multi-Role (Admin/Dosen/Mahasiswa)
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_siakad;
USE db_siakad;

-- 1. USERS
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. DOSEN
CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    nip VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    prodi VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. KELAS
CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(10) NOT NULL UNIQUE,
    dosen_id INT NOT NULL,
    FOREIGN KEY (dosen_id) REFERENCES dosen(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. MAHASISWA
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

-- 5. GEJALA
CREATE TABLE IF NOT EXISTS gejala (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(5) NOT NULL UNIQUE,
    proposisi VARCHAR(255) NOT NULL,
    deskripsi VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 6. RULES
CREATE TABLE IF NOT EXISTS rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_rule VARCHAR(50) NOT NULL,
    premis TEXT NOT NULL,
    kesimpulan VARCHAR(100) NOT NULL,
    ekspresi VARCHAR(255) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB;

-- 7. DETEKSI
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
    status_aman TINYINT(1) DEFAULT 0,
    status_label VARCHAR(50) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dosen_id) REFERENCES dosen(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. DETEKSI_DETAIL
CREATE TABLE IF NOT EXISTS deteksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deteksi_id INT NOT NULL,
    langkah INT NOT NULL,
    keterangan TEXT NOT NULL,
    FOREIGN KEY (deteksi_id) REFERENCES deteksi(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SEEDER DATA
-- ============================================================

-- GEJALA
INSERT INTO gejala (kode, proposisi, deskripsi) VALUES
('P', 'Kehadiran < 75%', 'Kehadiran mahasiswa kurang dari 75 persen'),
('Q', 'Terbukti Plagiarisme', 'Mahasiswa terbukti melakukan plagiarisme'),
('R', 'Menyontek saat Ujian', 'Mahasiswa terbukti menyontek saat ujian'),
('S', 'Tugas Tepat Waktu', 'Mahasiswa mengumpulkan tugas tepat waktu');

-- RULES
INSERT INTO rules (nama_rule, premis, kesimpulan, ekspresi, deskripsi) VALUES
('Rule 1', 'Q', 'Pelanggaran Berat', 'Q → Pelanggaran Berat', 'Jika terbukti plagiarisme maka pelanggaran berat.'),
('Rule 2', 'R', 'Pelanggaran Berat', 'R → Pelanggaran Berat', 'Jika terbukti menyontek maka pelanggaran berat.'),
('Rule 3', 'P, ¬S', 'Peringatan Akademik', '(P ∧ ¬S) → Peringatan Akademik', 'Jika kehadiran kurang dan tugas tidak tepat waktu maka peringatan akademik.'),
('Rule 4', '¬Q, ¬R, ¬(P ∧ ¬S)', 'Status Aman', '(¬Q ∧ ¬R ∧ ¬(P ∧ ¬S)) → Status Aman', 'Jika tidak ada pelanggaran maka status aman.');

-- USERS (password: admin=admin, dosen_a=kelas_a, dosen_b=kelas_b, dosen_c=kelas_c, mahasiswa=5 digit akhir NIM)
INSERT INTO users (username, password, nama, role) VALUES
('admin', '$2y$12$MfaPKxaQEOXCSS3xq65XGu3RKichKVRzeJ4T281o1IslmhY5Sc7Eq', 'Admin SIAKAD', 'admin'),
('dosen_a', '$2y$12$SpVBCaIUZ0UAKOg.khsnCuqk/vVUhjNWZpcCE1WfO4F/zxUntIGOu', 'Dr. Ahmad Fauzi', 'dosen'),
('dosen_b', '$2y$12$SpVBCaIUZ0UAKOg.khsnCuqk/vVUhjNWZpcCE1WfO4F/zxUntIGOu', 'Dr. Siti Nurhaliza', 'dosen'),
('dosen_c', '$2y$12$SpVBCaIUZ0UAKOg.khsnCuqk/vVUhjNWZpcCE1WfO4F/zxUntIGOu', 'Dr. Budi Hartono', 'dosen');

-- DOSEN
INSERT INTO dosen (user_id, nip, nama, prodi) VALUES
(2, '198001012010011001', 'Dr. Ahmad Fauzi', 'Teknik Informatika'),
(3, '198002022010011002', 'Dr. Siti Nurhaliza', 'Sistem Informasi'),
(4, '198003032010011003', 'Dr. Budi Hartono', 'Teknik Komputer');

-- KELAS
INSERT INTO kelas (nama_kelas, dosen_id) VALUES
('A', 1),
('B', 2),
('C', 3);

-- MAHASISWA (30 orang, 10 per kelas)
INSERT INTO users (username, password, nama, role) VALUES
('mhs_a1', '$2y$12$Og.rNdXJPUZsJ2vUYUOXEurcqEzYXAy/lWJYaMvueAty05p/PPg2e', 'Adi Pratama', 'mahasiswa'),
('mhs_a2', '$2y$12$u3l9wXgYiUeqB5Yd/Heas.LE1KyCSia8qofaZ1bBDB3lzEfJR/qn.', 'Bella Cindy', 'mahasiswa'),
('mhs_a3', '$2y$12$LDRrUP5ZzrJ/4yRloVYanOFdad1mkks9ADjVI26MpGnR6M32Wcyy2', 'Cahya Dwi', 'mahasiswa'),
('mhs_a4', '$2y$12$MgEBb7hEVy9R5iyH3fLILOV7vFLx4DYsbOCxDDH7XCwaPA30XSqFa', 'Dian Ekawati', 'mahasiswa'),
('mhs_a5', '$2y$12$HFY8fhd41c.Yn.jM8cXKXuyXFcdZEQ.lc5l7MSiKG5097bTQATV2i', 'Eko Prasetyo', 'mahasiswa'),
('mhs_a6', '$2y$12$qJ3ZI7pcEb0L5sd6S76Lpu8QooFtYRQ97.rm.kNpYLQyJC2P/XIH.', 'Fitri Handayani', 'mahasiswa'),
('mhs_a7', '$2y$12$7dhsRo6sneOzqYpmq4qzLOMaEuSgXUInm6lBR49vCvfYp/QHEkHDe', 'Gilang Ramadhan', 'mahasiswa'),
('mhs_a8', '$2y$12$6hcYRWB5O3JYELF7mrmuZe8YMV9Q5mFhurNglAf8k3Ymlo3/V89HC', 'Hesti Nurjanah', 'mahasiswa'),
('mhs_a9', '$2y$12$RjesW89w5yQBj2fezL/yz.kSQ2F3Jwe9ZmYh0nXIfWy3vnTnh5gEG', 'Irfan Maulana', 'mahasiswa'),
('mhs_a10', '$2y$12$LOameKXxTqthHXXbuqsv6eUYR43ere5f4fDEXHAZapEJS4GMCEsWG', 'Joko Susilo', 'mahasiswa'),
('mhs_b1', '$2y$12$yzfcodyrU30kEbegUP22uO0DrVjVggUPRnRo8o6yFTg1R44f1BlHC', 'Kartika Sari', 'mahasiswa'),
('mhs_b2', '$2y$12$aexL2gBcQXya4jbjFzoDEOsUnAmAX7qBcROT1HfDEI1XFDz45UFcO', 'Lutfi Hakim', 'mahasiswa'),
('mhs_b3', '$2y$12$SOSy7t5WsrrjjcgULoZFH.hKKT2AYXr5Ovj5Jo0Yh2Zdgf4uNnkFq', 'Mega Puspita', 'mahasiswa'),
('mhs_b4', '$2y$12$z0VvWQVobYDDzZu5b7ZkSO3YwNMRlPl1kH0mnvzUNkGPxWk8jnrCi', 'Nanda Pratama', 'mahasiswa'),
('mhs_b5', '$2y$12$kRlgoNDFnQg75V/WsAAXduD24Z/bGaVGlRxGd2ICslRMjgAqrjd7a', 'Oki Setiawan', 'mahasiswa'),
('mhs_b6', '$2y$12$/F2gbqDoCivWQcVRssKOR.ZqFPmdIIVlh9JHV7Oc2.XfW/Ti0rtkq', 'Putri Ayu', 'mahasiswa'),
('mhs_b7', '$2y$12$RMBOnbjOapaE05OuLwbQCOwjK5UqkvpqGQcW4lckk7G1SNm9kJTW6', 'Qori Amanda', 'mahasiswa'),
('mhs_b8', '$2y$12$Hces.eVHUxz.Yqsh7dllHu/.shcb/AmYVlYKmUkSl/pYNQ0ABWlqu', 'Rizky Fadhilah', 'mahasiswa'),
('mhs_b9', '$2y$12$pT0AU4FZs7GZ7ixJIiuDvOYFmfaeKNU0KAVR1EJi7ncArxpC9dSia', 'Sari Dewi', 'mahasiswa'),
('mhs_b10', '$2y$12$1TYvp.4sELZAX9K2VrUiwepwUTuccgW7oiDnok2dX2HR665OqOmIG', 'Teguh Wibowo', 'mahasiswa'),
('mhs_c1', '$2y$12$aYe3PFoVkKTtOpYQ.XnRL.1u0bYxJLTNk29LUmgmWL5DjBBxTuKyq', 'Umi Kalsum', 'mahasiswa'),
('mhs_c2', '$2y$12$h0/pXtd/tLZrxZnLOIA64.6Gs/TFkGvDJKKZuwtXALLwgvsjH/POy', 'Vina Amelia', 'mahasiswa'),
('mhs_c3', '$2y$12$jtDE8qL1TW3m8xKzmAo23OCdywQoFQAKWCCZDq6eY52Tf.oCAY0lu', 'Wahyu Nugroho', 'mahasiswa'),
('mhs_c4', '$2y$12$JkfI2qvhbm6497rAV0ucnehNw5VWpa6YRLSTisTm1jM839.QudTKu', 'Xavier Putra', 'mahasiswa'),
('mhs_c5', '$2y$12$xTprCarhu/ZxTtsERO7dNuj1lF3IyAE5VHmihCKbVQL4GZr7ilZVe', 'Yuni Astuti', 'mahasiswa'),
('mhs_c6', '$2y$12$9ExeRUAK7JyAVw4l7KmQi.2AL9iuY.S4RWeU03L1rHyMP9Ye2g8Vu', 'Zainal Arifin', 'mahasiswa'),
('mhs_c7', '$2y$12$BEh9tph3e.qPV7fVgWuoEumCt5GiCAVadGHxPXTw4lSqMGhIUAlHq', 'Aulia Rahman', 'mahasiswa'),
('mhs_c8', '$2y$12$XbqdipsuyUE9wqxgfPGGBOANdGOrdRKEDAhf70DaboVPMF4CbwWR2', 'Bunga Citra', 'mahasiswa'),
('mhs_c9', '$2y$12$jLDGD4XbnaCvjVdyoK6aHOg/wenzOEmSZQgjsbvUCZ3unK1MgRJz.', 'Citra Lestari', 'mahasiswa'),
('mhs_c10', '$2y$12$/YU6t4Lhfr3zf5LuwiZie.FuxdsMkjzjepE5e4/EPJRMJmfDVq.ae', 'Dimas Ardiansyah', 'mahasiswa');

-- MAHASISWA
INSERT INTO mahasiswa (user_id, nim, nama, kelas_id) VALUES
(5, '2510511000', 'Adi Pratama', 1),
(6, '2510511001', 'Bella Cindy', 1),
(7, '2510511002', 'Cahya Dwi', 1),
(8, '2510511003', 'Dian Ekawati', 1),
(9, '2510511004', 'Eko Prasetyo', 1),
(10, '2510511005', 'Fitri Handayani', 1),
(11, '2510511006', 'Gilang Ramadhan', 1),
(12, '2510511007', 'Hesti Nurjanah', 1),
(13, '2510511008', 'Irfan Maulana', 1),
(14, '2510511009', 'Joko Susilo', 1),
(15, '2510511010', 'Kartika Sari', 2),
(16, '2510511011', 'Lutfi Hakim', 2),
(17, '2510511012', 'Mega Puspita', 2),
(18, '2510511013', 'Nanda Pratama', 2),
(19, '2510511014', 'Oki Setiawan', 2),
(20, '2510511015', 'Putri Ayu', 2),
(21, '2510511016', 'Qori Amanda', 2),
(22, '2510511017', 'Rizky Fadhilah', 2),
(23, '2510511018', 'Sari Dewi', 2),
(24, '2510511019', 'Teguh Wibowo', 2),
(25, '2510511020', 'Umi Kalsum', 3),
(26, '2510511021', 'Vina Amelia', 3),
(27, '2510511022', 'Wahyu Nugroho', 3),
(28, '2510511023', 'Xavier Putra', 3),
(29, '2510511024', 'Yuni Astuti', 3),
(30, '2510511025', 'Zainal Arifin', 3),
(31, '2510511026', 'Aulia Rahman', 3),
(32, '2510511027', 'Bunga Citra', 3),
(33, '2510511028', 'Citra Lestari', 3),
(34, '2510511029', 'Dimas Ardiansyah', 3);
