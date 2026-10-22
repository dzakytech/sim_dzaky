<?php
require_once 'config/database.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_BCRYPT);

$query = "UPDATE users SET password = '$password' WHERE username = '$username'";

if (mysqli_query($conn, $query)) {
    echo "<h3>Sukses! Password admin telah diperbarui menjadi 'admin123'.</h3>";
    echo "<p>Silakan hapus file ini (fix_admin.php) demi keamanan.</p>";
    echo "<a href='login.php'>Kembali ke Login</a>";
} else {
    echo "Gagal memperbarui password: " . mysqli_error($conn);
}
?>
