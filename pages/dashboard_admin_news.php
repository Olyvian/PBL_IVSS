<?php

// 1. Panggil file config dan auth Anda
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Ambil data user untuk ditampilkan di header
if (isLoggedIn() && !isset($_SESSION['username'])) {
    try {
        $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 
        } else {
            session_destroy();
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        die("Error: Tidak bisa memuat data user. " . $e->getMessage());
    }
}

// 3. Panggil fungsi redirect dari auth.php
// redirectIfNotLoggedIn(['admin_news']);

// 4. Logika CRUD (Tambah/Edit) - Jika ada data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = __DIR__ . '/../uploads/news_images/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $filename;
        }
    }

    if (!empty($_POST['news_id'])) {
        // Mode EDIT (karena ada news_id)
        $id = $_POST['news_id'];
        if ($image === null) {
            $image = $_POST['existing_image'] ?? null;
        }
        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $content, $image, $id]);
    } else {
        // Mode TAMBAH (karena tidak ada news_id)
        $stmt = $pdo->prepare("INSERT INTO news (title, content, image) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $image]);
    }
    header('Location: dashboard_admin_news.php');
    exit;
}

// 5. Logika Hapus - Jika ada parameter GET 'delete'
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    if ($old && $old['image']) {
        $filePath = __DIR__ . "/../uploads/news_images/" . $old['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_admin_news.php');
    exit;
}

// 6. Ambil semua data berita untuk ditampilkan di tabel
$stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC");
$newsList = $stmt->fetchAll();

// ===== TAMBAHAN PHP (Hitung Total Berita) =====
$totalBerita = count($newsList);
// ===============================================

include_once __DIR__ . '/../includes/admin/header.php';
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Berita</span>
            <span class="stat-number"><?php echo $totalBerita; ?></span>
        </div>
    </div>
    </div>
<div class="card">

    <div class="form-container" id="newsFormContainer">
        </div>

    <div class="card-body">
        <table>
            </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Berita</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Berita</button>
    </div>

    <div class="form-container" id="newsFormContainer">
        <h4 id="formTitle">Tambah Berita Baru</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="news_id" id="news_id">
            <input type="hidden" name="existing_image" id="existing_image">
            <div class="form-group">
                <label for="title">Judul:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="content">Isi Berita:</label>
                <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="image_input">Unggah Gambar:</label>
                <input type="file" name="image" id="image_input" class="form-control">
            </div>
            <div id="image_preview_container" style="display:none; margin-top:10px;">
                <p><strong>Preview Gambar:</strong></p>
                <img id="image_preview" src="" alt="Preview">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" id="btnCancelForm">Batal</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th class="table-thumb-col">Thumbnail</th>
                    <th>Judul</th>
                    <th>Isi Berita</th> <th class="table-date-col">Tanggal</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($newsList)): ?>
                    <tr>
                        <td colspan="5" class="empty-table"> Belum ada berita.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($newsList as $n): ?>
                <tr>
                    <td class="table-thumb-col">
                        <?php if (!empty($n['image'])): ?>
                            <img src="../uploads/news_images/<?= htmlspecialchars($n['image']) ?>" class="table-thumbnail">
                        <?php else: ?>
                            <span class="table-thumbnail-placeholder">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    
                    <td>
                        <?php
                        // Potong isi berita agar tabel rapi
                        $excerpt = strip_tags($n['content']);
                        if (strlen($excerpt) > 80) {
                            echo htmlspecialchars(substr($excerpt, 0, 80)) . '...';
                        } else {
                            echo htmlspecialchars($excerpt);
                        }
                        ?>
                    </td>
                    
                    <td class="table-date-col"><?= date('d M Y', strtotime($n['published_at'])) ?></td>
                    <td class="table-aksi-col">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editNews(<?= $n['id'] ?>, <?= json_encode($n['title']) ?>, <?= json_encode($n['content']) ?>, <?= json_encode($n['image'] ?? '') ?>); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_admin_news.php?delete=<?= $n['id'] ?>" class="btn-icon btn-delete" title="Hapus"
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

<?php
// 8. Masukkan Footer (HTML Selesai & JS)
include_once __DIR__ . '/../includes/admin/footer.php';
?>