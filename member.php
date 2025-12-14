<?php 
// 1. Koneksi Database
include "config/database.php"; 
include 'includes/header.php'; 

$members = [];

try {
    // 2. Query Ambil Semua Member
    $stmt = $pdo->prepare("SELECT id, nama_lengkap, posisi, bio, foto_profil, status, nomor_telepon 
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
    die("Error Database: " . $e->getMessage());
}

function trim_text($text, $length) {
    if (strlen($text) <= $length) return $text;
    $last_space = strrpos(substr($text, 0, $length), ' ');
    return substr($text, 0, $last_space) . '...';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member - Laboratorium IVSS</title>
    
    <link rel="stylesheet" href="assets/css/style_profil.css"> 

    <style>
        .member-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
            gap: 30px;
            margin-top: 20px;
        }

        .member-card {
            position: relative; /* PENTING: Agar icon Telpon bisa diposisikan absolute di dalam kartu */
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center; 
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .member-img-container {
            width: 100%;
            height: 260px;
            overflow: hidden;
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
        }

        .member-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top; 
            transition: transform 0.4s ease;
        }

        .member-card:hover .member-img {
            transform: scale(1.05);
        }

        .member-info {
            padding: 20px;
            /* Tambahkan padding bawah ekstra agar teks bio tidak tertutup icon Telpon */
            padding-bottom: 60px; 
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .member-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0D4C7C; 
            margin: 0 0 5px 0;
            line-height: 1.3;
        }

        .member-role {
            font-size: 0.9rem;
            color: #CC0000; 
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .member-bio {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.5;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* --- STYLING KHUSUS FLOAT ICON POJOK KANAN BAWAH --- */
        .wa-float-container {
            position: absolute;
            bottom: 15px;
            right: 15px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .wa-icon {
            /* Reset ukuran kotak dan background */
            width: auto;
            height: auto;
            background-color: transparent; /* Background transparan */
            box-shadow: none; /* Hilangkan bayangan kotak */
            border-radius: 0;
            
            /* Styling Ikon */
            color: #0056b3; /* Warna ikon jadi Biru */
            font-size: 1.8rem; /* Ukuran diperbesar agar jelas karena tanpa kotak */
            
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, color 0.2s;
        }

        wa-float-container:hover .wa-icon {
            transform: scale(1.2); /* Efek membesar saat di-hover */
        }

        /* Tooltip Nomor (Awalnya Sembunyi) */
        .wa-number-tooltip {
            background-color: #333;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 600;
            margin-right: 10px; /* Jarak dari icon */
            
            /* Efek Sembunyi */
            opacity: 0;
            visibility: hidden;
            transform: translateX(10px); /* Geser sedikit ke kanan */
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        /* Efek Muncul saat Hover Container */
        .wa-float-container:hover .wa-number-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(0); /* Geser ke posisi normal */
        }

        /* Posisi Badge Status dipindah ke Kiri Bawah agar seimbang */
        .status-badge-container {
            position: absolute;
            bottom: 20px;
            left: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-aktif { background-color: #e6f4ea; color: #1e7e34; border: 1px solid #c3e6cb; }
        .status-alumni { background-color: #f8f9fa; color: #6c757d; border: 1px solid #d6d8db; }

    </style>
</head>
<body>

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
                            $clean_bio = strip_tags($m['bio']);
                            echo htmlspecialchars(trim_text($clean_bio, 80));
                        ?>
                    </p>
                </div>

                <div class="status-badge-container">
                    <?php 
                        $statusClass = ($m['status'] == 'alumni') ? 'status-alumni' : 'status-aktif';
                        $statusLabel = ($m['status'] == 'alumni') ? 'Alumni' : 'Aktif';
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                </div>

                <?php if (!empty($m['nomor_telepon'])): ?>
                <div class="wa-float-container">
                    <div class="wa-number-tooltip">
                        <?= htmlspecialchars($m['nomor_telepon']) ?>
                    </div>
                    
                    <div class="wa-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>