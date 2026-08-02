<?php
// Database configuration credentials
$host = '127.0.0.1;port=3308';
$db   = 'sac_historic_homes'; // Check your phpMyAdmin list to make sure this matches your DB name
$user = 'root';
$pass = ''; // WampServer uses a completely blank password for 'root' by default
$chrs = 'utf8mb4';

// Secure Data Source Name string
$dsn  = "mysql:host=$host;dbname=$db;charset=$chrs";

// Security options to ensure strict error handling and clean data types
$opts = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Attempt the handshake
    $pdo = new PDO($dsn, $user, $pass, $opts);
    
    // Temporarily uncomment the line below to test the connection in your browser!
    //echo "Database connection successful!";
} catch (\PDOException $e) {
    // Catch any connection failures and display the reason cleanly
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}