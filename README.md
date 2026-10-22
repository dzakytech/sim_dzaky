# Sistem Informasi Manajemen Mahasiswa - UMB

Sistem ini dibuat untuk mengelola data mahasiswa per program studi di Universitas Muhammadiyah Bengkulu.

## Fitur Utama
- **Dashboard Statis**: Ringkasan data total mahasiswa, prodi, dan fakultas.
- **Manajemen Mahasiswa**: CRUD (Tambah, Lihat, Edit, Hapus) data mahasiswa.
- **Manajemen Akademik**: CRUD Fakultas dan Program Studi.
- **Autentikasi**: Sistem login admin yang aman dengan hash password.
- **Antarmuka Modern**: Menggunakan Bootstrap 5 dengan desain sidebar yang responsif.

## Persiapan Lokal (XAMPP)
1. Pindahkan folder `sistemmahasiswa` ke dalam direktori `C:\xampp\htdocs\`.
2. Buka **XAMPP Control Panel** dan aktifkan **Apache** dan **MySQL**.
3. Buka browser dan akses `http://localhost/phpmyadmin/`.
4. Buat database baru dengan nama `db_sistemmahasiswa`.
5. Import file `database.sql` yang ada di folder proyek ke database tersebut.
6. Akses aplikasi melalui `http://localhost/sistemmahasiswa/`.
7. **Login Admin Default**:
   - Username: `admin`
   - Password: `admin123`

## Panduan Deployment ke InfinityFree
1. Daftar/Login ke [InfinityFree](https://infinityfree.com/).
2. Buat akun hosting baru dan buat database MySQL di panel control InfinityFree.
3. Catat detail database (MySQL Host, Username, Password, Database Name).
4. Buka file `config/database.php` dan ubah variabel koneksi sesuai data dari InfinityFree.
5. Upload semua file proyek ke folder `htdocs` di File Manager InfinityFree (atau via FTP).
6. Import `database.sql` melalui **phpMyAdmin** di panel control InfinityFree.

## Struktur Proyek
- `assets/`: File CSS, JS, dan Gambar.
- `config/`: Konfigurasi database.
- `includes/`: Komponen template (header, sidebar, footer).
- `modules/`: (Opsional) Logika tambahan.
- `database.sql`: Skema database MySQL.
