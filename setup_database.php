<?php
require_once 'config/database.php';

echo "<h2>Setup Database untuk Role Operator dan Foto Profil</h2>";
echo "<hr>";

// 1. Tambah kolom foto ke tabel users
echo "<h3>1. Menambahkan kolom foto...</h3>";
$check_foto = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'foto'");
if (mysqli_num_rows($check_foto) == 0) {
    $sql_foto = "ALTER TABLE users ADD COLUMN foto VARCHAR(255) DEFAULT 'assets/img/default-avatar.jpg'";
    if (mysqli_query($conn, $sql_foto)) {
        echo "<p style='color: green;'>✓ Kolom foto berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal menambahkan kolom foto: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Kolom foto sudah ada.</p>";
}

// 2. Update ENUM untuk menambahkan role Operator
echo "<h3>2. Menambahkan role Operator...</h3>";
$check_role = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
$role_info = mysqli_fetch_assoc($check_role);
$current_enum = $role_info['Type'];

if (strpos($current_enum, 'Operator') === false) {
    $sql_role = "ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Dosen', 'Mahasiswa', 'Operator') DEFAULT 'Admin'";
    if (mysqli_query($conn, $sql_role)) {
        echo "<p style='color: green;'>✓ Role Operator berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal menambahkan role Operator: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Role Operator sudah ada.</p>";
}

// 3. Buat direktori untuk foto profil jika belum ada
echo "<h3>3. Membuat direktori untuk foto profil...</h3>";
$profile_dir = "assets/img/profile";
if (!file_exists($profile_dir)) {
    if (mkdir($profile_dir, 0777, true)) {
        echo "<p style='color: green;'>✓ Direktori $profile_dir berhasil dibuat.</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal membuat direktori $profile_dir.</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Direktori $profile_dir sudah ada.</p>";
}

echo "<hr>";
echo "<h3>Setup Selesai!</h3>";
echo "<p>Sekarang Anda bisa membuat user dengan role Operator melalui menu Pengguna.</p>";
echo "<a href='index.php' class='btn btn-success'>Kembali ke Dashboard</a>";
?>
