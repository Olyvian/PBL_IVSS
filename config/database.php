<?php
$host = 'localhost';
$dbname = 'ivss';  // Ganti sesuai nama DB PostgreSQL kamu
$user = 'postgres';       // Default user PostgreSQL
$pass = 'postgre';  // Sesuaikan dengan password PostgreSQL

try {
    // DSN untuk PostgreSQL
    $dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$pass;port=5444";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi ke PostgreSQL gagal: " . $e->getMessage());
}
?>