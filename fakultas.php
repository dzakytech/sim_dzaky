<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Block POST operations for non-admin
if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Operator' && (isset($_POST['simpan']) || isset($_POST['update']) || isset($_GET['hapus']))) {
    header("Location: index.php");
    exit();
}

// Tambah Fakultas
if (isset($_POST['simpan'])) {
    $nama_fakultas = mysqli_real_escape_string($conn, $_POST['nama_fakultas']);
    mysqli_query($conn, "INSERT INTO fakultas (nama_fakultas) VALUES ('$nama_fakultas')");
    log_activity($conn, "Tambah Fakultas", "Menambah fakultas: $nama_fakultas");
    header("Location: fakultas.php?status=sukses");
    exit();
}

// Edit Fakultas
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_fakultas = mysqli_real_escape_string($conn, $_POST['nama_fakultas']);
    
    // Get old info
    $old_f = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_fakultas FROM fakultas WHERE id = '$id'"));
    $old_nama = $old_f['nama_fakultas'] ?? '';
    
    $desc = "Memperbarui fakultas: '$old_nama' -> '$nama_fakultas'";
    
    mysqli_query($conn, "UPDATE fakultas SET nama_fakultas = '$nama_fakultas' WHERE id = '$id'");
    log_activity($conn, "Edit Fakultas", $desc);
    header("Location: fakultas.php?status=update");
    exit();
}

// Hapus Fakultas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Get info before delete
    $f = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_fakultas FROM fakultas WHERE id = '$id'"));
    $nama = $f['nama_fakultas'] ?? 'Unknown';
    
    mysqli_query($conn, "DELETE FROM fakultas WHERE id = '$id'");
    log_activity($conn, "Hapus Fakultas", "Menghapus fakultas: $nama");
    header("Location: fakultas.php?status=hapus");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM fakultas ORDER BY id DESC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Data Akademik</h6>
                <h2 class="fw-bold">Data Fakultas</h2>
            </div>
            <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Fakultas
            </button>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Fakultas</th>
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
                                    <td><?php echo $row['nama_fakultas']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
                                        <button type="button" class="btn btn-sm btn-warning text-white btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-nama="<?php echo $row['nama_fakultas']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="fakultas.php?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
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
</div>

<?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Fakultas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fakultas</label>
                        <input type="text" name="nama_fakultas" class="form-control" required>
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
                <h5 class="modal-title fw-bold">Edit Fakultas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Fakultas</label>
                        <input type="text" name="nama_fakultas" id="edit-nama" class="form-control" required>
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
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('.btn-edit').click(function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        $('#edit-id').val(id);
        $('#edit-nama').val(nama);
    });
});
</script>
