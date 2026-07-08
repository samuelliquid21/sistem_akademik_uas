<?php
// ============================================================
// INFERENCE ENGINE - FORWARD CHAINING
// Porting dari logika_uas_akademik.cpp
// ============================================================

class InferenceEngine {
    private $P;
    private $Q;
    private $R;
    private $S;
    
    public $pelanggaranBerat = false;
    public $peringatanAkademik = false;
    public $statusAman = false;
    public $reasoningPath = [];
    public $ruleTerpenuhi = [];

    public function __construct($P, $Q, $R, $S) {
        $this->P = $P;
        $this->Q = $Q;
        $this->R = $R;
        $this->S = $S;
    }

    public function run() {
        // Fakta awal
        $this->reasoningPath[] = "=== FAKTA AWAL ===";
        $this->reasoningPath[] = "P (Kehadiran < 75%)     = " . ($this->P ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "Q (Plagiarisme)         = " . ($this->Q ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "R (Menyontek)           = " . ($this->R ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "S (Tugas Tepat Waktu)   = " . ($this->S ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "";
        $this->reasoningPath[] = "=== EVALUASI RULE ===";

        // Rule 1: Q -> Pelanggaran Berat
        $this->reasoningPath[] = "";
        $this->reasoningPath[] = "Rule 1: Q -> Pelanggaran Berat";
        $this->reasoningPath[] = "        Q = " . ($this->Q ? 'TRUE' : 'FALSE');
        if ($this->Q) {
            $this->pelanggaranBerat = true;
            $this->reasoningPath[] = "        => TRUE, Rule 1 TERPENUHI";
            $this->ruleTerpenuhi[] = "Rule 1: Q -> Pelanggaran Berat (TERPENUHI)";
        } else {
            $this->reasoningPath[] = "        => FALSE, Rule 1 tidak terpenuhi";
        }

        // Rule 2: R -> Pelanggaran Berat
        $this->reasoningPath[] = "";
        $this->reasoningPath[] = "Rule 2: R -> Pelanggaran Berat";
        $this->reasoningPath[] = "        R = " . ($this->R ? 'TRUE' : 'FALSE');
        if ($this->R) {
            $this->pelanggaranBerat = true;
            $this->reasoningPath[] = "        => TRUE, Rule 2 TERPENUHI";
            $this->ruleTerpenuhi[] = "Rule 2: R -> Pelanggaran Berat (TERPENUHI)";
        } else {
            $this->reasoningPath[] = "        => FALSE, Rule 2 tidak terpenuhi";
        }

        // Rule 3: (P ^ ~S) -> Peringatan Akademik
        $tidakTepatWaktu = !$this->S;
        $rule3 = $this->P && $tidakTepatWaktu;
        $this->reasoningPath[] = "";
        $this->reasoningPath[] = "Rule 3: (P ^ ~S) -> Peringatan Akademik";
        $this->reasoningPath[] = "        P  = " . ($this->P ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "        S  = " . ($this->S ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "        ~S = " . ($tidakTepatWaktu ? 'TRUE' : 'FALSE');
        $this->reasoningPath[] = "        P ^ ~S = " . ($rule3 ? 'TRUE' : 'FALSE');
        if ($rule3) {
            $this->peringatanAkademik = true;
            $this->reasoningPath[] = "        => TRUE, Rule 3 TERPENUHI";
            $this->ruleTerpenuhi[] = "Rule 3: (P ^ ~S) -> Peringatan Akademik (TERPENUHI)";
        } else {
            $this->reasoningPath[] = "        => FALSE, Rule 3 tidak terpenuhi";
        }

        // Rule 4: Status Aman
        $this->reasoningPath[] = "";
        $this->reasoningPath[] = "Rule 4: Tidak ada pelanggaran -> Status Aman";
        if (!$this->pelanggaranBerat && !$this->peringatanAkademik) {
            $this->statusAman = true;
            $this->reasoningPath[] = "        => Tidak ada rule lain terpenuhi";
            $this->reasoningPath[] = "        => TRUE, Rule 4 TERPENUHI";
            $this->ruleTerpenuhi[] = "Rule 4: Status Aman (TERPENUHI)";
        } else {
            $this->reasoningPath[] = "        => Ada pelanggaran ditemukan, Rule 4 tidak berlaku";
        }

        return [
            'pelanggaran_berat' => $this->pelanggaranBerat,
            'peringatan_akademik' => $this->peringatanAkademik,
            'status_aman' => $this->statusAman,
            'status_label' => $this->getStatusLabel(),
            'reasoning_path' => $this->reasoningPath,
            'rule_terpenuhi' => $this->ruleTerpenuhi
        ];
    }

    public function getStatusLabel() {
        if ($this->pelanggaranBerat && $this->peringatanAkademik)
            return 'Pelanggaran Berat + Peringatan Akademik';
        if ($this->pelanggaranBerat)
            return 'Pelanggaran Berat';
        if ($this->peringatanAkademik)
            return 'Peringatan Akademik';
        return 'Status Aman';
    }

    // Untuk test cases
    public static function runTestCase($P, $Q, $R, $S) {
        $engine = new self($P, $Q, $R, $S);
        return $engine->run();
    }

    // Generate 24 test cases
    public static function allTestCases() {
        $cases = [
            [false, false, false, true,  'Skenario 1  - Aman: tdk ada pelanggaran, tugas tepat'],
            [false, false, false, false, 'Skenario 2  - Aman: tdk plagiat, tdk sontek, hadir cukup'],
            [true,  false, false, true,  'Skenario 3  - Aman: hadir kurang tapi tugas tepat'],
            [true,  false, false, false, 'Skenario 4  - Peringatan: hadir kurang & tugas telat'],
            [false, true,  false, true,  'Skenario 5  - Berat via Q: plagiat, tugas tepat'],
            [false, true,  false, false, 'Skenario 6  - Berat via Q: plagiat, tugas telat'],
            [true,  true,  false, true,  'Skenario 7  - Berat via Q: plagiat, hadir kurang, tugas tepat'],
            [true,  true,  false, false, 'Skenario 8  - Berat+Peringatan via Q: plagiat, hadir kurang, tugas telat'],
            [false, false, true,  true,  'Skenario 9  - Berat via R: sontek, tugas tepat'],
            [false, false, true,  false, 'Skenario 10 - Berat via R: sontek, tugas telat'],
            [true,  false, true,  true,  'Skenario 11 - Berat via R: sontek, hadir kurang, tugas tepat'],
            [true,  false, true,  false, 'Skenario 12 - Berat+Peringatan via R: sontek, hadir kurang, tugas telat'],
            [false, true,  true,  true,  'Skenario 13 - Berat via Q+R: plagiat & sontek, tugas tepat'],
            [false, true,  true,  false, 'Skenario 14 - Berat via Q+R: plagiat & sontek, tugas telat'],
            [true,  true,  true,  true,  'Skenario 15 - Berat via Q+R: semua, tugas tepat'],
            [true,  true,  true,  false, 'Skenario 16 - Berat+Peringatan via Q+R: semua pelanggaran'],
            [true,  false, false, false, 'Skenario 17 - Edge: tepat di batas hadir & tugas telat'],
            [false, true,  false, true,  'Skenario 18 - Edge: hanya plagiat, lainnya bersih'],
            [false, false, true,  true,  'Skenario 19 - Edge: hanya menyontek, lainnya bersih'],
            [true,  true,  false, false, 'Skenario 20 - Edge: plagiat + hadir kurang + tugas telat'],
            [true,  false, true,  false, 'Skenario 21 - Edge: sontek + hadir kurang + tugas telat'],
            [false, true,  true,  false, 'Skenario 22 - Edge: plagiat + sontek + tugas telat'],
            [true,  true,  true,  false, 'Skenario 23 - Edge: semua TRUE kecuali tugas tepat'],
            [true,  true,  true,  true,  'Skenario 24 - Edge: semua TRUE termasuk tugas tepat'],
        ];

        $results = [];
        foreach ($cases as $c) {
            $result = self::runTestCase($c[0], $c[1], $c[2], $c[3]);
            $results[] = [
                'label' => $c[4],
                'P' => $c[0], 'Q' => $c[1], 'R' => $c[2], 'S' => $c[3],
                'status' => $result['status_label'],
                'pelanggaran_berat' => $result['pelanggaran_berat'],
                'peringatan_akademik' => $result['peringatan_akademik'],
                'status_aman' => $result['status_aman']
            ];
        }
        return $results;
    }
}
