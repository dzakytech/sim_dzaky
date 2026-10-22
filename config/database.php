<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_sistemmahasiswa";

// For InfinityFree, you will change these values later
// $host = "sqlxxx.infinityfree.com";
// $user = "if0_xxxxxx";
// $pass = "your_password";
// $db   = "if0_xxxxxx_db_name";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
