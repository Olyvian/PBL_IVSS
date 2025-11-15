<?php
// Cek status sesi dan mulai sesi hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$port = "5432";
$user = "postgres";
$password = " "; 
$dbname = " ";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;client_encoding=UTF8"; 

try {
    $koneksi = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
    ]);
    
} catch (PDOException $e) {
    die("Koneksi ke PostgreSQL gagal: " . $e->getMessage()); 
}
?>