<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$db = new PDO('mysql:host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'].';dbname='.$_ENV['DB_DATABASE'].';charset=utf8mb4', $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
$stmt = $db->query('SELECT kelas, COUNT(*) AS cnt FROM siswas GROUP BY kelas ORDER BY cnt DESC');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['kelas'] . '|' . $row['cnt'] . "\n";
}

$count = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
echo "USERS COUNT: " . $count . "\n";
echo "--- USERS ---\n";
$stmt = $db->query('SELECT username, role, name, kelas_id, kelas_second FROM users ORDER BY username');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['username'] . '|' . $row['role'] . '|' . $row['name'] . '|' . ($row['kelas_id'] ?? '-') . '|' . ($row['kelas_second'] ?? '-') . "\n";
}
