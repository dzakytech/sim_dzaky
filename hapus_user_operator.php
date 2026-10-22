<?php
require_once 'config/database.php';

// Hapus user dengan username 'operator'
$sql = "DELETE FROM users WHERE username = 'operator'";

if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo "<p style='color: green;'>✓ User dengan username 'operator' berhasil dihapus.</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Tidak ada user dengan username 'operator'.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Gagal menghapus user: " . mysqli_error($conn) . "</p>";
}

echo "<br><a href='pengguna.php' class='btn btn-success'>Kembali ke Pengguna</a>";
?>
