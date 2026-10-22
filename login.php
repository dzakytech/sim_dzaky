<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username' AND status = 'Aktif'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            log_activity($conn, "Login", "Pengguna $username berhasil login.");

            header("Location: index.php");
            exit();
        } else {
            log_activity($conn, "Login Gagal", "Percobaan login gagal untuk username: $username (Password salah)");
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan atau akun nonaktif!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Mahasiswa UMB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a4d2e 0%, #4f6f52 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: white;
        }
        .btn-success {
            background-color: #1a4d2e;
            border-color: #1a4d2e;
        }
        .btn-success:hover {
            background-color: #143d24;
        }
    </style>
</head>
<body>
    <div class="login-card text-center">
        <h3 class="fw-bold mb-4 text-success">SIM MAHASISWA</h3>
        <p class="text-muted mb-4">Universitas Muhammadiyah Bengkulu</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="btn btn-success w-100 py-2">Masuk</button>
        </form>
        <div class="mt-4 text-muted small">
            &copy; 2026 Universitas Muhammadiyah Bengkulu
        </div>
    </div>
</body>
</html>
