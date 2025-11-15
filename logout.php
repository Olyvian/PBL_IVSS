<?php
include 'koneksi.php';

$_SESSION = array();
session_destroy();

header("Location: login.php");
exit;
?>