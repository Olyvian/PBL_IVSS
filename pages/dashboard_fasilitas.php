<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Proteksi Halaman
// redirectIfNotLoggedIn(['admin_lab']); 

$pageTitle = 'Manajemen Fasilitas';
$activePage = 'fasilitas';

// --- 1. LOGIKA SIMPAN (TAMBAH & EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $jenis = $_POST['jenis'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = null;

    // Handle Upload Gambar
    if (!empty($_FILES['gambar']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $uploadPath = __DIR__ . '/../uploads/fasilitas/' . $filename;
        
        // Buat folder jika belum ada
        if (!is_dir(__DIR__ . '/../uploads/fasilitas/')) {
            mkdir(__DIR__ . '/../uploads/fasilitas/', 0777, true);
        }

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
            $gambar = $filename;
        }
    }

    if (!empty($_POST['fasilitas_id'])) {
        // Mode Edit
        $id = $_POST['fasilitas_id'];
        
        // Jika tidak upload gambar baru, pakai yang lama
        if ($gambar === null) {
            $gambar = $_POST['existing_gambar'] ?? null;
        }

        $stmt = $pdo->prepare("UPDATE fasilitas_peralatan SET judul=?, jenis=?, deskripsi=?, gambar=? WHERE id=?");
        $stmt->execute([$judul, $jenis, $deskripsi, $gambar, $id]);
    } else {
        // Mode Tambah
        $stmt = $pdo->prepare("INSERT INTO fasilitas_peralatan (judul, jenis, deskripsi, gambar) VALUES (?, ?, ?, ?)");
        $stmt->execute([$judul, $jenis, $deskripsi, $gambar]);
    }
    
    header('Location: dashboard_fasilitas.php');
    exit;
}

// --- 2. LOGIKA HAPUS ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus file fisik gambar lama
    $stmt = $pdo->prepare("SELECT gambar FROM fasilitas_peralatan WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    if ($old && $old['gambar']) {
        $path = __DIR__ . "/../uploads/fasilitas/" . $old['gambar'];
        if (file_exists($path)) unlink($path);
    }

    $stmt = $pdo->prepare("DELETE FROM fasilitas_peralatan WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_fasilitas.php');
    exit;
}

// --- 3. AMBIL DATA ---
$stmt = $pdo->query("SELECT * FROM fasilitas_peralatan ORDER BY jenis ASC, judul ASC");
$list = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Fasilitas & Alat</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Fasilitas</button>
        <button class="btn btn-secondary" id="btnCancelForm" style="display:none;">Tutup Form</button>
    </div>

    <div class="form-container" id="formContainer" style="display:none;">
        <h4 id="formTitle">Tambah Fasilitas Baru</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="fasilitas_id" id="fasilitas_id">
            <input type="hidden" name="existing_gambar" id="existing_gambar">
            
            <div class="form-group">
                <label for="judul">Nama Fasilitas / Alat:</label>
                <input type="text" name="judul" id="judul" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="jenis">Jenis:</label>
                <select name="jenis" id="jenis" class="form-control" required>
                    <option value="Fasilitas Utama">Fasilitas Utama</option>
                    <option value="Peralatan">Peralatan (Hardware)</option>
                    <option value="Software">Software / Tools</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image_input">Gambar Fasilitas:</label>
                <input type="file" name="gambar" id="image_input" class="form-control">
            </div>

            <div id="image_preview_container" style="display:none; margin-top:10px;">
                <p>Preview Gambar:</p>
                <img id="image_preview" src="" alt="Preview" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th style="width: 100px;">Gambar</th>
                    <th>Nama Fasilitas</th>
                    <th>Jenis</th>
                    <th>Deskripsi</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr><td colspan="5" class="empty-table">Belum ada data fasilitas.</td></tr>
                <?php endif; ?>

                <?php foreach ($list as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="../uploads/fasilitas/<?= htmlspecialchars($item['gambar']) ?>" 
                                 style="width: 100px; height: 80px; object-fit: cover; border-radius: 5px;">
                        <?php else: ?>
                            <span class="table-thumbnail-placeholder">No Img</span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="font-weight: 600;"><?= htmlspecialchars($item['judul']) ?></td>
                    
                    <td>
                            <?= htmlspecialchars($item['jenis']) ?>
                    </td>

                    <td style="color: #555;">
                        <?= htmlspecialchars($item['deskripsi']) ?>
                    </td>
                    
                    <td class="table-aksi-col">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editFasilitas(
                               <?= $item['id'] ?>, 
                               <?= json_encode($item['judul']) ?>, 
                               <?= json_encode($item['jenis']) ?>, 
                               <?= json_encode($item['deskripsi']) ?>, 
                               <?= json_encode($item['gambar'] ?? '') ?>
                           ); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_fasilitas.php?delete=<?= $item['id'] ?>" class="btn-icon btn-delete" title="Hapus"
                           onclick="return confirm('Hapus fasilitas ini?')">
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
    const inputId = document.getElementById('fasilitas_id');
    const inputJudul = document.getElementById('judul');
    const inputJenis = document.getElementById('jenis');
    const inputDeskripsi = document.getElementById('deskripsi');
    const inputExistingGambar = document.getElementById('existing_gambar');
    
    // Image Preview
    const inputImage = document.getElementById('image_input');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    // Tampilkan Form Tambah
    btnShow.addEventListener('click', () => {
        // Reset Form
        inputId.value = '';
        inputJudul.value = '';
        inputDeskripsi.value = '';
        inputExistingGambar.value = '';
        inputImage.value = '';
        
        previewContainer.style.display = 'none';
        formTitle.innerText = 'Tambah Fasilitas Baru';
        
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

    // Edit Data (Dipanggil tombol pensil)
    function editFasilitas(id, judul, jenis, deskripsi, gambar) {
        inputId.value = id;
        inputJudul.value = judul;
        inputJenis.value = jenis;
        inputDeskripsi.value = deskripsi;
        inputExistingGambar.value = gambar;

        formTitle.innerText = 'Edit Fasilitas';
        
        // Handle Preview Gambar Lama
        if (gambar) {
            previewImg.src = '../uploads/fasilitas/' + gambar;
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