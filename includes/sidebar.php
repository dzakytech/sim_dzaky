<?php
require_once 'config/database.php';
?>
<div id="sidebar">
    <div class="sidebar-heading">
        <div class="d-flex align-items-center">
            <i class="fas fa-university me-2"></i>
            <div>
                <div style="font-size: 0.9rem;">SIM Mahasiswa</div>
                <div style="font-size: 0.6rem; font-weight: normal;">Universitas Muhammadiyah Bengkulu</div>
            </div>
        </div>
    </div>
    
    <div class="menu-section">Menu Utama</div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </nav>

    <div class="menu-section">Data Akademik</div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'mahasiswa.php' ? 'active' : ''; ?>" href="mahasiswa.php">
            <i class="fas fa-users"></i> Data Mahasiswa
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'universitas.php' ? 'active' : ''; ?>" href="universitas.php">
            <i class="fas fa-university"></i> Universitas
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'fakultas.php' ? 'active' : ''; ?>" href="fakultas.php">
            <i class="fas fa-building"></i> Fakultas
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'prodi.php' ? 'active' : ''; ?>" href="prodi.php">
            <i class="fas fa-book"></i> Program Studi
        </a>
        <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pengguna.php' ? 'active' : ''; ?>" href="pengguna.php">
            <i class="fas fa-user-cog"></i> Pengguna
        </a>
        <?php endif; ?>
    </nav>

    <div class="menu-section">Laporan</div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'rekap.php' ? 'active' : ''; ?>" href="rekap.php">
            <i class="fas fa-file-alt"></i> Rekap Mahasiswa
        </a>
    </nav>

    <?php if ($_SESSION['role'] == 'Admin'): ?>
    <div class="menu-section">Sistem</div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'audit_log.php' ? 'active' : ''; ?>" href="audit_log.php">
            <i class="fas fa-history"></i> Audit Log
        </a>
    </nav>
    <?php endif; ?>
</div>

<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container-fluid">
        <button class="btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle me-2 overflow-hidden" style="width: 40px; height: 40px;">
                        <img src="<?php 
                            $user_query = mysqli_query($conn, "SELECT foto FROM users WHERE id = '".$_SESSION['user_id']."'");
                            $user_data = mysqli_fetch_assoc($user_query);
                            echo (!empty($user_data['foto']) && file_exists($user_data['foto'])) ? $user_data['foto'] : 'assets/img/default-avatar.jpg'; 
                        ?>" 
                             alt="Profile" 
                             class="img-fluid" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.8rem;"><?php echo $_SESSION['nama_lengkap'] ?? 'Admin UMB'; ?></div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?php echo $_SESSION['role'] ?? 'Admin'; ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php"><i class="fas fa-user-edit me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
