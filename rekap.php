<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get filter values
$id_fakultas = $_GET['fakultas'] ?? '';
$id_prodi = $_GET['prodi'] ?? '';
$status_mhs = $_GET['status'] ?? '';
$jk = $_GET['jk'] ?? '';
$search = $_GET['search'] ?? '';

// Base query
$query_str = "SELECT m.*, p.nama_prodi, f.nama_fakultas, u.status as user_status 
              FROM mahasiswa m 
              LEFT JOIN program_studi p ON m.id_prodi = p.id 
              LEFT JOIN fakultas f ON p.id_fakultas = f.id
              LEFT JOIN users u ON m.npm = u.username
              WHERE 1=1";

if ($id_fakultas) $query_str .= " AND f.id = '$id_fakultas'";
if ($id_prodi) $query_str .= " AND p.id = '$id_prodi'";
if ($jk) $query_str .= " AND m.jenis_kelamin = '$jk'";
if ($search) $query_str .= " AND (m.nama LIKE '%$search%' OR m.npm LIKE '%$search%')";

$result = mysqli_query($conn, $query_str);

// Stats
$total_mhs = mysqli_num_rows($result);
$laki_laki = 0;
$perempuan = 0;
$aktif = 0;

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
    if ($row['jenis_kelamin'] == 'Laki-laki') $laki_laki++;
    if ($row['jenis_kelamin'] == 'Perempuan') $perempuan++;
    // Assuming active if there's a user account that is active, or just all for now
    $aktif++; 
}

$fakultas_list = mysqli_query($conn, "SELECT * FROM fakultas");
$prodi_list = mysqli_query($conn, "SELECT * FROM program_studi");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Laporan Akademik</h6>
            <h2 class="fw-bold">Rekap Laporan Mahasiswa</h2>
            <p class="text-muted small">Rekap data mahasiswa berdasarkan ruang lingkup akses pengguna.</p>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Filter Laporan</h6>
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Fakultas</label>
                        <select name="fakultas" class="form-select form-select-sm">
                            <option value="">-- Semua Fakultas --</option>
                            <?php while($f = mysqli_fetch_assoc($fakultas_list)): ?>
                                <option value="<?php echo $f['id']; ?>" <?php echo $id_fakultas == $f['id'] ? 'selected' : ''; ?>>
                                    <?php echo $f['nama_fakultas']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Program Studi</label>
                        <select name="prodi" class="form-select form-select-sm">
                            <option value="">-- Semua Program Studi --</option>
                            <?php while($p = mysqli_fetch_assoc($prodi_list)): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo $id_prodi == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo $p['nama_prodi']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status Mahasiswa</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" <?php echo $status_mhs == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Nonaktif" <?php echo $status_mhs == 'Nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Jenis Kelamin</label>
                        <select name="jk" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            <option value="Laki-laki" <?php echo $jk == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo $jk == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Cari NPM / Nama</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik NPM atau nama mahasiswa" value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-success btn-sm px-4">Tampilkan Laporan</button>
                        <a href="rekap.php" class="btn btn-light btn-sm border px-4">Reset</a>
                        <button type="button" class="btn btn-outline-success btn-sm px-4" onclick="window.print()">Cetak</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 p-3">
                    <div class="text-muted small">Total Mahasiswa</div>
                    <div class="h3 fw-bold mb-0"><?php echo $total_mhs; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 p-3">
                    <div class="text-muted small">Laki-laki</div>
                    <div class="h3 fw-bold mb-0"><?php echo $laki_laki; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 p-3">
                    <div class="text-muted small">Perempuan</div>
                    <div class="h3 fw-bold mb-0"><?php echo $perempuan; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 p-3">
                    <div class="text-muted small">Mahasiswa Aktif</div>
                    <div class="h3 fw-bold mb-0"><?php echo $aktif; ?></div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Daftar Mahasiswa</h6>
                <small class="text-muted">Seluruh Data Mahasiswa - <?php echo date('d-m-Y H:i'); ?></small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">No</th>
                                <th>NPM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Program Studi</th>
                                <th>Fakultas</th>
                                <th>JK</th>
                                <th>Tanggal Masuk</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($data as $row): 
                            ?>
                            <tr>
                                <td class="px-3"><?php echo $no++; ?></td>
                                <td><?php echo $row['npm']; ?></td>
                                <td class="fw-bold"><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['nama_prodi']; ?></td>
                                <td><?php echo $row['nama_fakultas']; ?></td>
                                <td><?php echo $row['jenis_kelamin'] == 'Laki-laki' ? 'L' : 'P'; ?></td>
                                <td><?php echo $row['tanggal_masuk']; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-success border small" style="font-size: 0.7rem;">Aktif</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Tidak ada data ditemukan dengan filter tersebut.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    #sidebar, .navbar, .btn, .card-body form, .menu-section {
        display: none !important;
    }
    #content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
