<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/mail.php';

$pageTitle = 'Pendaftaran';
$activePage = 'pendaftaran';

// --- Handle Status Update & Email Notification ---
if (isset($_GET['update_status'], $_GET['id'], $_GET['email'])) {
    $status = $_GET['update_status'];
    $id = $_GET['id'];
    $email = $_GET['email'];

    if (in_array($status, ['diterima', 'ditolak'])) {
        
        // Update DB
        $pdo->prepare("UPDATE pendaftaran_magang SET status = ? WHERE id = ?")->execute([$status, $id]);
        
        $stmt = $pdo->prepare("SELECT nama_lengkap FROM pendaftaran_magang WHERE id = ?");
        $stmt->execute([$id]);
        $nama_lengkap = $stmt->fetchColumn();

        // Prepare Email Content
        if ($status == 'diterima') {
            $subject = 'Selamat! Anda Diterima Magang di Lab IVSS';
            $pesan_intro = 'Berdasarkan hasil seleksi administrasi dan wawancara, kami dengan senang hati menginformasikan bahwa Anda <strong>DITERIMA</strong> untuk mengikuti program magang.';
            
            $extra_content = '
                <table width="100%" cellpadding="15" style="background:#f9fbfd; border-left: 4px solid #FF8C00; margin-bottom: 25px;">
                    <tr><td>
                        <p style="margin:5px 0; font-size:14px;"><strong>Posisi:</strong> Magang</p>
                        <p style="margin:5px 0; font-size:14px;"><strong>Lokasi:</strong> Lab IVSS Gedung Sipil & TI</p>
                    </td></tr>
                </table>
                <table width="100%">
                    <tr><td align="center">
                        <a href="#" style="background-color:#FF8C00; color:#fff; padding:14px 30px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block;">Konfirmasi Penerimaan</a>
                    </td></tr>
                </table>';
        } else {
            $subject = 'Pemberitahuan Hasil Seleksi Magang di Lab IVSS';
            $pesan_intro = 'Setelah meninjau aplikasi Anda, dengan berat hati kami sampaikan bahwa kami <strong>belum dapat menerima</strong> Anda untuk posisi magang pada periode ini.';
            $extra_content = ''; 
        }

        // Send Email
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <body style="background-color:#f4f4f4; font-family:Arial,sans-serif; margin:0; padding:20px;">
            <table align="center" width="600" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                <tr>
                    <td align="center" style="background:#003366; padding:30px 20px;">
                        <h1 style="color:#fff; margin:0; font-size:22px;">Lab IVSS</h1>
                    </td>
                </tr>
                <tr><td height="6" style="background:#FF8C00;"></td></tr>
                <tr>
                    <td style="padding:40px 30px; color:#333;">
                        <h2 style="color:#003366; margin-top:0;">Halo, '.$nama_lengkap.'</h2>
                        <p style="font-size:16px; line-height:1.6;">'.$pesan_intro.'</p>
                        '.$extra_content.'
                        <p style="margin-top:30px; border-top:1px solid #eee; padding-top:20px; font-size:14px; color:#666;">
                            Regards,<br><strong>Tim Lab IVSS</strong>
                        </p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        try {
            $mail->send();
        } catch (Exception $e) {
            // Log error silently or handle user feedback
        }
        
        header("Location:dashboard_pendaftaran.php");
        exit;
    }
}

// Fetch Registrations
$stmt = $pdo->query("SELECT p.*, u.username, u.email 
                     FROM pendaftaran_magang p
                     JOIN users u ON p.user_id = u.id 
                     ORDER BY p.tanggal_daftar DESC");
$pendaftaranList = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Pendaftaran Magang</span>
        </div>
    </div>

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
                    <tr><td colspan="5" class="empty-table">Belum ada pendaftaran.</td></tr>
                <?php endif; ?>
                
                <?php foreach ($pendaftaranList as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['username']) ?></td>
                    <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                    <td>
                        <?php
                        $status = $p['status'];
                        $badge_class = 'bg-secondary';
                        if ($status == 'diterima') $badge_class = 'bg-success';
                        if ($status == 'ditolak') $badge_class = 'bg-danger';
                        if ($status == 'pending') $badge_class = 'bg-warning';
                        ?>
                        <span class="badge <?= $badge_class ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                    </td>
                    <td class="table-date-col"><?= date('d M Y', strtotime($p['tanggal_daftar'])) ?></td>
                    <td>
                        <a href="../uploads/cv/<?= htmlspecialchars($p['cv_file']) ?>" class="btn btn-sm btn-info" target="_blank">Lihat CV</a>

                        <?php if ($p['status'] === 'pending'): ?>
                            <a href="?update_status=diterima&id=<?= $p['id'] ?>&email=<?= $p['email'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Terima pendaftaran ini?')">Terima</a>
                            <a href="?update_status=ditolak&id=<?= $p['id'] ?>&email=<?= $p['email'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pendaftaran ini?')">Tolak</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/table.php'; ?>