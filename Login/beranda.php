<?php
// Pastikan session_start() dipanggil di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// !!! LOGIC REDIRECT DIHAPUS !!!
// Pengguna sekarang dapat mengakses halaman ini tanpa login.

// Logic untuk pesan sukses (tetap diperlukan jika login berhasil)
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

// Variabel $username dipindahkan ke dalam blok HTML if-statement
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium IVSS - POLINEMA</title>
    <link rel="stylesheet" href="style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* --- Bagian CSS yang dimodifikasi untuk Navbar --- */
        
        /* Modifikasi untuk menempatkan Logout di ujung kanan atas header-institusi */
        .header-institusi {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-right: 20px; 
        }
        .logout-container {
            display: flex;
            align-items: center;
        }
        .user-greeting {
            color: #fff; /* Pastikan teks sapaan tetap terlihat di header gelap */
            margin-right: 15px;
            font-size: 0.9rem;
        }

        /* Navbar Centering */
        .navbar {
            display: flex;
            justify-content: center; /* Mengatur navbar container ke tengah */
            background-color: #0d364a; /* Ganti dengan warna navbar Anda (misalnya: dark blue) */
        }
        .navbar ul {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0; 
            list-style: none; /* Menghapus bullet point */
            margin: 0;
        }
        .navbar li {
            padding: 0 15px; /* Jarak antar menu */
            position: relative;
        }
        
        /* Style Link Navbar (Beranda & lainnya) - Default */
        .navbar li a {
            color: #fff; /* Warna teks putih */
            text-decoration: none; /* Menghapus garis bawah default */
            padding: 15px 0;
            display: block; 
            transition: border-bottom 0.2s;
        }

        /* Efek Hover (Garis Bawah) */
        .navbar li a:hover {
            border-bottom: 2px solid #fff; /* Garis bawah putih saat hover */
        }

        /* Dropdown Styling */
        .navbar li.has-dropdown > a {
            padding-right: 20px; /* Ruang untuk ikon panah */
            /* Anda mungkin perlu menambahkan ikon panah di style.css atau dengan pseudo-element */
        }

        /* --- END Bagian CSS yang dimodifikasi untuk Navbar --- */
        
        /* CSS tambahan lainnya (tetap dari versi sebelumnya) */
        .dashboard-info {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .main-content {
            padding-top: 0;
        }
        .header-institusi {
            padding-right: 20px; 
        }

    </style>
</head>
<body>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success text-center mt-4" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <header class="header-institusi">
        <div class="logo-container">
            <img src="logo_polinema.png" alt="Logo POLINEMA">
            <div class="text-identitas">
                <h3>Intelligent Vision and Intelligent Systems</h3>
                <h2>POLITEKNIK NEGERI MALANG</h2>
            </div>
        </div>
        
        <div class="logout-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                // Mengambil nama pengguna untuk ditampilkan
                $username = htmlspecialchars($_SESSION['username'] ?? 'User');
                ?>
                <span class="user-greeting">Haloo, <?php echo $username; ?></span>
                <a class="btn-danger" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="service-btn btn-filled" href="login.php" style="text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
        <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button> 
    </header>

    <nav class="navbar" id="main-navbar">
        <ul>
            <li><a href="beranda.php">Beranda</a></li>
            
            <li class="beranda.php"><a href="#">Kemahasiswaan<span style="font-size: 0.7em;"></span></a></li>
            
            <li class="beranda.php"><a href="#">Penelitian dan Pengabdian<span style="font-size: 0.7em;"></span></a></li>
            
            <li><a href="berita-pengumuman.php">Berita & Pengumuman</a></li>
        </ul>
    </nav>

    <section class="hero-section">
        <h1>Intelligent Vision and Intelligent Systems Laboratory</h1>
    </section>

    <main class="main-content">
        <div class="logo-lab">
            <img src="Logo-lab-IVSS-300x118.png" alt="Logo Laboratorium IVSS"> 
        </div>

        <div class="content-area">
            <h3>Selamat Datang di Laboratorium IVSS</h3>
            <p>
                Laboratorium Intelligent Vision and Intelligent Systems (IVSS) berfokus pada pengembangan sistem cerdas 
                yang melibatkan pengolahan citra, pengenalan pola, dan aplikasi visi komputer untuk berbagai solusi industri dan pendidikan. 
                Kami mendukung penelitian mahasiswa dan dosen dalam bidang Artificial Intelligence dan Smart Systems.
            </p>
            <p>
                Silakan jelajahi menu di atas untuk mengetahui lebih lanjut tentang fasilitas, proyek penelitian, dan layanan kami.
            </p>
        </div>

        <section class="vision-mission-section">
            <div class="card vision-card">
                <h4 class="card-title">VISION</h4>
                <p>To become a leading laboratory in the development of intelligent vision technology (Intelligent Vision) and integrated intelligent systems (Smart Systems) that are innovative, applicable, and competitive nationally and internationally to support the advancement of information technology and artificial intelligence-based industries.</p>
            </div>
            <div class="card mission-card">
                <h4 class="card-title">MISSION</h4>
                <ul>
                    <li>Conducting research and innovation in the fields of computer vision, artificial intelligence, and smart systems that are oriented towards the needs of industry and society.</li>
                    <li>Providing research and training facilities for Polinema lecturers and students in the development of computer vision-based systems, machine learning, and the Internet of Things (IoT).</li>
                    <li>Encourage academic and industrial collaboration in the application of intelligent vision technology and smart systems to produce real and sustainable solutions.</li>
                    <li>Producing scientific publications, prototypes, and innovative products that support Polinema's reputation as an international-class vocational institution.</li>
                    <li>Developing a research-based adaptive learning ecosystem to produce superior human resources in the field of artificial intelligence and intelligent systems.</li>
                </ul>
            </div>
        </section>

        <hr class="section-divider">
        
        <section class="info-section">
            <h3 class="section-title">Activities & Projects</h3>
            <div class="grid-container">
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Intelligent System</h4>
                    <p>Integration of AI with real systems to aid decision making.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Machine Learning</h4>
                    <p>Machine learning for classification, regression, and clustering using real datasets.</p>
                </div>

                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Computer Vision</h4>
                    <p>Application of AI techniques in image/video processing to detect, recognize, and track objects.</p>
                </div>

            </div>
        </section>
        
        <hr class="section-divider">

        <section class="info-section">
            <h3 class="section-title">Related Lectures</h3>
            <div class="grid-container">
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Artificial Intelligence (AI)</h4>
                    <p>Technology that focuses on developing systems or machines that can perform tasks that normally require human intelligence, such as pattern recognition, learning, problem solving, and decision making.</p>
                </div>

                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Machine Learning</h4>
                    <p>A branch of artificial intelligence that focuses on developing algorithms that enable machines to learn from data to make predictions or decisions without being explicitly programmed.</p>
                </div>

                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Image Processing and Computer Vision</h4>
                    <p>Techniques for processing and analyzing images or video using computers, including object detection, segmentation, pattern recognition, and image interpretation for applications such as facial recognition and autonomous vehicles.</p>
                </div>
            </div>
        </section>
        
        <hr class="section-divider">

        <section class="info-section">
            <h3 class="section-title">Facilities & Equipment</h3>
            <div class="grid-container facilities-grid">
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Table chairs</h4>
                    <p>Basic furniture to support comfortable learning, practicals, and research.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Air conditioning</h4>
                    <p>Air conditioning to maintain a comfortable room temperature during the learning process and maintain equipment reliability.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Prayer Room Area</h4>
                    <p>Special facilities for worship/prayer activities.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Mineral water</h4>
                    <p>Drinking water to maintain hydration and comfort of facility users.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Desktop Computer</h4>
                    <p>A standard computer device used as a workstation for data processing, testing, and research.</p>
                </div>

                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">DSLR Camera</h4>
                    <p>High-quality cameras for high-resolution visual data capture needs in research.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Intel RealSense™ D415 Camera</h4>
                    <p>Special cameras capable of capturing depth (3D) and visual data are essential for research in the field of Computer Vision/Intelligent Systems.</p>
                </div>
                
                <div class="info-card">
                    <span class="card-icon">&#9744;</span>
                    <h4 class="card-title">Camera 260fps</h4>
                    <p>High-speed cameras capable of capturing fast motion (260 frames per second) are used for detailed motion analysis.</p>
                </div>
        </section>

        <hr class="section-divider">

        <section class="info-section service-section">
            <div class="service-header">
                <div class="service-buttons">
                    <a href="#" class="service-btn btn-outline">SOP Praktikum</a>
                    <a href="#" class="service-btn btn-filled">Form Permohonan Layanan</a>
                </div>
            </div>
        </section>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>