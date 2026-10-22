<?php
session_start();
require_once 'config/database.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Operator') {
    header("Location: mahasiswa.php");
    exit();
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id = '$id'");
$mhs = mysqli_fetch_assoc($query);

if (!$mhs) {
    header("Location: mahasiswa.php");
    exit();
}

$prodi_query = mysqli_query($conn, "SELECT * FROM program_studi ORDER BY nama_prodi ASC");

if (isset($_POST['update'])) {
    $npm = mysqli_real_escape_string($conn, $_POST['npm']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $id_prodi = $_POST['id_prodi'];

    // Construct detailed changes description
    $changes = [];
    if ($mhs['npm'] != $npm) $changes[] = "NPM: '" . $mhs['npm'] . "' -> '$npm'";
    if ($mhs['nama'] != $nama) $changes[] = "Nama: '" . $mhs['nama'] . "' -> '$nama'";
    if ($mhs['jenis_kelamin'] != $jenis_kelamin) $changes[] = "JK: '" . $mhs['jenis_kelamin'] . "' -> '$jenis_kelamin'";
    if ($mhs['tempat_lahir'] != $tempat_lahir) $changes[] = "Tempat Lahir: '" . $mhs['tempat_lahir'] . "' -> '$tempat_lahir'";
    if ($mhs['tanggal_lahir'] != $tanggal_lahir) $changes[] = "Tgl Lahir: '" . $mhs['tanggal_lahir'] . "' -> '$tanggal_lahir'";
    if ($mhs['tanggal_masuk'] != $tanggal_masuk) $changes[] = "Tgl Masuk: '" . $mhs['tanggal_masuk'] . "' -> '$tanggal_masuk'";
    if ($mhs['alamat'] != $alamat) $changes[] = "Alamat: '" . $mhs['alamat'] . "' -> '$alamat'";
    if ($mhs['id_prodi'] != $id_prodi) {
        $old_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_prodi FROM program_studi WHERE id = '".$mhs['id_prodi']."'"));
        $new_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_prodi FROM program_studi WHERE id = '$id_prodi'"));
        $changes[] = "Prodi: '" . ($old_p['nama_prodi'] ?? '-') . "' -> '" . ($new_p['nama_prodi'] ?? '-') . "'";
    }

    $desc = "Memperbarui data mahasiswa NPM: $npm. Perubahan: " . (empty($changes) ? "Tidak ada perubahan data." : implode(", ", $changes));

    $query = "UPDATE mahasiswa SET 
              npm = '$npm', 
              nama = '$nama', 
              jenis_kelamin = '$jenis_kelamin', 
              tempat_lahir = '$tempat_lahir', 
              tanggal_lahir = '$tanggal_lahir', 
              tanggal_masuk = '$tanggal_masuk', 
              alamat = '$alamat', 
              id_prodi = '$id_prodi' 
              WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        log_activity($conn, "Edit Mahasiswa", $desc);
        header("Location: mahasiswa.php?status=sukses");
        exit();
    } else {
        $error = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Mahasiswa</h6>
            <h2 class="fw-bold">Edit Data Mahasiswa</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPM</label>
                            <input type="text" name="npm" class="form-control" value="<?php echo $mhs['npm']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $mhs['nama']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki" <?php echo ($mhs['jenis_kelamin'] == 'Laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo ($mhs['jenis_kelamin'] == 'Perempuan' ? 'selected' : ''); ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <select name="id_prodi" class="form-select" required>
                                <?php while($prodi = mysqli_fetch_assoc($prodi_query)): ?>
                                    <option value="<?php echo $prodi['id']; ?>" <?php echo ($mhs['id_prodi'] == $prodi['id'] ? 'selected' : ''); ?>>
                                        <?php echo $prodi['nama_prodi']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="<?php echo $mhs['tempat_lahir']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $mhs['tanggal_lahir']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="<?php echo $mhs['tanggal_masuk']; ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?php echo $mhs['alamat']; ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="update" class="btn btn-success px-4">Update Data</button>
                        <a href="mahasiswa.php" class="btn btn-light px-4 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
