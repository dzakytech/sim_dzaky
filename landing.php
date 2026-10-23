<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get statistics from database
$total_mhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mahasiswa"))['total'];
$total_prodi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM program_studi"))['total'];
$total_fakultas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM fakultas"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status = 'Aktif'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Manajemen Mahasiswa - Universitas Muhammadiyah Bengkulu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a4d2e;
            --secondary-color: #4f6f52;
            --accent-color: #a3b18a;
            --light-color: #dad7cd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 77, 46, 0.3);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .btn-hero {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin-right: 15px;
        }

        .btn-hero:hover {
            background: var(--light-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-hero-outline:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
            font-size: 1.1rem;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            height: 100%;
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: white;
            font-size: 2rem;
        }

        .feature-card h4 {
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* About Section */
        .about {
            padding: 100px 0;
            background: white;
        }

        .about-img {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .about-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .about-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-list {
            list-style: none;
            padding: 0;
        }

        .about-list li {
            padding: 10px 0;
            color: #666;
        }

        .about-list li i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        /* Stats Section */
        .stats {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
        }

        .stat-item i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 60px 0 30px;
        }

        footer h5 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        footer p {
            color: #999;
            line-height: 1.8;
        }

        footer a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 30px;
            text-align: center;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .btn-hero, .btn-hero-outline {
                padding: 12px 30px;
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .stat-item h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>SIM MAHASISWA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                </ul>
                <a href="login.php" class="btn btn-login ms-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <span class="hero-badge">
                            <i class="fas fa-star me-2"></i>Sistem Informasi Terintegrasi
                        </span>
                        <h1>Sistem Informasi Manajemen Mahasiswa</h1>
                        <p>Platform manajemen akademik modern untuk Universitas Muhammadiyah Bengkulu. Kelola data mahasiswa, program studi, fakultas, dan seluruh aktivitas akademik dengan mudah dan efisien.</p>
                        <div class="mt-4">
                            <a href="login.php" class="btn btn-hero">
                                <i class="fas fa-rocket me-2"></i>Mulai Sekarang
                            </a>
                            <a href="#features" class="btn btn-hero-outline">
                                <i class="fas fa-info-circle me-2"></i>Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="text-center">
                        <div style="background: rgba(255,255,255,0.1); padding: 60px; border-radius: 30px; backdrop-filter: blur(10px);">
                            <img src="assets/img/logo.png" alt="Logo Universitas Muhammadiyah Bengkulu" style="max-width: 100%; height: auto; max-height: 400px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Fitur Unggulan</h2>
                <p>Teknologi terkini untuk memudahkan manajemen akademik</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4>Manajemen Mahasiswa</h4>
                        <p>Kelola data mahasiswa secara lengkap mulai dari pendaftaran, biodata, hingga informasi akademik dengan sistem yang terintegrasi.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4>Manajemen Fakultas dan Program Studi</h4>
                        <p>Pengelolaan data fakultas dan program studi yang rapi dan sistematis untuk mendukung operasional akademik yang lebih baik.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h4>CRUD Data</h4>
                        <p>Sistem CRUD (Create, Read, Update, Delete) yang lengkap untuk pengelolaan data mahasiswa, fakultas, dan program studi dengan mudah dan cepat.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h4>Manajemen Pengguna</h4>
                        <p>Sistem pengelolaan hak akses pengguna dengan role-based access control untuk keamanan data yang optimal.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Laporan & Statistik</h4>
                        <p>Dashboard informatif dengan statistik real-time dan laporan komprehensif untuk keputusan strategis.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4>Audit Log</h4>
                        <p>Pencatatan aktivitas sistem secara detail untuk monitoring dan audit trail yang transparan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="about-img">
                        <img src="assets/img/logo.png" alt="Universitas Muhammadiyah Bengkulu" style="object-fit: contain; padding: 50px; background: #f8f9fa;">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3>Tentang Sistem</h3>
                        <p>Sistem Informasi Manajemen Mahasiswa (SIM Mahasiswa) adalah platform digital yang dirancang khusus untuk Universitas Muhammadiyah Bengkulu guna memudahkan pengelolaan data akademik dan administratif.</p>
                        <p>Sistem ini menyediakan solusi terintegrasi untuk seluruh kebutuhan manajemen mahasiswa, mulai dari pendaftaran, pengelolaan data, hingga pelaporan akademik yang akurat dan real-time.</p>
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> Sistem keamanan data yang terjamin</li>
                            <li><i class="fas fa-check-circle"></i> Antarmuka yang user-friendly dan mudah digunakan</li>
                            <li><i class="fas fa-check-circle"></i> Akses multi-role dengan pembagian hak akses yang jelas</li>
                            <li><i class="fas fa-check-circle"></i> Laporan dan statistik yang komprehensif</li>
                            <li><i class="fas fa-check-circle"></i> Audit trail untuk monitoring aktivitas sistem</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <i class="fas fa-user-graduate"></i>
                        <h3><?php echo $total_mhs; ?></h3>
                        <p>Mahasiswa Terdaftar</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <i class="fas fa-book"></i>
                        <h3><?php echo $total_prodi; ?></h3>
                        <p>Program Studi</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <i class="fas fa-building"></i>
                        <h3><?php echo $total_fakultas; ?></h3>
                        <p>Fakultas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <h3><?php echo $total_users; ?></h3>
                        <p>Pengguna Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>SIM Mahasiswa</h5>
                    <p>Sistem Informasi Manajemen Mahasiswa Universitas Muhammadiyah Bengkulu. Platform digital untuk pengelolaan data akademik yang terintegrasi dan modern.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#about">Tentang</a></li>
                        <li><a href="login.php">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Kontak</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i>Jl. Bali, Kp. Bali, Kec. Tlk. Segara, Kota Bengkulu, Bengkulu 38119</p>
                    <p><i class="fas fa-globe me-2"></i>umb.ac.id</p>
                    <p><i class="fab fa-whatsapp me-2"></i>0852-6666-7992</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Universitas Muhammadiyah Bengkulu. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        });
    </script>
</body>
</html>
