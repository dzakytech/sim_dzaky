<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

$message = '';
$message_type = '';

if (isset($_POST['update_profile'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $foto_path = $user['foto'];

    $changes = [];
    if ($user['nama_lengkap'] != $nama_lengkap) $changes[] = "Nama: '".$user['nama_lengkap']."' -> '$nama_lengkap'";
    if ($user['username'] != $username) $changes[] = "Username: '".$user['username']."' -> '$username'";

    // Handle Photo Upload
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "assets/img/profile/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = "profile_" . $id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            $check = getimagesize($_FILES["foto"]["tmp_name"]);
            if($check !== false) {
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    // Delete old photo if it's not the default
                    if ($user['foto'] != 'assets/img/default-avatar.png' && file_exists($user['foto'])) {
                        unlink($user['foto']);
                    }
                    $foto_path = $target_file;
                    $changes[] = "Foto: [DIUBAH]";
                }
            }
        }
    }

    $desc = "Memperbarui profil sendiri. Perubahan: " . (empty($changes) ? "Tidak ada perubahan." : implode(", ", $changes));

    $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username', foto = '$foto_path' WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['username'] = $username;
        log_activity($conn, "Update Profil", $desc);
        $message = "Profil berhasil diperbarui!";
        $message_type = "success";
        // Refresh user data for password update logic
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'"));
    }
}

if (isset($_POST['update_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    
    if (password_verify($old_pass, $user['password'])) {
        if ($new_pass === $confirm_pass) {
            $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            mysqli_query($conn, "UPDATE users SET password = '$hashed_pass' WHERE id = '$id'");
            log_activity($conn, "Ganti Password", "Mengganti password akun sendiri. Status: Sukses");
            $message = "Password berhasil diubah!";
            $message_type = "success";
        } else {
            log_activity($conn, "Ganti Password Gagal", "Percobaan ganti password gagal (Konfirmasi tidak cocok)");
            $message = "Konfirmasi password tidak cocok!";
            $message_type = "danger";
        }
    } else {
        log_activity($conn, "Ganti Password Gagal", "Percobaan ganti password gagal (Password lama salah)");
        $message = "Password lama salah!";
        $message_type = "danger";
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Pengguna</h6>
            <h2 class="fw-bold">Profil Saya</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4 border-0 shadow-sm">
                    <div class="mx-auto mb-3" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%;">
                        <img src="<?php echo (!empty($user['foto']) && file_exists($user['foto'])) ? $user['foto'] : 'assets/img/default-avatar.JPG'; ?>" 
                             alt="Profile Photo" 
                             class="img-fluid" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 class="fw-bold"><?php echo $user['nama_lengkap']; ?></h4>
                    <span class="badge bg-success mb-2"><?php echo $user['role']; ?></span>
                    <p class="text-muted small">Terdaftar sejak <?php echo date('d M Y', strtotime($user['id'] > 0 ? 'now' : 'now')); ?></p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Edit Informasi Profil</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $user['nama_lengkap']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Kosongkan jika tidak ingin mengganti foto.</small>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-success px-4">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Ubah Password</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="old_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-outline-success px-4">Ganti Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
