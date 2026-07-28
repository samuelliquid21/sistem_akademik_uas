<?php
$allowedOrigins = ['http://localhost:8000', 'http://localhost:3000', 'http://127.0.0.1:8000'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
} else {
    header('Access-Control-Allow-Origin: http://localhost:8000');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$envFile = __DIR__ . '/../.env';
$env = (file_exists($envFile) && is_readable($envFile))
  ? parse_ini_file($envFile) : [];
// Environment variables override .env (used by Railway & hosting)
$host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: getenv('MARIADB_HOST') ?: ($env['DB_HOST'] ?? 'localhost');
$user = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: getenv('MARIADB_USER') ?: ($env['DB_USER'] ?? 'root');
$pass = getenv('DB_PASS') ?: getenv('MYSQL_PASSWORD') ?: getenv('MARIADB_PASSWORD') ?: ($env['DB_PASS'] ?? 'sardenggan123');
$db   = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: getenv('MARIADB_DATABASE') ?: ($env['DB_NAME'] ?? 'db_siakad');
$tokenSecret = getenv('TOKEN_SECRET') ?: ($env['TOKEN_SECRET'] ?? 'siakad_secret_key_change_me');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function get_input() {
    return json_decode(file_get_contents('php://input'), true) ?? $_POST;
}

function generate_token($userId) {
    global $tokenSecret;
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, $expires]);
    return $token;
}

function validate_token() {
    global $pdo;
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!$header) {
        $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    }
    if (!$header) {
        json_response(['error' => 'Unauthorized'], 401);
    }
    $parts = explode(' ', $header);
    $token = end($parts);

    $stmt = $pdo->prepare("SELECT s.*, u.role, u.username, u.nama FROM sessions s JOIN users u ON u.id = s.user_id WHERE s.token = ? AND s.expires_at > NOW()");
    $stmt->execute([$token]);
    $session = $stmt->fetch();

    if (!$session) {
        json_response(['error' => 'Invalid or expired token'], 401);
    }

    return $session;
}
