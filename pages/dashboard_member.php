<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
redirectIfNotLoggedIn(['admin_lab']); 

// Set judul dan halaman aktif
$pageTitle = 'Member';
$activePage = 'member';
$stmt = $pdo->query("SELECT * FROM anggota_lab ORDER BY nama_lengkap ASC");
$list = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $bio = $_POST['bio'];
    $posisi = $_POST['posisi'];
    $foto_profil = null;

    if (!empty($_FILES['foto_profil']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['foto_profil']['name']);
        $uploadPath = __DIR__ . '/../uploads/profile/' . $filename; // Pastikan folder ini ada
        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadPath)) {
            $foto_profil = $filename;
        }
    }
        // Mode TAMBAH
        $stmt = $pdo->prepare("INSERT INTO anggota_lab (nama_lengkap,bio,posisi,foto_profil)  VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_lengkap, $bio, $posisi, $foto_profil]);
    
    header('Location: dashboard_member.php'); 
    exit;
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT foto_profil FROM anggota_lab WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    if ($old && $old['foto_profil']) {
        $filePath = __DIR__ . "/../uploads/news_images/" . $old['foto_profil'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM anggota_lab WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_member.php'); 
    exit;
}

// Panggil sidebar
include_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Konten Halaman -->
<div class="card">
<div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Member</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Anggota Lab</button>
        <button class="btn btn-primary hidden" id="btnCancelForm">Tutup Form</button>
    </div>
    <div class="form-container" id="newsFormContainer">
        <h4 id="formTitle">Tambah Anggota Baru</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="berita_id" id="berita_id">
            <input type="hidden" name="existing_foto_profil" id="existing_gambar_header">
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="posisi">Posisi:</label>
                <input type="text" name="posisi" id="posisi" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="isi">Isi Bio:</label>
                <textarea name="bio" id="bio" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="image_input">Unggah Foto Profil:</label>
                <input type="file" name="foto_profil" id="image_input" class="form-control">
            </div>
            <div id="image_preview_container" style="display:none; margin-top:10px;">
                <p><strong>Preview Gambar:</strong></p>
                <img id="image_preview" src="" alt="Preview">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding: 2rem;">
        <h4>Halaman Manajemen Member</h4>
        <table>
            <thead>
                <tr>
                    <th class="table-thumb-col">Gambar Profil</th>
                    <th>Nama Lengkap</th>
                    <th>Bio</th>
                    <th class="table-tipe-col">Posisi</th> <!-- Kolom Tipe BARU -->
                    <th class="table-date-col">Tanggal Ditambahkan</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="6" class="empty-table"> Belum ada anggota lab.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($list as $n): ?>
                <tr>
                    <td class="table-thumb-col">
                        <?php if (!empty($n['foto_profil'])): ?>
                            <img src="../uploads/profile/<?= htmlspecialchars($n['foto_profil']) ?>" class="table-thumbnail">
                        <?php else: ?>
                            <span class="table-thumbnail-placeholder">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(string: $n['nama_lengkap']) ?></td>
                    <td>
                        <?php
                        $excerpt = strip_tags(string: $n['bio']);
                        if (strlen($excerpt) > 20) {
                            echo htmlspecialchars(substr($excerpt, 0, length: 20)) . '...';
                        } else {
                            echo htmlspecialchars($excerpt);
                        }
                        ?>
                    </td>
                    <!-- Tampilkan Tipe (BARU) -->
                    <td class="table-tipe-col">
                            <?php echo htmlspecialchars(string: $n['posisi']); ?>
                    </td>
                    <td class="table-date-col"><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                    <td class="table-aksi-col">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editNews(<?= $n['id'] ?>, <?= json_encode($n['judul']) ?>, <?= json_encode($n['isi']) ?>, <?= json_encode($n['foto_profil'] ?? '') ?>, <?= json_encode($n['tipe']) ?>); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_member.php?delete=<?= $n['id'] ?>" class="btn-icon btn-delete" title="Hapus"
                           onclick="return confirm('Anda yakin ingin menghapus berita ini?')">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../assets/js/script.js"></script>