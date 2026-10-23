<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (isset($_SESSION['username'])) {
    log_activity($conn, "Logout", "Pengguna " . $_SESSION['username'] . " telah keluar dari sistem.");
}

session_destroy();
header("Location: landing.php");
exit();
?>
