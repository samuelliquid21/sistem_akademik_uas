#include<iostream>
#include <string>
#include <vector>
#include <map>
#include <iomanip>
using namespace std;

// ============================================================
//   SISTEM DETEKSI PELANGGARAN AKADEMIK
//   Berbasis Logika Proposisional & Forward Chaining
// ============================================================

// === VARIABEL PROPOSISI ===
// P = Kehadiran < 75%
// Q = Terbukti plagiarisme
// R = Menyontek saat ujian
// S = Mengumpulkan tugas tepat waktu

struct Mahasiswa {
    string nama;
    string nim;
    bool P; // Kehadiran < 75%
    bool Q; // Terbukti plagiarisme
    bool R; // Menyontek saat ujian
    bool S; // Mengumpulkan tugas tepat waktu
};

struct HasilDeteksi {
    bool pelanggaranBerat;
    bool peringatanAkademik;
    bool statusAman;
    vector<string> reasoningPath;
    vector<string> ruleTerpenuhi;
};

// ============================================================
//   TAMPILAN HEADER
// ============================================================
void printHeader() {
    cout << "\n";
    cout << "================================================================\n";
    cout << "        SISTEM DETEKSI PELANGGARAN AKADEMIK                     \n";
    cout << "        Berbasis Logika Proposisional & Forward Chaining        \n";
    cout << "================================================================\n";
}

void printSeparator() {
    cout << "----------------------------------------------------------------\n";
}

void printDoubleSeparator() {
    cout << "================================================================\n";
}

// ============================================================
//   MANAJEMEN RULE (Knowledge Base)
// ============================================================
void tampilkanRule() {
    cout << "\n";
    printDoubleSeparator();
    cout << "  MANAJEMEN RULE - KNOWLEDGE BASE\n";
    printDoubleSeparator();
    cout << "\n";
    cout << "  VARIABEL PROPOSISI:\n";
    printSeparator();
    cout << "  P = Kehadiran mahasiswa < 75%\n";
    cout << "  Q = Terbukti melakukan plagiarisme\n";
    cout << "  R = Terbukti menyontek saat ujian\n";
    cout << "  S = Mengumpulkan tugas tepat waktu\n";
    cout << "\n";
    cout << "  ATURAN LOGIKA:\n";
    printSeparator();
    cout << "  Rule 1: Q -> Pelanggaran Berat\n";
    cout << "          Jika plagiarisme maka pelanggaran berat.\n\n";
    cout << "  Rule 2: R -> Pelanggaran Berat\n";
    cout << "          Jika menyontek maka pelanggaran berat.\n\n";
    cout << "  Rule 3: (P ^ ~S) -> Peringatan Akademik\n";
    cout << "          Jika kehadiran kurang DAN tugas tidak tepat waktu\n";
    cout << "          maka peringatan akademik.\n\n";
    cout << "  Rule 4: (~Q ^ ~R ^ ~(P ^ ~S)) -> Status Aman\n";
    cout << "          Jika tidak ada pelanggaran maka status aman.\n";
    printDoubleSeparator();
}

// ============================================================
//   TABEL KEBENARAN
// ============================================================
void tampilkanTabelKebenaran() {
    cout << "\n";
    printDoubleSeparator();
    cout << "  TABEL KEBENARAN\n";
    printDoubleSeparator();

    // Rule 1: Q -> Pelanggaran Berat
    cout << "\n  Rule 1: Q -> Pelanggaran Berat\n";
    printSeparator();
    cout << "  Q     | Pelanggaran Berat\n";
    printSeparator();
    cout << "  TRUE  | TRUE\n";
    cout << "  FALSE | FALSE\n";

    // Rule 2: R -> Pelanggaran Berat
    cout << "\n  Rule 2: R -> Pelanggaran Berat\n";
    printSeparator();
    cout << "  R     | Pelanggaran Berat\n";
    printSeparator();
    cout << "  TRUE  | TRUE\n";
    cout << "  FALSE | FALSE\n";

    // Rule 3: P ^ ~S -> Peringatan Akademik
    cout << "\n  Rule 3: (P ^ ~S) -> Peringatan Akademik\n";
    printSeparator();
    cout << "  P     | S     | ~S    | P^~S  | Peringatan\n";
    printSeparator();
    cout << "  TRUE  | TRUE  | FALSE | FALSE | FALSE\n";
    cout << "  TRUE  | FALSE | TRUE  | TRUE  | TRUE\n";
    cout << "  FALSE | TRUE  | FALSE | FALSE | FALSE\n";
    cout << "  FALSE | FALSE | TRUE  | FALSE | FALSE\n";

    printDoubleSeparator();
}

