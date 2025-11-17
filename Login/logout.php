<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'pdo.php';

$_SESSION = array();
session_destroy();

header("Location: login.php");
exit;
?>