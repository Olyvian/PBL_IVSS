<?php 
// 1. Koneksi Database
// Pastikan file config/database.php berisi logic koneksi PDO Anda
include "../config/database.php"; 

$members = [];

try {
    // 2. Query Ambil Semua Member
    // Menggunakan SELECT spesifik untuk keamanan, dan ORDER BY CASE untuk memastikan 
    // anggota 'aktif' selalu di urutan terdepan, lalu diurutkan berdasarkan nama.
    $stmt = $pdo->prepare("SELECT id, nama_lengkap, posisi, bio, foto_profil, status 
                          FROM anggota_lab 
                          ORDER BY 
                            CASE 
                              WHEN status = 'aktif' THEN 1 
                              ELSE 2 
                            END, 
                            nama_lengkap ASC");
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tampilkan error database hanya saat development. 
    // Di lingkungan produksi, ganti dengan pesan user-friendly dan log error.
    die("Error Database: " . $e->getMessage());
}

// Fungsi sederhana untuk memotong teks tanpa memotong di tengah kata
function trim_text($text, $length) {
    if (strlen($text) <= $length) {
        return $text;
    }
    // Cari posisi spasi terakhir sebelum batas panjang
    $last_space = strrpos(substr($text, 0, $length), ' ');
    $trimmed_text = substr($text, 0, $last_space);
    return $trimmed_text . '...';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member - Laboratorium IVSS</title>
    
    <link rel="stylesheet" href="../assets/css/style_profil.css"> 

    <style>
        /* --- Styling Khusus Member Grid --- */

        .member-grid {
            display: grid;
            /* Grid responsif, minimal lebar kartu 260px */
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 30px;
            margin-top: 20px;
        }

        .member-card {
            background-color: #fff; /* var(--card-bg) */
            border: 1px solid #e0e0e0; /* var(--card-border) */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center; 
            display: flex;
            flex-direction: column;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .member-img-container {
            width: 100%;
            height: 280px; /* Tinggi foto tetap agar rapi */
            overflow: hidden;
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
        }

        .member-img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Agar foto tidak gepeng */
            object-position: top; /* Fokus ke wajah (atas) */
            transition: transform 0.4s ease;
        }

        .member-card:hover .member-img {
            transform: scale(1.05); /* Efek zoom dikit saat hover */
        }

        .member-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .member-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0D4C7C; /* var(--polinema-blue) */
            margin: 0 0 5px 0;
        }

        .member-role {
            font-size: 0.95rem;
            color: #CC0000; /* var(--polinema-red) */
            font-weight: 600;
            margin-bottom: 10px;
        }

        .member-bio {
            font-size: 0.9rem;
            color: #34495E; /* var(--text-dark) */
            line-height: 1.5;
            margin-bottom: 15px;
            text-align: justify;
            /* CSS untuk membatasi tampilan bio menjadi 3 baris */
            display: -webkit-box;
            -webkit-line-clamp: 3; 
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Badge Status */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: auto; /* Dorong ke bawah */
        }

        .status-aktif {
            background-color: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #1e7e34;
        }

        .status-alumni {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
    </style>
</head>
<body>

<?php 
// INCLUDE FILE HEADER.PHP
// Memanggil header dan navigasi dari file terpisah
include '../includes/header.php'; 
?>

<section class="hero-section">
    <h1>Our Laboratory Members & Team</h1>
</section>

<main class="main-content">
    <h3 class="section-title">Anggota Laboratorium IVSS</h3>

    <div class="news-list-container">
        
        <div class="member-grid">

            <?php if (empty($members)): ?>
                <p style="text-align:center; width:100%; grid-column: 1 / -1;">Belum ada data anggota.</p>
            <?php endif; ?>

            <?php foreach ($members as $m): ?>
            <div class="member-card">
                <div class="member-img-container">
                    <?php 
                        // Tentukan path foto profil atau gunakan default
                        $foto = !empty($m['foto_profil']) 
                                ? 'uploads/profile/' . $m['foto_profil'] 
                                : 'assets/img/default-profile.png'; 
                    ?>
                    <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($m['nama_lengkap']) ?>" class="member-img">
                </div>

                <div class="member-info">
                    <h4 class="member-name"><?= htmlspecialchars($m['nama_lengkap']) ?></h4>
                    <div class="member-role"><?= htmlspecialchars($m['posisi']) ?></div>
                    
                    <p class="member-bio">
                        <?php 
                            // Tampilkan bio yang sudah dibersihkan dari tag HTML dan dipotong
                            $clean_bio = strip_tags($m['bio']);
                            echo htmlspecialchars(trim_text($clean_bio, 80));
                        ?>
                    </p>

                    <?php if (isset($m['status']) && $m['status'] == 'aktif'): ?>
                        <span class="status-badge status-aktif">Aktif</span>
                    <?php elseif (isset($m['status']) && $m['status'] == 'alumni'): ?>
                        <span class="status-badge status-alumni">Alumni</span>
                    <?php else: ?>
                        <span class="status-badge status-aktif">Aktif</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</main>

<footer class="footer-polinema">
    <div class="footer-top">
        <div class="footer-identitas">
            <div class="logo-container-footer">
                <img src="../assets/img/logo_polinema.png" alt="Logo POLINEMA">
                <div class="text-identitas-footer">
                    <h3>JURUSAN TEKNOLOGI INFORMASI</h3>
                    <h2>POLITEKNIK NEGERI MALANG</h2>
                    <h3>Jl. Soekarno Hatta No.9, Lowokwaru, Kota Malang</h3>
                </div>
            </div>
        </div>
        <div class="social-media">
            <a href="#"><img src="../assets/img/yt.png" alt="YouTube Icon"></a> 
            <a href="#"><img src="../assets/img/ig.jpeg" alt="Instagram Icon"></a>
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
                <li><a href="http://polinema.ac.id">Polinema.ac.id</a></li>
            </ul>
        </div>
    </div>
</footer>

<script src="script.js"></script> 
</body>
</html>