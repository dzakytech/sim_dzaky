<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}

// Get statistics
$total_mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mahasiswa"))['total'];
$total_prodi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM program_studi"))['total'];
$total_fakultas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM fakultas"))['total'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Dashboard</h6>
            <h2 class="fw-bold">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></h2>
            <p class="text-muted"> Pada Sistem Informasi Manajemen Mahasiswa Universitas Muhammadiyah Bengkulu</p>
        </div>

        <div class="row g-4 mb-4">
            <!-- Total Mahasiswa -->
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-3 me-3 text-success">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Mahasiswa</div>
                            <div class="h3 fw-bold mb-0"><?php echo $total_mhs; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program Studi -->
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-3 me-3 text-success">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Program Studi</div>
                            <div class="h3 fw-bold mb-0"><?php echo $total_prodi; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fakultas -->
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-3 me-3 text-success">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Fakultas</div>
                            <div class="h3 fw-bold mb-0"><?php echo $total_fakultas; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hak Akses -->
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-3 me-3 text-success">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Hak Akses</div>
                            <div class="h4 fw-bold mb-0"><?php echo $_SESSION['role']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Universitas</label>
                        <span class="fw-bold">Universitas Muhammadiyah Bengkulu</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Role</label>
                        <span class="fw-bold"><?php echo $_SESSION['role']; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Username</label>
                        <span class="fw-bold"><?php echo $_SESSION['username']; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">Status</label>
                        <span class="badge bg-success">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
