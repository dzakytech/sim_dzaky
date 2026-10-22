<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'Admin') {
    header("Location: index.php");
    exit();
}

$query = mysqli_query($conn, "SELECT a.*, u.username, u.nama_lengkap 
                               FROM audit_log a 
                               LEFT JOIN users u ON a.user_id = u.id 
                               ORDER BY a.created_at DESC LIMIT 100");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Sistem</h6>
            <h2 class="fw-bold">Audit Log</h2>
            <p class="text-muted small">Mencatat riwayat aktivitas pengguna di dalam sistem.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Keterangan</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td class="px-3 small text-muted"><?php echo date('d/m/Y H:i:s', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo $row['nama_lengkap'] ?? 'System'; ?></div>
                                    <small class="text-muted"><?php echo $row['username'] ?? '-'; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?php echo $row['action']; ?></span></td>
                                <td class="small"><?php echo $row['description']; ?></td>
                                <td class="small text-muted"><?php echo $row['ip_address']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($query) == 0): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada log aktivitas.</td>
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
