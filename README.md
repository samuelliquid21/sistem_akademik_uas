# SIAKAD — Sistem Deteksi Pelanggaran Akademik

Aplikasi web untuk **deteksi pelanggaran akademik mahasiswa** menggunakan **Forward Chaining Inference Engine** berbasis logika proposisional. Dibangun dengan React (JSX) + PHP + MySQL.

![Stack](https://img.shields.io/badge/React-18-61DAFB?logo=react)
![Stack](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php)
![Stack](https://img.shields.io/badge/MySQL-11.8-4479A1?logo=mysql)
![Stack](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss)

---

## Fitur

### 🔐 Keamanan
- Autentikasi token Bearer (login → token, wajib di setiap request)
- Session expiry 24 jam
- Database credentials via `.env` (tidak hardcode)
- CORS terbatas (tidak `*`)
- Password di-hash dengan `bcrypt`

### 👥 Multi-Role
- **Admin** — kelola data mahasiswa, dosen, kelas, lihat semua deteksi
- **Dosen** — input deteksi pelanggaran untuk mahasiswa binaan
- **Mahasiswa** — lihat riwayat deteksi sendiri

### 🧠 Inference Engine (Forward Chaining)
4 aturan logika proposisional:

| Rule | Ekspresi | Keterangan |
|------|----------|------------|
| R1 | `Q → Pelanggaran Berat` | Plagiarisme → Pelanggaran Berat |
| R2 | `R → Pelanggaran Berat` | Menyontek → Pelanggaran Berat |
| R3 | `(P ∧ ¬S) → Peringatan Akademik` | Hadir < 75% & Tugas Telat → Peringatan Akademik |
| R4 | `(¬Q ∧ ¬R ∧ ¬(P∧¬S)) → Status Aman` | Tidak ada pelanggaran → Status Aman |

### 📊 Dashboard & Visualisasi
- Auto-refresh dashboard tiap 30 detik
- Grafik batang (Chart.js) — perbandingan status aman vs pelanggaran berat
- Cards statistik real-time

### 📋 Manajemen Data (CRUD)
- **Mahasiswa** — tambah, edit, hapus, cari (NIM/nama)
- **Dosen** — tambah, edit, hapus
- **Kelas** — tambah, edit, hapus
- **Riwayat Deteksi** — filter, cari, cetak (print-friendly)

### 🧪 Uji Coba
- 24 skenario pengujian inference engine
- Tabel kebenaran untuk setiap rule
- Reasoning path detail (forward chaining step-by-step)

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Frontend | React 18 (CDN via Babel Standalone) |
| Styling | Tailwind CSS 3 (CDN) |
| Icons | Solar Icon Set (Iconify) |
| Chart | Chart.js 4 |
| Backend | PHP 8.5 (vanilla, no framework) |
| Database | MySQL / MariaDB 11 |
| Auth | Bearer Token (random bytes + sessions table) |

---

## Instalasi Lokal

### Prasyarat
- PHP 8+ dengan `pdo_mysql`
- MySQL / MariaDB
- Composer (tidak wajib, proyek tanpa dependency)

### Langkah

```bash
# 1. Clone repositori
git clone https://github.com/username/siakad.git
cd siakad

# 2. Copy .env dan isi kredensial database
cp .env.example siakad/.env
# Edit siakad/.env sesuai database kamu

# 3. Setup database (buat database & seed data)
php siakad/setup.php

# 4. Jalankan development server
php -S localhost:8000 -t siakad/

# 5. Buka browser
open http://localhost:8000/app.html
```

### Akun Demo

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin` |
| Dosen A | `dosen_a` | `kelas_a` |
| Dosen B | `dosen_b` | `kelas_b` |
| Dosen C | `dosen_c` | `kelas_c` |
| Mahasiswa | `mhs_a1` | `11000` |

> Password mahasiswa = 5 digit terakhir NIM (2510511**1000**)

---

## Deploy ke Railway

```bash
# 1. Push ke GitHub
git add .
git commit -m "Initial commit"
git push

# 2. Buka railway.app → New Project → Deploy from GitHub
# 3. Tambah MySQL plugin
# 4. Set environment variables:
#    DB_HOST, DB_USER, DB_PASS, DB_NAME, TOKEN_SECRET
# 5. Buka URL yang diberikan Railway → /app.html
```

> Pastikan `setup.php` sudah dihapus setelah database terisi.

---

## Struktur Proyek

```
siakad/
├── api/               # REST API endpoints
│   ├── config.php     # Koneksi DB, functions, auth
│   ├── login.php      # Login → return token
│   ├── dashboard.php  # Dashboard stats per role
│   ├── mahasiswa.php  # Data mahasiswa
│   ├── dosen.php      # Data dosen
│   ├── kelas.php      # Data kelas
│   ├── deteksi.php    # CRUD deteksi
│   ├── rules.php      # Aturan & gejala
│   ├── admin.php      # CRUD admin (add/edit/delete)
│   ├── profile.php    # Profile & ganti password
│   └── test_cases.php # 24 skenario uji
├── engine/
│   └── inference.php  # Forward chaining engine
├── app.html           # Single-page React app
├── database.sql       # Schema + seed data
├── .env               # Kredensial (gitignored)
└── setup.php          # Setup database (hapus setelah deploy)
```

---

## Lisensi

MIT — bebas digunakan untuk pembelajaran dan pengembangan.
