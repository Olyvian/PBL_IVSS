<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Proteksi Halaman (Hanya Admin Lab)
// redirectIfNotLoggedIn(['admin_lab']); 

$pageTitle = 'Manajemen Produk';
$activePage = 'produk';

// --- 1. LOGIKA SIMPAN (TAMBAH & EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $deskripsi   = $_POST['deskripsi'];
    $gambar      = null;

    // Handle Upload Gambar
    if (!empty($_FILES['gambar']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $uploadPath = __DIR__ . '/../uploads/produk/' . $filename;
        
        // Buat folder jika belum ada
        if (!is_dir(__DIR__ . '/../uploads/produk/')) {
            mkdir(__DIR__ . '/../uploads/produk/', 0777, true);
        }

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
            $gambar = $filename;
        }
    }

    if (!empty($_POST['produk_id'])) {
        // Mode EDIT
        $id = $_POST['produk_id'];
        
        if ($gambar === null) {
            $gambar = $_POST['existing_gambar'] ?? null;
        }

        // Query Update
        $stmt = $pdo->prepare("UPDATE produk SET nama_produk=?, deskripsi=?, gambar=? WHERE id=?");
        $stmt->execute([$nama_produk, $deskripsi, $gambar, $id]);

    } else {
        // Mode TAMBAH
        $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, deskripsi, gambar) VALUES (?, ?, ?)");
        $stmt->execute([$nama_produk, $deskripsi, $gambar]);
    }
    
    header('Location: dashboard_produk.php'); 
    exit;
}

// --- 2. LOGIKA HAPUS ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus file fisik
    $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    if ($old && $old['gambar']) {
        $path = __DIR__ . "/../uploads/produk/" . $old['gambar'];
        if (file_exists($path)) unlink($path);
    }

    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: dashboard_produk.php'); 
    exit;
}

// --- 3. AMBIL DATA ---
$stmt = $pdo->query("SELECT * FROM produk ORDER BY id DESC");
$list = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Produk & Inovasi</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Produk</button>
        <button class="btn btn-secondary" id="btnCancelForm" style="display:none;">Tutup Form</button>
    </div>

    <div class="form-container" id="formContainer" style="display:none;">
        <h4 id="formTitle">Tambah Produk Baru</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="produk_id" id="produk_id">
            <input type="hidden" name="existing_gambar" id="existing_gambar">
            
            <div class="form-group">
                <label for="nama_produk">Nama Produk / Inovasi:</label>
                <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="image_input">Gambar Produk:</label>
                <input type="file" name="gambar" id="image_input" class="form-control">
            </div>

            <div id="image_preview_container" style="display:none; margin-top:10px;">
                <p>Preview:</p>
                <img id="image_preview" src="" alt="Preview" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="width: 200px; text-align: center;">Gambar</th>
                    <th style="width: 25%;">Nama Produk</th>
                    <th>Deskripsi</th> <th style="width: 150px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr><td colspan="4" class="empty-table">Belum ada produk ditambahkan.</td></tr>
                <?php endif; ?>

                <?php foreach ($list as $item): ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="../uploads/produk/<?= htmlspecialchars($item['gambar']) ?>" 
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                        <?php else: ?>
                            <span class="table-thumbnail-placeholder">No Img</span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="font-weight: 600; color: #0D4C7C; vertical-align: middle;">
                        <?= htmlspecialchars($item['nama_produk']) ?>
                    </td>
                    
                    <td style="color: #555; font-size: 0.9rem; line-height: 1.5; vertical-align: middle;">
                        <?= substr(htmlspecialchars($item['deskripsi']), 0, 150) ?>...
                    </td>
                    
                    <td class="table-aksi-col" style="text-align: center; vertical-align: middle;">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editProduk(
                               <?= $item['id'] ?>, 
                               <?= json_encode($item['nama_produk']) ?>, 
                               <?= json_encode($item['deskripsi']) ?>, 
                               <?= json_encode($item['gambar'] ?? '') ?>
                           ); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_produk.php?delete=<?= $item['id'] ?>" class="btn-icon btn-delete" title="Hapus"
                           onclick="return confirm('Hapus produk ini?')">
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
    const formContainer = document.getElementById('formContainer');
    const btnShow = document.getElementById('btnShowForm');
    const btnCancel = document.getElementById('btnCancelForm');
    const formTitle = document.getElementById('formTitle');
    
    // Inputs
    const inputId = document.getElementById('produk_id');
    const inputNama = document.getElementById('nama_produk');
    const inputDeskripsi = document.getElementById('deskripsi');
    const inputExistingGambar = document.getElementById('existing_gambar');
    
    // Image Preview
    const inputImage = document.getElementById('image_input');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    // Tampilkan Form Tambah
    btnShow.addEventListener('click', () => {
        inputId.value = '';
        inputNama.value = '';
        inputDeskripsi.value = '';
        inputExistingGambar.value = '';
        inputImage.value = '';
        
        previewContainer.style.display = 'none';
        formTitle.innerText = 'Tambah Produk Baru';
        
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });

    // Sembunyikan Form
    btnCancel.addEventListener('click', () => {
        formContainer.style.display = 'none';
        btnShow.style.display = 'inline-block';
        btnCancel.style.display = 'none';
    });

    // Edit Data
    function editProduk(id, nama, deskripsi, gambar) {
        inputId.value = id;
        inputNama.value = nama;
        inputDeskripsi.value = deskripsi;
        inputExistingGambar.value = gambar;

        formTitle.innerText = 'Edit Produk';
        
        if (gambar) {
            previewImg.src = '../uploads/produk/' + gambar;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }
        
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }

    // Preview Gambar saat Upload Baru
    inputImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
        }
    });
</script>
