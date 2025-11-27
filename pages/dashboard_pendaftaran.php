<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/mail.php';
// redirectIfNotLoggedIn(['admin_lab']); dimatikan buat test desain

// Logika Update Status
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $status = $_GET['update_status'];
    $id = $_GET['id'];
    // Pastikan status valid
    if (in_array($status, ['diterima', 'ditolak'])) {
        $stmt = $pdo->prepare("UPDATE pendaftaran_magang SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $mail->addAddress($_GET['email']);
        $mail->send();
    }
    header('Location: dashboard_pendaftaran.php');
    exit;
}


// Ambil semua data pendaftaran
$stmt = $pdo->query("SELECT p.*, u.username 
                     FROM pendaftaran_magang p
                     JOIN users u ON p.user_id = u.id 
                     ORDER BY p.tanggal_daftar DESC");
$pendaftaranList = $stmt->fetchAll();

// Hitung pendaftaran pending
$totalPending = 0;
foreach ($pendaftaranList as $p) {
    if ($p['status'] == 'pending') {
        $totalPending++;
    }
}

// Set judul dan halaman aktif
$pageTitle = 'Pendaftaran';
$activePage = 'pendaftaran';

// Panggil sidebar
include_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Card Tabel Pendaftaran -->
<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Pendaftaran Magang</span>
        </div>
        <!-- Tidak ada tombol "Tambah" di sini -->
    </div>

    <!-- Tabel Daftar Pendaftaran -->
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>Pendaftar (User)</th>
                    <th>Nama Lengkap</th>
                    <th>Status</th>
                    <th class="table-date-col">Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendaftaranList)): ?>
                    <tr><td colspan="6" class="empty-table">Belum ada pendaftaran.</td></tr>
                <?php endif; ?>
                <?php foreach ($pendaftaranList as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['username']) ?></td>
                    <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                    <td>
                        <?php
                        $status = $p['status'];
                        $badge_class = 'bg-secondary'; // default
                        if ($status == 'diterima') $badge_class = 'bg-success';
                        if ($status == 'ditolak') $badge_class = 'bg-danger';
                        if ($status == 'pending') $badge_class = 'bg-warning';
                        ?>
                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                    </td>
                    <td class="table-date-col"><?= date('d M Y', strtotime(datetime: $p['tanggal_daftar'])) ?></td>
                    <td>
                        <!-- Asumsi CV ada di folder uploads/cv/ -->
                        <a href="../uploads/cv/<?= htmlspecialchars($p['cv_file']) ?>" class="btn btn-sm btn-info" target="_blank">Lihat CV</a>

                        <?php 
                        $stmt = $pdo->prepare('SELECT email from users WHERE id = :user_id LIMIT 1');

                        $email=$stmt->execute(['user_id' => $p['user_id']]);

                        $user = $stmt->fetch(); 
                        ?>

                        <?php if ($p['status'] === 'pending'): ?>
                            <a href="?update_status=diterima&id=<?= $p['id'] ?>&email=<?= $user['email'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Anda yakin ingin MENERIMA pendaftaran ini?')">Terima</a>
                            <a href="?update_status=ditolak&id=<?= $p['id'] ?>&email=<?= $user['email'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin MENOLAK pendaftaran ini?')">Tolak</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>