<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Pengumuman - Laboratorium IVSS</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header class="header-institusi">
        <div class="logo-container">
            <img src="logo_polinema.png" alt="Logo POLINEMA">
            <div class="text-identitas">
                <h3>Intelligent Vision and Intelligent Systems</h3>
                <h2>POLITEKNIK NEGERI MALANG</h2>
            </div>
        </div>

        <div class="logout-container">
            <span class="user-greeting">Haloo, <?php echo $username; ?></span> 
            <a class="btn btn-sm btn-danger" href="logout.php">Logout</a>
        </div>
        
        <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button> 
    </header>

    <nav class="navbar" id="main-navbar">
        <ul>
            <li><a href="beranda.php">Beranda</a></li>
            </li>
            <li class="beranda.php"><a href="#">Kemahasiswaan</a></li>
            <li class="beranda.php"><a href="#">Penelitian dan Pengabdian</a></li>
            <li><a href="berita-pengumuman.php">Berita & Pengumuman</a></li>
        </ul>
    </nav>
    
    <section class="hero-section" style="height: 300px;">
        <h1>BERITA & PENGUMUMAN</h1>
    </section>

    <main class="main-content">
        <div class="content-area" style="max-width: 100%;">
            <h3 style="border-bottom: none; margin-bottom: 0;">Update Terbaru dari Laboratorium IVSS</h3>
            <p style="text-align: center; margin-bottom: 30px;">
                Informasi terkini mengenai kegiatan, proyek penelitian, dan pengumuman penting untuk mahasiswa dan dosen di Laboratorium IVSS.
            </p>
            
            <section class="news-list-container">
                
                <div class="featured-news-card">
                    <img src="featured-image.jpg" alt="Featured News Image Placeholder">
                    <div class="featured-news-content">
                        <h4>Pengumuman Pendaftaran Asisten Laboratorium Periode 2024/2025</h4>
                        <span class="date-meta">15 November 2025 | Pengumuman Resmi</span>
                        <p>
                            Pendaftaran asisten Lab IVSS telah dibuka untuk mahasiswa yang antusias dan berdedikasi. Posisi ini memberikan kesempatan untuk terlibat langsung dalam proyek penelitian AI dan Visi Komputer, serta mendapatkan pengalaman praktis dalam pengelolaan laboratorium. Persyaratan pendaftaran dan jadwal seleksi dapat dilihat pada tautan di bawah ini.
                        </p>
                        <a href="#">Baca Selengkapnya &rarr;</a>
                    </div>
                </div>
                
                <h3 class="section-title" style="margin-top: 50px;">Berita Lainnya</h3>
                
                <div class="news-grid">
                    
                    <article class="news-item-grid">
                        <img src="news-image-1.jpg" alt="News Image Placeholder 1">
                        <div class="news-item-content">
                            <h4>Riset Kolaborasi IVSS dan Industri X: Pengembangan Sistem Kendali Kualitas</h4>
                            <span class="date-meta">01 November 2025 | Penelitian</span>
                            <p>Lab IVSS memulai proyek penelitian baru dengan mitra industri untuk mengembangkan solusi otomatisasi pada lini produksi menggunakan Computer Vision dan Deep Learning.</p>
                            <a href="#">Baca Selengkapnya &rarr;</a>
                        </div>
                    </article>

                    <article class="news-item-grid">
                        <img src="news-image-2.jpg" alt="News Image Placeholder 2">
                        <div class="news-item-content">
                            <h4>Pelatihan Dasar Deep Learning untuk Mahasiswa Semester Akhir</h4>
                            <span class="date-meta">25 Oktober 2025 | Kegiatan Mahasiswa</span>
                            <p>Pelatihan intensif ini mencakup arsitektur Convolutional Neural Network (CNN) dan implementasinya menggunakan framework TensorFlow/PyTorch. Segera daftarkan diri Anda!</p>
                            <a href="#">Baca Selengkapnya &rarr;</a>
                        </div>
                    </article>
                    
                    <article class="news-item-grid">
                        <img src="news-image-3.jpg" alt="News Image Placeholder 3">
                        <div class="news-item-content">
                            <h4>Seminar Teknologi AI Terapan di Lingkungan Politeknik</h4>
                            <span class="date-meta">10 Oktober 2025 | Event</span>
                            <p>Dihadiri oleh pakar dari berbagai universitas, seminar ini membahas masa depan implementasi Artificial Intelligence di pendidikan vokasi.</p>
                            <a href="#">Baca Selengkapnya &rarr;</a>
                        </div>
                    </article>
                    
                    <article class="news-item-grid">
                        <img src="news-image-4.jpg" alt="News Image Placeholder 4">
                        <div class="news-item-content">
                            <h4>Kunjungan Industri ke Perusahaan Teknologi Y</h4>
                            <span class="date-meta">05 Oktober 2025 | Kunjungan</span>
                            <p>Kegiatan kunjungan industri rutin untuk memberikan wawasan langsung kepada mahasiswa mengenai aplikasi nyata dari Intelligent Systems.</p>
                            <a href="#">Baca Selengkapnya &rarr;</a>
                        </div>
                    </article>
                    
                </div>
            </section>
        </div>
    </main>

    <footer class="footer-polinema">
        <div class="footer-top">
            <div class="footer-identitas">
                <div class="logo-container-footer">
                    <img src="logo_polinema.png" alt="Logo POLINEMA">
                    <div class="text-identitas-footer">
                        <h3>JURUSAN TEKNOLOGI INFORMASI</h3>
                        <h2>POLITEKNIK NEGERI MALANG</h2>
                    </div>
                </div>
                <div class="alamat-info">
                    <p>BLU POLITEKNIK NEGERI MALANG</p>
                    <p>Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
                </div>
            </div>
            
            <div class="social-media">
                <a href="#"><img src="yt.png" alt="YouTube Icon"></a>
                <a href="#"><img src="ig.jpeg" alt="Instagram Icon"></a>
            </div>
        </div>
        
        <div class="footer-bottom-menu">
            <div class="menu-group">
                <h4>Tentang JTI</h4>
                <ul>
                    <li><a href="#">Sejarah</a></li>
                    <li><a href="#">Visi, Misi & Tujuan</a></li>
                    <li><a href="#">Struktur Organisasi</a></li>
                    <li><a href="#">Tenaga Pengajar</a></li>
                    <li><a href="#">Tenaga Kependidikan</a></li>
                    <li><a href="#">Sarana & Prasarana</a></li>
                </ul>
            </div>
            <div class="menu-group">
                <h4>Website Polinema Lainnya</h4>
                <ul>
                    <li><a href="http://www.polinema.ac.id" target="_blank">Polinema.ac.id</a></li>
                </ul>
            </div>
        </div>
    </footer>
    <script src="script.js"></script> 
</body>
</html>