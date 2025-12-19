<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Auth checks
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
            header('Location: ../Login/login.php');
            exit;
        }
    } catch (PDOException $e) {
        die("Error loading user data: " . $e->getMessage());
    }
}

$pageTitle = 'Berita';
$activePage = 'berita';

// --- Handle Form Submission (Create & Update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tipe = $_POST['tipe'];
    $author_id = $_SESSION['user_id'] ?? null;
    $gambar_header = null;

    // Handle Image Upload
    if (!empty($_FILES['gambar_header']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['gambar_header']['name']);
        $uploadPath = __DIR__ . '/../uploads/news/' . $filename;
        if (move_uploaded_file($_FILES['gambar_header']['tmp_name'], $uploadPath)) {
            $gambar_header = $filename;
        }
    }

    if (!empty($_POST['berita_id'])) {
        // Mode: Edit
        $id = $_POST['berita_id'];
        if ($gambar_header == null) {
            $gambar_header = $_POST['existing_gambar_header'] ?? null;
        }
        $stmt = $pdo->prepare("UPDATE berita SET judul = ?, isi = ?, gambar_header = ?, tipe = ? WHERE id = ?");
        $stmt->execute([$judul, $isi, $gambar_header, $tipe, $id]);
    } else {
        // Mode: Create
        $stmt = $pdo->prepare("INSERT INTO berita (judul, isi, gambar_header, tipe, author_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$judul, $isi, $gambar_header, $tipe, $author_id]);
    }
    
    header('Location: dashboard_berita.php');
    exit;
}

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT gambar_header FROM berita WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    
    if ($old && $old['gambar_header']) {
        $filePath = __DIR__ . "/../uploads/news/" . $old['gambar_header'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_berita.php');
    exit;
}

// Fetch all news
$stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC");
$newsList = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

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
            <input type="hidden" name="berita_id" id="berita_id">
            <input type="hidden" name="existing_gambar_header" id="existing_gambar_header">

            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" name="judul" id="judul" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="tipe">Kategori:</label>
                <select name="tipe" id="tipe" class="form-control" required>
                    <option value="berita">Berita</option>
                    <option value="pengumuman">Pengumuman</option>
                    <option value="pelatihan">Pelatihan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="isi">Isi Berita:</label>
                <textarea name="isi" id="isi" class="form-control" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="image_input">Unggah Gambar Header:</label>
                <input type="file" name="gambar_header" id="image_input" class="form-control">
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
                    <th class="table-thumb-col">Gambar</th>
                    <th>Judul</th>
                    <th>Isi Berita</th>
                    <th class="table-tipe-col">Tipe</th>
                    <th class="table-date-col">Tanggal</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($newsList)): ?>
                    <tr><td colspan="6" class="empty-table">Belum ada berita.</td></tr>
                <?php endif; ?>

                <?php foreach ($newsList as $n): ?>
                    <tr>
                        <td class="table-thumb-col">
                            <?php if (!empty($n['gambar_header'])): ?>
                                <img src="../uploads/news/<?= htmlspecialchars($n['gambar_header']) ?>" class="table-thumbnail">
                            <?php else: ?>
                                <span class="table-thumbnail-placeholder">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($n['judul']) ?></td>
                        <td>
                            <?php
                            $excerpt = strip_tags($n['isi']);
                            echo strlen($excerpt) > 50 ? htmlspecialchars(substr($excerpt, 0, 50)) . '...' : htmlspecialchars($excerpt);
                            ?>
                        </td>
                        <td class="table-tipe-col">
                            <?= htmlspecialchars($n['tipe']); ?>
                        </td>
                        <td class="table-date-col"><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                        <td class="table-aksi-col">
                            <a href="#" class="btn-icon btn-edit" title="Edit"
                                onclick='editNews(<?= $n['id'] ?>, <?= json_encode($n['judul']) ?>, <?= json_encode($n['isi']) ?>, <?= json_encode($n['gambar_header'] ?? '') ?>, <?= json_encode($n['tipe']) ?>); return false;'>
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            <a href="dashboard_berita.php?delete=<?= $n['id'] ?>" class="btn-icon btn-delete" title="Hapus"
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

<script>
    const formContainer = document.getElementById('newsFormContainer');
    const btnShow = document.getElementById('btnShowForm');
    const btnCancel = document.getElementById('btnCancelForm');
    const formTitle = document.getElementById('formTitle');
    const form = formContainer.querySelector('form');

    // Input References
    const inputId = document.getElementById('berita_id');
    const inputJudul = document.getElementById('judul');
    const inputIsi = document.getElementById('isi');
    const inputTipe = document.getElementById('tipe');
    const inputExistingImage = document.getElementById('existing_gambar_header');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    // Toggle Form Visibility
    btnShow.addEventListener('click', () => {
        if (formContainer.style.display === 'block') {
            formContainer.style.display = 'none';
            btnShow.innerText = 'Tambah Berita';
            form.reset();
        } else {
            form.reset();
            inputId.value = '';
            inputExistingImage.value = '';
            formTitle.innerText = 'Tambah Berita Baru';
            btnShow.innerText = 'Tutup Form';
            previewContainer.style.display = 'none';
            formContainer.style.display = 'block';
            window.scrollTo(0, 0);
        }
    });

    // Populate Form for Editing
    function editNews(id, judul, isi, gambar, tipe) {
        form.reset();
        inputId.value = id;
        inputJudul.value = judul;
        inputIsi.value = isi;
        inputTipe.value = tipe;
        inputExistingImage.value = gambar;
        formTitle.innerText = 'Edit Berita';
        btnShow.innerText = 'Tutup Form';

        if (gambar) {
            previewImg.src = '../uploads/news/' + gambar;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        formContainer.style.display = 'block';
        window.scrollTo(0, 0);
    }

    btnCancel.addEventListener('click', () => {
        formContainer.style.display = 'none';
        btnShow.innerText = 'Tambah Berita';
        form.reset();
    });
</script>

<?php include_once __DIR__ . '/../includes/table.php'; ?>