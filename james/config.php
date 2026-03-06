<?php
// config.php
$host = '127.0.0.1';
$db   = 'zetech_library';
$user = 'root'; // Change to your production database user
$pass = '';     // Change to your production database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log this error instead of echoing it to the screen
    error_log($e->getMessage());
    exit('Database connection failed. Please contact the administrator.');
}
?>