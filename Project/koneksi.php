<?php
// Cek status sesi dan mulai sesi hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$port = "5432";
$user = "postgres";
$password = " "; //isi sesuai password Database
$dbname = "pbl";

// String koneksi ke database pbl
$connStr = "host=$host port=$port dbname=$dbname user=$user password=$password options='--client_encoding=UTF8'";
$koneksi = pg_connect($connStr);

if (!$koneksi) {
    die("Koneksi ke PostgreSQL gagal: " . pg_last_error());
}
?>