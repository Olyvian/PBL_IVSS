<?php
include 'config/database.php';
include 'includes/header.php'; 

// Ambil semua data riset menggunakan PDO
$sql = "SELECT * FROM riset ORDER BY tanggal_mulai DESC";
$stmt = $pdo->query($sql);
$riset = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Riset - Laboratorium IVSS</title>
    <link rel="stylesheet" href="assets/css/style_profil.css">

    <style>
        .content-riset {
            padding: 30px;
            max-width: 1100px;
            margin: auto;
        }

        .judul-section {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
            color: #003399;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card-riset {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .card-riset h3 {
            margin-bottom: 10px;
            color: #003399;
        }

        .card-riset .deskripsi {
            margin-bottom: 15px;
            color: #444;
        }

        .card-riset .tanggal {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-link {
            display: inline-block;
            padding: 8px 14px;
            background: #003399;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-link:hover {
            background: #002277;
        }
    </style>
</head>

<section class="hero-section">
    <h1>IVSS Laboratory Research and Innovations</h1>
</section>

<body>

<!-- ===================== MAIN CONTENT (DAFTAR RISET) ===================== -->

<main class="content-riset">
    <h2 class="judul-section">Daftar Riset Laboratorium</h2>

    <div class="card-container">
        <?php foreach ($riset as $row): ?>
            <div class="card-riset">
                <h3><?= $row['judul_riset']; ?></h3>

                <p class="deskripsi">
                    <?= $row['deskripsi']; ?>
                </p>

                <p class="tanggal">
                    <strong>Tanggal Mulai:</strong> 
                    <?= $row['tanggal_mulai'] ?: '–'; ?> <br>

                    <strong>Tanggal Selesai:</strong> 
                    <?= $row['tanggal_selesai'] ?: '–'; ?>
                </p>

                <?php if (!empty($row['link_riset'])): ?>
                    <a class="btn-link" href="<?= $row['link_riset']; ?>" target="_blank">
                        Lihat Detail Riset
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="script.js"></script>
</body>
</html>

<?php
include 'includes/footer.php'; 
?>