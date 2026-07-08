<?php
$host = 'localhost'; $user = 'root'; $pass = 'sardenggan123';
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS db_siakad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database db_siakad created<br>";
    $pdo->exec("USE db_siakad");
    
    // Run the SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    // Get only the part after USE db_siakad
    $parts = explode("USE db_siakad;", $sql);
    $pdo->exec($parts[1]);
    echo "✅ All tables and data seeded successfully<br><br>";
    echo "🔗 <a href='app.html' style='background:#1e3a5f;color:white;padding:10px 20px;border-radius:8px;text-decoration:none'>→ Buka SIAKAD</a>";
} catch (PDOException $e) {
    echo "❌ " . $e->getMessage();
}
