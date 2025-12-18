<?php
// 1. KONEKSI & CONFIG
require_once 'config/database.php';
include 'includes/header.php'; 

$riset_list = [];

// 2. QUERY DATABASE (PostgreSQL Optimized)
$sql = "
    SELECT 
        r.id,
        r.judul_riset,
        r.deskripsi,
        r.link_riset,
        r.tanggal_mulai,
        r.tanggal_selesai,
        STRING_AGG(a.nama_lengkap, ', ') AS nama_anggota
    FROM riset r
    LEFT JOIN riset_anggota ra ON r.id = ra.riset_id
    LEFT JOIN anggota_lab a ON ra.anggota_id = a.id
    GROUP BY r.id
    ORDER BY r.tanggal_mulai DESC
";

try {
    $stmt = $pdo->query($sql);
    $riset_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Error Database: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riset & Inovasi - Laboratorium IVSS</title>
    
    <link rel="stylesheet" href="assets/css/style_profil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Layout & Typography --- */
        .content-riset {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .judul-section {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.2rem;
            font-weight: 800;
            color: #0D4C7C;
            position: relative;
            padding-bottom: 15px;
        }

        .judul-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #CC0000;
            border-radius: 2px;
        }

        /* --- Grid System --- */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        /* --- Modern Card Style --- */
        .card-riset {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eaeaea;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            height: 100%;
        }

        .card-riset:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: transparent;
        }

        /* Garis warna di atas kartu */
        .card-top-accent {
            height: 5px;
            background: linear-gradient(90deg, #0D4C7C, #007bff);
            width: 100%;
        }

        .card-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        /* Judul */
        .riset-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.4;
            margin: 0 0 15px 0; /* Jarak ke deskripsi */
        }

        /* Deskripsi */
        .riset-desc {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
            flex-grow: 1; 
        }

        /* Info Box (Tanggal & Tim) */
        .meta-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            border: 1px solid #eee;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
        }
        .meta-item:last-child { margin-bottom: 0; }

        .meta-icon {
            color: #CC0000;
            width: 18px;
            text-align: center;
            margin-top: 2px;
        }

        .meta-content strong {
            display: block;
            color: #333;
            margin-bottom: 2px;
        }
        .meta-content span {
            color: #666;
        }

        /* Tombol */
        .card-footer {
            margin-top: auto;
        }

        .btn-detail {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px 0;
            background-color: #0D4C7C;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-detail:hover {
            background-color: #0a3d66;
            color: #fff;
        }

        .no-data {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px;
            background: #f9f9f9;
            border-radius: 12px;
            color: #777;
        }
    </style>
</head>

<body>

<section class="hero-section">
    <h1>Riset dan Inovasi</h1>
    <p>Menjelajahi batas teknologi melalui riset Laboratorium IVSS</p>
</section>

<main class="content-riset">
    <h2 class="judul-section">Daftar Riset & Penelitian</h2>

    <div class="card-container">
        
        <?php if (isset($error_msg)): ?>
            <div class="no-data">
                <i class="fa-solid fa-triangle-exclamation fa-3x" style="color:#CC0000; margin-bottom:15px;"></i>
                <p><?= $error_msg ?></p>
            </div>
        <?php elseif (empty($riset_list)): ?>
            <div class="no-data">
                <i class="fa-solid fa-flask fa-3x" style="color:#ccc; margin-bottom:15px;"></i>
                <p>Belum ada data riset yang ditampilkan saat ini.</p>
            </div>
        <?php else: ?>

            <?php foreach ($riset_list as $row): ?>
            
            <div class="card-riset">
                <div class="card-top-accent"></div>
                
                <div class="card-body">
                    <h3 class="riset-title"><?= htmlspecialchars($row['judul_riset']); ?></h3>

                    <div class="riset-desc">
                        <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                    </div>

                    <div class="meta-info">
                        
                        <div class="meta-item">
                            <i class="fa-regular fa-calendar meta-icon"></i>
                            <div class="meta-content">
                                <strong>Periode Pelaksanaan</strong>
                                <span>
                                    <?= date('d M Y', strtotime($row['tanggal_mulai'])); ?> 
                                    s/d 
                                    <?php 
                                        // Cek jika tanggal selesai kosong atau nol
                                        if (empty($row['tanggal_selesai']) || $row['tanggal_selesai'] == '0000-00-00') {
                                            echo 'Sekarang';
                                        } else {
                                            echo date('d M Y', strtotime($row['tanggal_selesai']));
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-users meta-icon"></i>
                            <div class="meta-content">
                                <strong>Tim Peneliti</strong>
                                <span>
                                    <?php if ($row['nama_anggota']): ?>
                                        <?= htmlspecialchars($row['nama_anggota']); ?>
                                    <?php else: ?>
                                        <em style="color:#999;">Data anggota belum diinput.</em>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <?php if (!empty($row['link_riset'])): ?>
                            <a class="btn-detail" href="<?= htmlspecialchars($row['link_riset']); ?>" target="_blank">
                                <i class="fa-solid fa-book-open"></i> Lihat Detail / Publikasi
                            </a>
                        <?php else: ?>
                            <button class="btn-detail" style="background:#ccc; cursor:not-allowed;" disabled>
                                Detail Belum Tersedia
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</main>

<script src="assets/js/script_main.js"></script>

</body>
</html>

<?php
include 'includes/footer.php';
?>
