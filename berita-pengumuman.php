<?php 
include "config/database.php";
include 'includes/header.php'; 

$has_featured = false;
$news_list = [];
$featured = [];

try {
    // Query Berita Utama
    $query_featured = $pdo->prepare("SELECT * FROM berita ORDER BY created_at DESC LIMIT 1");
    $query_featured->execute();
    $row_featured = $query_featured->fetch();

    if ($row_featured) {
        $has_featured = true;
        $featured = [
            'judul'      => $row_featured['judul'], 
            'created_at' => $row_featured['created_at'],
            'isi'        => substr(strip_tags($row_featured['isi']), 0, 250) . '...', 
            'gambar'     => !empty($row_featured['gambar_header']) 
                            ? 'uploads/news/' . $row_featured['gambar_header'] 
                            : 'assets/img/placeholder.png', 
            'link'       => 'detail_berita.php?id=' . $row_featured['id'],
            'tipe'       => $row_featured['tipe'] 
        ];
        
        // Query Berita Lainnya
        $query_list = $pdo->prepare("SELECT * FROM berita WHERE id != :id_featured ORDER BY created_at DESC");
        $query_list->execute(['id_featured' => $row_featured['id']]);
        $rows_list = $query_list->fetchAll();

        foreach($rows_list as $row) {
            $news_list[] = [
                'judul'      => $row['judul'],
                'created_at' => $row['created_at'],
                'isi'        => substr(strip_tags($row['isi']), 0, 150) . '...',
                'gambar'     => !empty($row['gambar_header']) 
                                ? 'uploads/news/' . $row['gambar_header'] 
                                : 'assets/img/placeholder.png',
                'link'       => 'detail_berita.php?id=' . $row['id'],
                'tipe'       => $row['tipe']
            ];
        }

    } else {
        // Data Dummy
        $featured = [
            'gambar'     => 'assets/img/no-news.png', 
            'judul'      => 'Belum Ada Berita Terbaru',
            'created_at' => date('Y-m-d'),
            'isi'        => 'Saat ini belum ada berita atau pengumuman yang tersedia.',
            'link'       => '#',
            'tipe'       => 'umum'
        ];
    }
    
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita dan Pengumuman - Laboratorium IVSS</title>
    <link rel="stylesheet" href="assets/css/style_profil.css">

    <style>
        /* 1. Pastikan Container Parent Relative */
        .featured-news-content, 
        .news-item-content {
            position: relative; /* Agar badge bisa diposisikan absolute terhadap kotak ini */
            padding-bottom: 50px; /* Memberi ruang di bawah agar teks tidak menabrak badge */
        }

        /* 2. Styling Badge Absolute */
        .badge-tipe {
            position: absolute;
            bottom: 20px; /* Jarak dari bawah */
            right: 20px;  /* Jarak dari kanan */
            
            display: inline-block;
            padding: 5px 10px;
            border-radius: 10px; /* Membuatnya lebih bulat (pill shape) */
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: black;
            background-color: #ffc107;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <h1>IVSS Laboratory News and Announcements</h1>
</section>

<main class="main-content">
    <h3 class="section-title">Berita dan Pengumuman - Laboratorium IVSS</h3>

    <div class="news-list-container">

        <div class="featured-news-card">
            <img src="<?= $featured['gambar'] ?>" alt="Gambar Berita Utama">
            <div class="featured-news-content">
                
                <h4><?= $featured['judul'] ?></h4>
                <span class="date-meta"><?= date("d F Y", strtotime($featured['created_at'])) ?></span>
                <p><?= $featured['isi'] ?></p>
                
                <a href="<?= $featured['link'] ?>">Baca Selengkapnya →</a>

                <span class="badge-tipe badge-<?= strtolower($featured['tipe']) ?>">
                    <?= htmlspecialchars($featured['tipe']) ?>
                </span>
            </div>
        </div>

        <?php if ($has_featured || !empty($news_list)):?>
        <hr class="section-divider">
        <?php endif; ?>

        <div class="news-grid">
        <?php if (empty($news_list) && $has_featured): ?>
            <p>Tidak ada berita lain selain berita utama.</p>
        <?php elseif (empty($news_list) && !$has_featured): ?>
            <p>Tidak ada berita atau pengumuman lain yang tersedia saat ini.</p>
        <?php endif; ?>

        <?php foreach ($news_list as $news): ?>
            <div class="news-item-grid">
                <img src="<?= $news['gambar'] ?>" alt="Thumbnail Berita">
                <div class="news-item-content">
                    
                    <h4><?= $news['judul'] ?></h4>
                    <span class="date-meta"><?= date("d F Y", strtotime($news['created_at'])) ?></span>
                    <p><?= $news['isi'] ?></p>
                    
                    <a href="<?= $news['link'] ?>">Baca Selengkapnya →</a>

                    <span class="badge-tipe badge-<?= strtolower($news['tipe']) ?>">
                        <?= htmlspecialchars($news['tipe']) ?>
                    </span>

                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>
</main>
    <script src="script.js"></script> 
</body>
</html>
<?php
include 'includes/footer.php'; 
?>