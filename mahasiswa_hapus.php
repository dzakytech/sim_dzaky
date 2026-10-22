<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Operator') {
    header("Location: mahasiswa.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get info before delete for logging
    $mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT npm FROM mahasiswa WHERE id = '$id'"));
    $npm = $mhs['npm'] ?? 'Unknown';

    $query = "DELETE FROM mahasiswa WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        log_activity($conn, "Hapus Mahasiswa", "Menghapus mahasiswa dengan NPM: $npm");
        header("Location: mahasiswa.php?status=hapus");
        exit();
    } else {
        die("Gagal menghapus data: " . mysqli_error($conn));
    }
} else {
    header("Location: mahasiswa.php");
    exit();
}
?>
