<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Operator') {
    header("Location: index.php");
    exit();
}

// Tambah Pengguna
if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Cek apakah username sudah ada
    $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username '$username' sudah digunakan. Silakan gunakan username lain.";
    } else {
        $query = "INSERT INTO users (username, nama_lengkap, password, role, status)
                  VALUES ('$username', '$nama_lengkap', '$password', '$role', '$status')";

        if (mysqli_query($conn, $query)) {
            log_activity($conn, "Tambah Pengguna", "Menambah pengguna baru: $username ($role)");
            header("Location: pengguna.php?status=sukses");
            exit();
        } else {
            $error = "Gagal menambah pengguna: " . mysqli_error($conn);
        }
    }
}

// Edit Pengguna
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Get old info
    $old_u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'"));
    
    $changes = [];
    if ($old_u['username'] != $username) $changes[] = "Username: '".$old_u['username']."' -> '$username'";
    if ($old_u['nama_lengkap'] != $nama_lengkap) $changes[] = "Nama: '".$old_u['nama_lengkap']."' -> '$nama_lengkap'";
    if ($old_u['role'] != $role) $changes[] = "Role: '".$old_u['role']."' -> '$role'";
    if ($old_u['status'] != $status) $changes[] = "Status: '".$old_u['status']."' -> '$status'";

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $query = "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap', 
                  password = '$password', role = '$role', status = '$status' WHERE id = '$id'";
        $changes[] = "Password: [DIUBAH]";
    } else {
        $query = "UPDATE users SET username = '$username', nama_lengkap = '$nama_lengkap', 
                  role = '$role', status = '$status' WHERE id = '$id'";
    }
    
    $desc = "Memperbarui pengguna: $username. Perubahan: " . (empty($changes) ? "Tidak ada perubahan." : implode(", ", $changes));

    if (mysqli_query($conn, $query)) {
        log_activity($conn, "Edit Pengguna", $desc);
        header("Location: pengguna.php?status=update");
        exit();
    }
}

// Hapus Pengguna
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Don't delete self
    if ($id != $_SESSION['user_id']) {
        // Get info before delete
        $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM users WHERE id = '$id'"));
        $username = $u['username'] ?? 'Unknown';
        
        mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
        log_activity($conn, "Hapus Pengguna", "Menghapus pengguna: $username");
        header("Location: pengguna.php?status=hapus");
    } else {
        header("Location: pengguna.php?status=error_self");
    }
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Sistem</h6>
                <h2 class="fw-bold">Manajemen Pengguna</h2>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Pengguna
            </button>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'sukses'): ?>
                <div class="alert alert-success alert-dismissible fade show">Data berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'update'): ?>
                <div class="alert alert-info alert-dismissible fade show">Data berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'hapus'): ?>
                <div class="alert alert-warning alert-dismissible fade show">Data berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($_GET['status'] == 'error_self'): ?>
                <div class="alert alert-danger alert-dismissible fade show">Anda tidak dapat menghapus akun sendiri!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = mysqli_fetch_assoc($query)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_lengkap']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><span class="badge bg-primary"><?php echo $row['role']; ?></span></td>
                                <td><span class="badge bg-<?php echo $row['status'] == 'Aktif' ? 'success' : 'danger'; ?>"><?php echo $row['status']; ?></span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning text-white btn-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEdit" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-username="<?php echo $row['username']; ?>"
                                            data-nama="<?php echo $row['nama_lengkap']; ?>"
                                            data-role="<?php echo $row['role']; ?>"
                                            data-status="<?php echo $row['status']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="pengguna.php?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="Admin">Admin</option>
                            <option value="Operator">Operator</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit-role" class="form-select" required>
                            <option value="Admin">Admin</option>
                            <option value="Operator">Operator</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit-status" class="form-select" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('.btn-edit').click(function() {
        const id = $(this).data('id');
        const username = $(this).data('username');
        const nama = $(this).data('nama');
        const role = $(this).data('role');
        const status = $(this).data('status');
        
        $('#edit-id').val(id);
        $('#edit-username').val(username);
        $('#edit-nama').val(nama);
        $('#edit-role').val(role);
        $('#edit-status').val(status);
    });
});
</script>
