<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Operator' && isset($_POST['update_univ'])) {
    header("Location: universitas.php");
    exit();
}

// Check if table exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'universitas'");
if (mysqli_num_rows($check_table) == 0) {
    die("Tabel 'universitas' belum ada. Silakan jalankan perintah SQL di database.sql terlebih dahulu.");
}

// Handle Update
if (isset($_POST['update_univ'])) {
    $nama_univ = mysqli_real_escape_string($conn, $_POST['nama_univ']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $akreditasi = mysqli_real_escape_string($conn, $_POST['akreditasi']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    
    // Fetch old data for logging
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM universitas WHERE id = 1"));
    
    $changes = [];
    if ($old['nama_univ'] != $nama_univ) $changes[] = "Nama: '".$old['nama_univ']."' -> '$nama_univ'";
    if ($old['alamat'] != $alamat) $changes[] = "Alamat: '".$old['alamat']."' -> '$alamat'";
    if ($old['akreditasi'] != $akreditasi) $changes[] = "Akreditasi: '".$old['akreditasi']."' -> '$akreditasi'";
    if ($old['website'] != $website) $changes[] = "Website: '".$old['website']."' -> '$website'";
    
    // Handle Logo Upload
    $logo_path = $old['logo'];
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "assets/img/";
        $file_extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
        $new_filename = "logo_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_path = $target_file;
                $changes[] = "Logo: [DIUBAH]";
                
                // Delete old logo if it's not the default and exists
                if ($old['logo'] != 'assets/img/logo.png' && file_exists($old['logo'])) {
                    unlink($old['logo']);
                }
            }
        }
    }

    $desc = "Memperbarui informasi universitas. Perubahan: " . (empty($changes) ? "Tidak ada perubahan." : implode(", ", $changes));

    $query = "UPDATE universitas SET 
              nama_univ = '$nama_univ', 
              alamat = '$alamat', 
              akreditasi = '$akreditasi', 
              website = '$website',
              logo = '$logo_path'
              WHERE id = 1";
              
    if (mysqli_query($conn, $query)) {
        log_activity($conn, "Edit Universitas", $desc);
        $status = "sukses";
    } else {
        $status = "gagal";
    }
}

// Fetch data
$query = mysqli_query($conn, "SELECT * FROM universitas WHERE id = 1");
$univ = mysqli_fetch_assoc($query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Sistem</h6>
                <h2 class="fw-bold">Informasi Universitas</h2>
            </div>
            <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Operator'): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEdit">
                <i class="fas fa-edit me-2"></i> Edit Informasi
            </button>
            <?php endif; ?>
        </div>

        <?php if (isset($status)): ?>
            <?php if ($status == 'sukses'): ?>
                <div class="alert alert-success alert-dismissible fade show">Informasi berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php else: ?>
                <div class="alert alert-danger alert-dismissible fade show">Gagal memperbarui informasi!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <img src="<?php echo (!empty($univ['logo']) && file_exists($univ['logo'])) ? $univ['logo'] : 'assets/img/logo.png'; ?>" alt="Logo UMB" class="img-fluid mb-3" style="max-height: 150px;">
                    </div>
                    <div class="col-md-9">
                        <h3 class="fw-bold"><?php echo $univ['nama_univ'] ?? 'Universitas Muhammadiyah Bengkulu'; ?></h3>
                        <p class="text-muted"><?php echo $univ['alamat'] ?? 'Jl. Bali, Teluk Segara, Kota Bengkulu, Bengkulu'; ?></p>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4 fw-bold">Akreditasi</div>
                            <div class="col-sm-8">: <?php echo $univ['akreditasi'] ?? 'B'; ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Website Resmi</div>
                            <div class="col-sm-8">: <a href="<?php echo $univ['website'] ?? 'https://umb.ac.id'; ?>" target="_blank"><?php echo str_replace(['https://', 'http://'], '', $univ['website'] ?? 'umb.ac.id'); ?></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Informasi Universitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Universitas</label>
                        <input type="text" name="nama_univ" class="form-control" value="<?php echo $univ['nama_univ'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" required><?php echo $univ['alamat'] ?? ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Akreditasi</label>
                            <input type="text" name="akreditasi" class="form-control" value="<?php echo $univ['akreditasi'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" value="<?php echo $univ['website'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Logo (Format: JPG, PNG, GIF)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti logo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_univ" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
