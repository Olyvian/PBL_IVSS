<?php 
// 1. Koneksi Database & Header
require_once 'config/database.php';
include 'includes/header.php'; // Menggunakan header utama Anda

$has_featured = false;
$produk_list = [];
$featured = [];

try {
    // 2. LOGIKA DATA: Ambil 1 Produk Terbaru sebagai "Featured"
    $query_featured = $pdo->prepare("SELECT * FROM produk ORDER BY id DESC LIMIT 1");
    $query_featured->execute();
    $row_featured = $query_featured->fetch();

    if ($row_featured) {
        $has_featured = true;

        // Mapping Data Featured
        $featured = [
            'judul'      => $row_featured['nama_produk'], 
            'tanggal'    => $row_featured['tanggal_dibuat'] ?? date('Y-m-d'), // Fallback jika null
            // Potong deskripsi biar tidak kepanjangan
            'deskripsi'  => substr(strip_tags($row_featured['deskripsi']), 0, 250) . '...', 
            // Cek gambar
            'gambar'     => !empty($row_featured['gambar']) 
                            ? 'uploads/produk/' . $row_featured['gambar'] 
                            : 'assets/img/placeholder_product.png', 
            // Link eksternal (GitHub/Demo) jika ada
            'link'       => $row_featured['link_produk'] ?? '#'
        ];
        
        // 3. LOGIKA DATA: Ambil Sisanya untuk Grid
        $query_list = $pdo->prepare("SELECT * FROM produk WHERE id != :id_featured ORDER BY id DESC");
        $query_list->execute(['id_featured' => $row_featured['id']]);
        $rows_list = $query_list->fetchAll();

        // Mapping Data Grid
        foreach($rows_list as $row) {
            $produk_list[] = [
                'judul'      => $row['nama_produk'],
                'tanggal'    => $row['tanggal_dibuat'] ?? date('Y-m-d'),
                'deskripsi'  => substr(strip_tags($row['deskripsi']), 0, 150) . '...',
                'gambar'     => !empty($row['gambar']) 
                                ? 'uploads/produk/' . $row['gambar'] 
                                : 'assets/img/placeholder_product.png',
                'link'       => $row['link_produk'] ?? '#'
            ];
        }

    } else {
        // Data Dummy jika Database Kosong
        $featured = [
            'gambar'     => 'assets/img/no-data.png', 
            'judul'      => 'Belum Ada Produk',
            'tanggal'    => date('Y-m-d'),
            'deskripsi'  => 'Saat ini belum ada produk atau inovasi yang dipublikasikan.',
            'link'       => '#'
        ];
    }
    
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <style>
        /* Memastikan gambar produk di card featured mengisi ruang dengan rapi */
        .featured-news-card img {
            object-fit: cover;
        }
        /* Memastikan gambar di grid seragam */
        .news-item-grid img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <h1>Our Innovative Products & Prototypes</h1>
    </section>

    <main class="main-content">
        <h3 class="section-title">Produk & Inovasi Lab IVSS</h3>

        <div class="news-list-container">

            <div class="featured-news-card">
                <img src="<?= htmlspecialchars($featured['gambar']) ?>" alt="Gambar Produk Utama">
                
                <div class="featured-news-content">
                    <h4><?= htmlspecialchars($featured['judul']) ?></h4>
                    
                    <span class="date-meta">
                        <i class="fa-regular fa-calendar"></i> 
                        <?= date("d F Y", strtotime($featured['tanggal'])) ?>
                    </span>
                    
                    <p><?= htmlspecialchars($featured['deskripsi']) ?></p>
                    
                    <?php if (!empty($featured['link']) && $featured['link'] !== '#'): ?>
                        <a href="<?= htmlspecialchars($featured['link']) ?>" target="_blank">
                            Lihat Detail / Demo <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($has_featured && !empty($produk_list)):?>
            <hr class="section-divider">
            <?php endif; ?>

            <div class="news-grid">
                
                <?php if (empty($produk_list) && $has_featured): ?>
                <?php elseif (empty($produk_list) && !$has_featured): ?>
                    <p>Tidak ada data produk.</p>
                <?php endif; ?>

                <?php foreach ($produk_list as $prod): ?>
                    <div class="news-item-grid">
                        <img src="<?= htmlspecialchars($prod['gambar']) ?>" alt="Thumbnail Produk">
                        
                        <div class="news-item-content">
                            <h4><?= htmlspecialchars($prod['judul']) ?></h4>
                            
                            <span class="date-meta">
                                <?= date("d F Y", strtotime($prod['tanggal'])) ?>
                            </span>
                            
                            <p><?= htmlspecialchars($prod['deskripsi']) ?></p>
                            
                            <?php if (!empty($prod['link']) && $prod['link'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($prod['link']) ?>" target="_blank">
                                    Lihat Detail →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div> </div>
    </main>

    <script src="assets/js/script_main.js"></script> 
</body>
</html>

<?php 
// Include Footer
include 'includes/footer.php'; 
?>