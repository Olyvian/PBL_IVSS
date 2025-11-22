<?php
$host = 'localhost';
$dbname = 'Lab_IVIS';  
$user = 'postgres';       
$pass = '11223344';  

try {
    $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$pass";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi ke PostgreSQL gagal: " . $e->getMessage());
}
?>