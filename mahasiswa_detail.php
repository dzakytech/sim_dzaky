<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT m.*, p.nama_prodi, f.nama_fakultas FROM mahasiswa m 
                               LEFT JOIN program_studi p ON m.id_prodi = p.id 
                               LEFT JOIN fakultas f ON p.id_fakultas = f.id
                               WHERE m.id = '$id'");
$mhs = mysqli_fetch_assoc($query);

if (!$mhs) {
    header("Location: mahasiswa.php");
    exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Detail Mahasiswa</h6>
                <h2 class="fw-bold"><?php echo $mhs['nama']; ?></h2>
            </div>
            <a href="mahasiswa.php" class="btn btn-light border">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                        <i class="fas fa-user-graduate fa-5x text-success"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo $mhs['npm']; ?></h4>
                    <p class="text-muted mb-0"><?php echo $mhs['nama_prodi']; ?></p>
                    <p class="text-muted"><?php echo $mhs['nama_fakultas']; ?></p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Biodata Lengkap</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Nama Lengkap</th>
                                <td>: <?php echo $mhs['nama']; ?></td>
                            </tr>
                            <tr>
                                <th>NPM</th>
                                <td>: <?php echo $mhs['npm']; ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>: <?php echo $mhs['jenis_kelamin']; ?></td>
                            </tr>
                            <tr>
                                <th>Tempat, Tgl Lahir</th>
                                <td>: <?php echo $mhs['tempat_lahir'] . ', ' . date('d F Y', strtotime($mhs['tanggal_lahir'])); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td>: <?php echo date('d F Y', strtotime($mhs['tanggal_masuk'])); ?></td>
                            </tr>
                            <tr>
                                <th>Program Studi</th>
                                <td>: <?php echo $mhs['nama_prodi']; ?></td>
                            </tr>
                            <tr>
                                <th>Fakultas</th>
                                <td>: <?php echo $mhs['nama_fakultas']; ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>: <?php echo $mhs['alamat']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
