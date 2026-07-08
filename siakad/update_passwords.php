<?php
$host='localhost';$user='root';$pass='sardenggan123';$db='db_siakad';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

// Admin
$pdo->prepare("UPDATE users SET password=? WHERE username='admin'")->execute([password_hash('admin',PASSWORD_DEFAULT)]);
echo "✅ admin / admin\n";

// Dosen: password = kelas_a, kelas_b, kelas_c
$dosenPw = ['dosen_a'=>'kelas_a','dosen_b'=>'kelas_b','dosen_c'=>'kelas_c'];
foreach ($dosenPw as $uname => $pw) {
    $pdo->prepare("UPDATE users SET password=? WHERE username=?")->execute([password_hash($pw,PASSWORD_DEFAULT), $uname]);
    echo "✅ $uname / $pw\n";
}

// Mahasiswa: password = 5 digit terakhir NIM
for ($i=0;$i<=29;$i++) {
    $nim = 2510511000 + $i;
    $pw = substr($nim,-5);
    $uname = 'mhs_' . chr(ord('a') + intdiv($i,10)) . (($i%10)+1);
    $pdo->prepare("UPDATE users SET password=? WHERE username=?")->execute([password_hash($pw,PASSWORD_DEFAULT), $uname]);
    echo "✅ $uname (NIM $nim) / $pw\n";
}
echo "\n✅ SEMUA PASSWORD BERHASIL DIUPDATE!";
