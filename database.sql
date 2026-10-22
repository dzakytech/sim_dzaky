CREATE DATABASE IF NOT EXISTS db_sistemmahasiswa;
USE db_sistemmahasiswa;

CREATE TABLE fakultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_fakultas VARCHAR(100) NOT NULL
);

CREATE TABLE program_studi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_fakultas INT,
    nama_prodi VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_fakultas) REFERENCES fakultas(id) ON DELETE CASCADE
);

CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    npm VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    tanggal_masuk DATE,
    alamat TEXT,
    password VARCHAR(255) NOT NULL,
    id_prodi INT,
    FOREIGN KEY (id_prodi) REFERENCES program_studi(id) ON DELETE SET NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Dosen', 'Mahasiswa') DEFAULT 'Admin',
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif'
);

-- Insert sample data
INSERT INTO fakultas (nama_fakultas) VALUES ('Teknik'), ('Ekonomi dan Bisnis');
INSERT INTO program_studi (id_fakultas, nama_prodi) VALUES (1, 'Teknik Informatika'), (1, 'Sistem Informasi'), (2, 'Manajemen');

CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Default admin password is 'admin123' (hashed using BCRYPT)
INSERT INTO users (username, password, nama_lengkap, role, status) 
VALUES ('admin', '$2y$10$XRDNAOf2f3k7kQVVmoOTX.t4VjxSHROV01ozNj2LpbqfvfeDf8Gre', 'Admin SIM Mahasiswa UMB', 'Admin', 'Aktif');
