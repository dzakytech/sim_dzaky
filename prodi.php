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

// Tambah Prodi
if (isset($_POST['simpan'])) {
    $nama_prodi = mysqli_real_escape_string($conn, $_POST['nama_prodi']);
    $id_fakultas = $_POST['id_fakultas'];
    mysqli_query($conn, "INSERT INTO program_studi (nama_prodi, id_fakultas) VALUES ('$nama_prodi', '$id_fakultas')");
    log_activity($conn, "Tambah Prodi", "Menambah program studi: $nama_prodi");
    header("Location: prodi.php?status=sukses");
    exit();
}

// Edit Prodi
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_prodi = mysqli_real_escape_string($conn, $_POST['nama_prodi']);
    $id_fakultas = $_POST['id_fakultas'];
    
    // Get old info
    $old_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.*, f.nama_fakultas FROM program_studi p JOIN fakultas f ON p.id_fakultas = f.id WHERE p.id = '$id'"));
    $new_f = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_fakultas FROM fakultas WHERE id = '$id_fakultas'"));
    
    $changes = [];
    if ($old_p['nama_prodi'] != $nama_prodi) $changes[] = "Nama: '".$old_p['nama_prodi']."' -> '$nama_prodi'";
    if ($old_p['id_fakultas'] != $id_fakultas) $changes[] = "Fakultas: '".$old_p['nama_fakultas']."' -> '".$new_f['nama_fakultas']."'";
    
    $desc = "Memperbarui prodi: $nama_prodi. Perubahan: " . (empty($changes) ? "Tidak ada perubahan." : implode(", ", $changes));
    
    mysqli_query($conn, "UPDATE program_studi SET nama_prodi = '$nama_prodi', id_fakultas = '$id_fakultas' WHERE id = '$id'");
    log_activity($conn, "Edit Prodi", $desc);
    header("Location: prodi.php?status=update");
    exit();
}

// Hapus Prodi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Get info before delete
    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_prodi FROM program_studi WHERE id = '$id'"));
    $nama = $p['nama_prodi'] ?? 'Unknown';

    mysqli_query($conn, "DELETE FROM program_studi WHERE id = '$id'");
    log_activity($conn, "Hapus Prodi", "Menghapus program studi: $nama");
    header("Location: prodi.php?status=hapus");
    exit();
}

$query = mysqli_query($conn, "SELECT p.*, f.nama_fakultas FROM program_studi p 
                               JOIN fakultas f ON p.id_fakultas = f.id 
                               ORDER BY p.id DESC");
$fakultas_query = mysqli_query($conn, "SELECT * FROM fakultas ORDER BY nama_fakultas ASC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Data Akademik</h6>
                <h2 class="fw-bold">Data Program Studi</h2>
            </div>
            <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Prodi
            </button>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Program Studi</th>
                                    <th>Fakultas</th>
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
                                    <td><?php echo $row['nama_prodi']; ?></td>
                                    <td><?php echo $row['nama_fakultas']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
                                        <button type="button" class="btn btn-sm btn-warning text-white btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-nama="<?php echo $row['nama_prodi']; ?>"
                                                data-fakultas="<?php echo $row['id_fakultas']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="prodi.php?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
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
                <h5 class="modal-title fw-bold">Tambah Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Program Studi</label>
                        <input type="text" name="nama_prodi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultas</label>
                        <select name="id_fakultas" class="form-select" required>
                            <option value="">Pilih Fakultas</option>
                            <?php while($f = mysqli_fetch_assoc($fakultas_query)): ?>
                                <option value="<?php echo $f['id']; ?>"><?php echo $f['nama_fakultas']; ?></option>
                            <?php endwhile; ?>
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
                <h5 class="modal-title fw-bold">Edit Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Program Studi</label>
                        <input type="text" name="nama_prodi" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultas</label>
                        <select name="id_fakultas" id="edit-fakultas" class="form-select" required>
                            <option value="">Pilih Fakultas</option>
                            <?php 
                            mysqli_data_seek($fakultas_query, 0);
                            while($f = mysqli_fetch_assoc($fakultas_query)): 
                            ?>
                                <option value="<?php echo $f['id']; ?>"><?php echo $f['nama_fakultas']; ?></option>
                            <?php endwhile; ?>
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
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('.btn-edit').click(function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const fakultas = $(this).data('fakultas');
        $('#edit-id').val(id);
        $('#edit-nama').val(nama);
        $('#edit-fakultas').val(fakultas);
    });
});
</script>
