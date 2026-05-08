<?php
// config/database.php
$host = '127.0.0.1';
$db   = 'artflow_db';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If database doesn't exist, try connecting without dbname
    if ($e->getCode() == 1049) {
        try {
            $pdo_init = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            // We can attempt to run database.sql if needed, but for now we expect the user to import it.
            // Let's create the DB just in case, but tables might be missing.
            $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db`");
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e_init) {
            die("Database connection failed: " . $e_init->getMessage());
        }
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}
