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

$prodi_query = mysqli_query($conn, "SELECT * FROM program_studi ORDER BY nama_prodi ASC");

if (isset($_POST['simpan'])) {
    $npm = mysqli_real_escape_string($conn, $_POST['npm']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $id_prodi = $_POST['id_prodi'];
    $password = password_hash($npm, PASSWORD_BCRYPT); // Default password is NPM

    $query = "INSERT INTO mahasiswa (npm, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, tanggal_masuk, alamat, password, id_prodi) 
              VALUES ('$npm', '$nama', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$tanggal_masuk', '$alamat', '$password', '$id_prodi')";
    
    if (mysqli_query($conn, $query)) {
        log_activity($conn, "Tambah Mahasiswa", "Menambah mahasiswa baru dengan NPM: $npm");
        header("Location: mahasiswa.php?status=sukses");
        exit();
    } else {
        $error = "Gagal menambah data: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div id="content">
    <div class="container-fluid">
        <div class="mb-4">
            <h6 class="text-success fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Mahasiswa</h6>
            <h2 class="fw-bold">Tambah Mahasiswa Baru</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPM</label>
                            <input type="text" name="npm" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <select name="id_prodi" class="form-select" required>
                                <option value="">Pilih Program Studi</option>
                                <?php while($prodi = mysqli_fetch_assoc($prodi_query)): ?>
                                    <option value="<?php echo $prodi['id']; ?>"><?php echo $prodi['nama_prodi']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" name="simpan" class="btn btn-success px-4">Simpan Data</button>
                        <a href="mahasiswa.php" class="btn btn-light px-4 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
