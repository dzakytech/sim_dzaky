<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT m.*, p.nama_prodi FROM mahasiswa m 
          LEFT JOIN program_studi p ON m.id_prodi = p.id 
          ORDER BY m.id DESC";
$result = mysqli_query($conn, $query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Data Akademik</h6>
                <h2 class="fw-bold">Data Mahasiswa</h2>
            </div>
            <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
            <a href="mahasiswa_tambah.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Tambah Mahasiswa
            </a>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'sukses'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Data berhasil disimpan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'hapus'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Data berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NPM</th>
                                <th>Nama Mahasiswa</th>
                                <th>L/P</th>
                                <th>Program Studi</th>
                                <th>Tanggal Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo $row['npm']; ?></td>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo ($row['jenis_kelamin'] == 'Laki-laki' ? 'L' : 'P'); ?></td>
                                <td><?php echo $row['nama_prodi'] ?? '-'; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="mahasiswa_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-info text-white" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
                                        <a href="mahasiswa_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="mahasiswa_hapus.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada data mahasiswa.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
