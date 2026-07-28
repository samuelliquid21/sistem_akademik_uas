<?php
header('Content-Type: text/plain');
$vars = ['DB_HOST','DB_USER','DB_PASS','DB_NAME','MYSQL_HOST','MYSQL_USER','MYSQL_PASSWORD','MYSQL_DATABASE','MYSQL_URL','MYSQL_PORT','MARIADB_HOST','MARIADB_URL','TOKEN_SECRET','PORT'];
foreach ($vars as $v) {
    $val = getenv($v);
    echo "$v = " . ($val ? substr($val, 0, 30) . '...' : '(not set)') . "\n";
}
