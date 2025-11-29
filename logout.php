
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/database.php';

$_SESSION = array();
session_destroy();

header("Location: index.php");
exit;
?>