// ============================================================
//   INFERENCE ENGINE - FORWARD CHAINING
// ============================================================
HasilDeteksi inferenceEngine(Mahasiswa& mhs) {
    HasilDeteksi hasil;
    hasil.pelanggaranBerat = false;
    hasil.peringatanAkademik = false;
    hasil.statusAman = false;

    // Fakta awal
    hasil.reasoningPath.push_back("=== FAKTA AWAL ===");
    hasil.reasoningPath.push_back("P (Kehadiran < 75%)     = " + string(mhs.P ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("Q (Plagiarisme)         = " + string(mhs.Q ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("R (Menyontek)           = " + string(mhs.R ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("S (Tugas Tepat Waktu)   = " + string(mhs.S ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("");

    hasil.reasoningPath.push_back("=== EVALUASI RULE ===");

    // Rule 1: Q -> Pelanggaran Berat
    hasil.reasoningPath.push_back("");
    hasil.reasoningPath.push_back("Rule 1: Q -> Pelanggaran Berat");
    hasil.reasoningPath.push_back("        Q = " + string(mhs.Q ? "TRUE" : "FALSE"));
    if (mhs.Q) {
        hasil.pelanggaranBerat = true;
        hasil.reasoningPath.push_back("        => TRUE, Rule 1 TERPENUHI");
        hasil.ruleTerpenuhi.push_back("Rule 1: Q -> Pelanggaran Berat (TERPENUHI)");
    } else {
        hasil.reasoningPath.push_back("        => FALSE, Rule 1 tidak terpenuhi");
    }

    // Rule 2: R -> Pelanggaran Berat
    hasil.reasoningPath.push_back("");
    hasil.reasoningPath.push_back("Rule 2: R -> Pelanggaran Berat");
    hasil.reasoningPath.push_back("        R = " + string(mhs.R ? "TRUE" : "FALSE"));
    if (mhs.R) {
        hasil.pelanggaranBerat = true;
        hasil.reasoningPath.push_back("        => TRUE, Rule 2 TERPENUHI");
        hasil.ruleTerpenuhi.push_back("Rule 2: R -> Pelanggaran Berat (TERPENUHI)");
    } else {
        hasil.reasoningPath.push_back("        => FALSE, Rule 2 tidak terpenuhi");
    }

    // Rule 3: P ^ ~S -> Peringatan Akademik
    bool tidakTepatWaktu = !mhs.S;
    bool rule3 = mhs.P && tidakTepatWaktu;
    hasil.reasoningPath.push_back("");
    hasil.reasoningPath.push_back("Rule 3: (P ^ ~S) -> Peringatan Akademik");
    hasil.reasoningPath.push_back("        P  = " + string(mhs.P ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("        S  = " + string(mhs.S ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("        ~S = " + string(tidakTepatWaktu ? "TRUE" : "FALSE"));
    hasil.reasoningPath.push_back("        P ^ ~S = " + string(rule3 ? "TRUE" : "FALSE"));
    if (rule3) {
        hasil.peringatanAkademik = true;
        hasil.reasoningPath.push_back("        => TRUE, Rule 3 TERPENUHI");
        hasil.ruleTerpenuhi.push_back("Rule 3: (P ^ ~S) -> Peringatan Akademik (TERPENUHI)");
    } else {
        hasil.reasoningPath.push_back("        => FALSE, Rule 3 tidak terpenuhi");
    }

    // Rule 4: Status Aman
    hasil.reasoningPath.push_back("");
    hasil.reasoningPath.push_back("Rule 4: Tidak ada pelanggaran -> Status Aman");
    if (!hasil.pelanggaranBerat && !hasil.peringatanAkademik) {
        hasil.statusAman = true;
        hasil.reasoningPath.push_back("        => Tidak ada rule lain terpenuhi");
        hasil.reasoningPath.push_back("        => TRUE, Rule 4 TERPENUHI");
        hasil.ruleTerpenuhi.push_back("Rule 4: Status Aman (TERPENUHI)");
    } else {
        hasil.reasoningPath.push_back("        => Ada pelanggaran ditemukan, Rule 4 tidak berlaku");
    }

    return hasil;
}

// ============================================================
//   VISUALISASI LOGIKA
// ============================================================
void tampilkanVisualisasiLogika(Mahasiswa& mhs, HasilDeteksi& hasil) {
    cout << "\n";
    printDoubleSeparator();
    cout << "  VISUALISASI LOGIKA\n";
    printDoubleSeparator();
    cout << "\n";
    cout << "  INPUT FAKTA:\n";
    cout << "  +-------+-------------------------+-------+\n";
    cout << "  | Kode  | Proposisi               | Nilai |\n";
    cout << "  +-------+-------------------------+-------+\n";
    cout << "  |  P    | Kehadiran < 75%         |  " << (mhs.P ? "TRUE " : "FALSE") << "  |\n";
    cout << "  |  Q    | Terbukti Plagiarisme     |  " << (mhs.Q ? "TRUE " : "FALSE") << "  |\n";
    cout << "  |  R    | Menyontek saat Ujian     |  " << (mhs.R ? "TRUE " : "FALSE") << "  |\n";
    cout << "  |  S    | Tugas Tepat Waktu        |  " << (mhs.S ? "TRUE " : "FALSE") << "  |\n";
    cout << "  +-------+-------------------------+-------+\n";
    cout << "\n";
    cout << "  EVALUASI RULE:\n";
    cout << "  +--------+----------------------+----------+\n";
    cout << "  | Rule   | Ekspresi Logika      | Hasil    |\n";
    cout << "  +--------+----------------------+----------+\n";
    cout << "  | Rule 1 | Q -> Pelanggar Berat | " << (mhs.Q ? "TERPENUHI" : "TIDAK    ") << " |\n";
    cout << "  | Rule 2 | R -> Pelanggar Berat | " << (mhs.R ? "TERPENUHI" : "TIDAK    ") << " |\n";
    cout << "  | Rule 3 | P^~S -> Peringatan   | " << (mhs.P && !mhs.S ? "TERPENUHI" : "TIDAK    ") << " |\n";
    cout << "  | Rule 4 | ~semua -> Aman       | " << (hasil.statusAman ? "TERPENUHI" : "TIDAK    ") << " |\n";
    cout << "  +--------+----------------------+----------+\n";
    printDoubleSeparator();
}

// ============================================================
//   REASONING PATH
// ============================================================
void tampilkanReasoningPath(HasilDeteksi& hasil) {
    cout << "\n";
    printDoubleSeparator();
    cout << "  REASONING PATH (Forward Chaining)\n";
    printDoubleSeparator();
    cout << "\n";
    for (auto& path : hasil.reasoningPath) {
        cout << "  " << path << "\n";
    }
    printDoubleSeparator();
}

// ============================================================
//   OUTPUT HASIL
// ============================================================
void tampilkanOutput(Mahasiswa& mhs, HasilDeteksi& hasil) {
    cout << "\n";
    printDoubleSeparator();
    cout << "  OUTPUT - HASIL DETEKSI PELANGGARAN AKADEMIK\n";
    printDoubleSeparator();
    cout << "\n";
    cout << "  Nama Mahasiswa : " << mhs.nama << "\n";
    cout << "  NIM            : " << mhs.nim << "\n";
    cout << "\n";
    cout << "  Input Data:\n";
    printSeparator();
    cout << "  " << (mhs.P ? "v" : "x") << " Kehadiran < 75%\n";
    cout << "  " << (mhs.Q ? "v" : "x") << " Terbukti Plagiarisme\n";
    cout << "  " << (mhs.R ? "v" : "x") << " Menyontek saat Ujian\n";
    cout << "  " << (mhs.S ? "v" : "x") << " Tugas Tepat Waktu\n";
    cout << "\n";
    printSeparator();
    cout << "  Rule Terpenuhi:\n";
    printSeparator();
    if (hasil.ruleTerpenuhi.empty()) {
        cout << "  (tidak ada rule terpenuhi)\n";
    } else {
        for (auto& r : hasil.ruleTerpenuhi) {
            cout << "  v " << r << "\n";
        }
    }
    cout << "\n";
    printSeparator();
    cout << "  STATUS AKHIR:\n";
    printSeparator();
    if (hasil.pelanggaranBerat) {
        cout << "  * PELANGGARAN BERAT *\n";
        cout << "  Sanksi: Skorsing / Pembatalan Nilai\n";
    }
    if (hasil.peringatanAkademik) {
        cout << "  * PERINGATAN AKADEMIK *\n";
        cout << "  Sanksi: Surat Peringatan dari Akademik\n";
    }
    if (hasil.statusAman) {
        cout << "  >>> STATUS AMAN <<<\n";
        cout << "  Tidak ditemukan pelanggaran akademik.\n";
    }
    printDoubleSeparator();
}

// ============================================================
//   INPUT DATA MAHASISWA
// ============================================================
Mahasiswa inputData() {
    Mahasiswa mhs;
    int pilihan;

    cout << "\n";
    printDoubleSeparator();
    cout << "  INPUT DATA MAHASISWA\n";
    printDoubleSeparator();
    cout << "\n";
    cout << "  Nama Mahasiswa : ";
    cin.ignore();
    getline(cin, mhs.nama);
    cout << "  NIM            : ";
    getline(cin, mhs.nim);

    cout << "\n  Jawab dengan 1 (Ya) atau 0 (Tidak):\n\n";

    cout << "  P - Kehadiran mahasiswa < 75%?        : ";
    cin >> pilihan; mhs.P = (pilihan == 1);

    cout << "  Q - Terbukti melakukan plagiarisme?   : ";
    cin >> pilihan; mhs.Q = (pilihan == 1);

    cout << "  R - Terbukti menyontek saat ujian?    : ";
    cin >> pilihan; mhs.R = (pilihan == 1);

    cout << "  S - Mengumpulkan tugas tepat waktu?   : ";
    cin >> pilihan; mhs.S = (pilihan == 1);

    return mhs;
}

// ============================================================
//   SKENARIO PENGUJIAN (24 Kasus - per Reasoning Path)
// ============================================================
void jalankanTestCase() {
    // Format: {P, Q, R, S, label, keterangan reasoning path}
    // K1 = Q v R (Pelanggaran Berat)
    // K2 = P ^ ~S (Peringatan Akademik)
    vector<tuple<bool,bool,bool,bool,string>> testCases = {
        // === STATUS AMAN (K1=F, K2=F) ===
        {false, false, false, true,  "Skenario 1  - Aman: tdk ada pelanggaran, tugas tepat"},
        {false, false, false, false, "Skenario 2  - Aman: tdk plagiat, tdk sontek, hadir cukup"},
        {true,  false, false, true,  "Skenario 3  - Aman: hadir kurang tapi tugas tepat waktu"},

        // === PERINGATAN AKADEMIK SAJA (K1=F, K2=T) ===
        {true,  false, false, false, "Skenario 4  - Peringatan: hadir kurang & tugas telat"},

        // === K1 VIA Q SAJA (Q=T, R=F) ===
        {false, true,  false, true,  "Skenario 5  - Berat via Q: plagiat, tugas tepat"},
        {false, true,  false, false, "Skenario 6  - Berat via Q: plagiat, tugas telat"},
        {true,  true,  false, true,  "Skenario 7  - Berat via Q: plagiat, hadir kurang, tugas tepat"},
        {true,  true,  false, false, "Skenario 8  - Berat+Peringatan via Q: plagiat, hadir kurang, tugas telat"},

        // === K1 VIA R SAJA (Q=F, R=T) ===
        {false, false, true,  true,  "Skenario 9  - Berat via R: sontek, tugas tepat"},
        {false, false, true,  false, "Skenario 10 - Berat via R: sontek, tugas telat"},
        {true,  false, true,  true,  "Skenario 11 - Berat via R: sontek, hadir kurang, tugas tepat"},
        {true,  false, true,  false, "Skenario 12 - Berat+Peringatan via R: sontek, hadir kurang, tugas telat"},

        // === K1 VIA Q DAN R (Q=T, R=T) ===
        {false, true,  true,  true,  "Skenario 13 - Berat via Q+R: plagiat & sontek, tugas tepat"},
        {false, true,  true,  false, "Skenario 14 - Berat via Q+R: plagiat & sontek, tugas telat"},
        {true,  true,  true,  true,  "Skenario 15 - Berat via Q+R: plagiat & sontek, hadir kurang, tugas tepat"},
        {true,  true,  true,  false, "Skenario 16 - Berat+Peringatan via Q+R: semua pelanggaran"},

        // === KASUS EDGE / BATAS ===
        {true,  false, false, false, "Skenario 17 - Edge: tepat di batas hadir & tugas telat"},
        {false, true,  false, true,  "Skenario 18 - Edge: hanya plagiat, lainnya bersih"},
        {false, false, true,  true,  "Skenario 19 - Edge: hanya menyontek, lainnya bersih"},
        {true,  true,  false, false, "Skenario 20 - Edge: plagiat + hadir kurang + tugas telat"},
        {true,  false, true,  false, "Skenario 21 - Edge: sontek + hadir kurang + tugas telat"},
        {false, true,  true,  false, "Skenario 22 - Edge: plagiat + sontek + tugas telat"},
        {true,  true,  true,  false, "Skenario 23 - Edge: semua TRUE kecuali tugas tepat"},
        {true,  true,  true,  true,  "Skenario 24 - Edge: semua TRUE termasuk tugas tepat"},
    };

    cout << "\n";
    printDoubleSeparator();
    cout << "  HASIL 24 SKENARIO PENGUJIAN\n";
    cout << "  * Dikelompokkan per Reasoning Path (K1 via Q / R / Q+R)\n";
    printDoubleSeparator();
    cout << "  +-------------+---+---+---+---+--------------------+\n";
    cout << "  | Skenario    | P | Q | R | S | Status             |\n";
    cout << "  +-------------+---+---+---+---+--------------------+\n";

    for (auto& [P, Q, R, S, label] : testCases) {
        Mahasiswa mhs;
        mhs.P = P; mhs.Q = Q; mhs.R = R; mhs.S = S;
        HasilDeteksi h = inferenceEngine(mhs);

        string status = "";
        if (h.pelanggaranBerat && h.peringatanAkademik) status = "Berat + Peringatan  ";
        else if (h.pelanggaranBerat)     status = "Pelanggaran Berat   ";
        else if (h.peringatanAkademik)   status = "Peringatan Akademik ";
        else                             status = "Status Aman         ";

        // ambil 12 char pertama label untuk kolom
        string lbl = label.substr(0, 12);
        cout << "  | " << left << setw(12) << lbl
             << "| " << (P?"T":"F") << " | " << (Q?"T":"F")
             << " | " << (R?"T":"F") << " | " << (S?"T":"F")
             << " | " << status << "|\n";
    }
    cout << "  +-------------+---+---+---+---+--------------------+\n";
    printDoubleSeparator();
}

// ============================================================
//   MENU UTAMA
// ============================================================
int main() {
    int menu;
    bool running = true;

    printHeader();

    while (running) {
        cout << "\n  MENU UTAMA:\n";
        printSeparator();
        cout << "  1. Lihat Manajemen Rule (Knowledge Base)\n";
        cout << "  2. Lihat Tabel Kebenaran\n";
        cout << "  3. Input Data & Deteksi Pelanggaran\n";
        cout << "  4. Jalankan 20 Skenario Pengujian\n";
        cout << "  5. Keluar\n";
        printSeparator();
        cout << "  Pilih menu [1-5]: ";
        cin >> menu;

        switch (menu) {
            case 1:
                tampilkanRule();
                break;
            case 2:
                tampilkanTabelKebenaran();
                break;
            case 3: {
                Mahasiswa mhs = inputData();
                HasilDeteksi hasil = inferenceEngine(mhs);
                tampilkanVisualisasiLogika(mhs, hasil);
                tampilkanReasoningPath(hasil);
                tampilkanOutput(mhs, hasil);
                break;
            }
            case 4:
                jalankanTestCase();
                break;
            case 5:
                cout << "\n  Terima kasih. Program selesai.\n\n";
                running = false;
                break;
            default:
                cout << "\n  Pilihan tidak valid!\n";
        }
    }

    return 0;
}