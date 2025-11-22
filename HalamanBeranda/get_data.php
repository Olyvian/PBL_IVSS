<?php
// Sertakan file konfigurasi koneksi
require_once 'db_config.php'; // Ini akan membuat objek $pdo (PDO PostgreSQL)

// Atur header agar klien tahu responsnya adalah JSON
header('Content-Type: application/json');

// Inisialisasi array respons
$response = [
    'vision' => 'Data Visi belum tersedia.',
    'mission' => [],
    'activities' => [],
    'lecturers' => [],
    'facilities' => []
];

try {
    // ===================================
    // 1. Ambil Data Visi (Menggunakan PDO)
    // ===================================
    $stmt_vision = $pdo->prepare("SELECT content FROM visions LIMIT 1");
    $stmt_vision->execute();
    $vision_row = $stmt_vision->fetch(); 

    if ($vision_row) {
        $response['vision'] = $vision_row['content'];
    }

    // ===================================
    // 2. Ambil Data Misi (Menggunakan PDO)
    // ===================================
    $stmt_mission = $pdo->prepare("SELECT point FROM missions ORDER BY id ASC");
    $stmt_mission->execute();
    $mission_list = $stmt_mission->fetchAll();

    foreach($mission_list as $row) {
        $response['mission'][] = $row['point'];
    }

    // ===================================
    // 3. Ambil Data Activities/Kegiatan (Menggunakan PDO)
    // ===================================
    $stmt_activities = $pdo->prepare("SELECT title, description FROM activities ORDER BY id DESC LIMIT 3"); 
    $stmt_activities->execute();
    $response['activities'] = $stmt_activities->fetchAll();

    // ===================================
    // 4. Ambil Data Lecturers/Dosen (Menggunakan PDO)
    // ===================================
    $stmt_lecturers = $pdo->prepare("SELECT name, expertise, image_url FROM lecturers ORDER BY id ASC");
    $stmt_lecturers->execute();
    $response['lecturers'] = $stmt_lecturers->fetchAll();


    // ===================================
    // 5. Ambil Data Facilities/Fasilitas (Menggunakan PDO)
    // ===================================
    $stmt_facilities = $pdo->prepare("SELECT name, description FROM facilities ORDER BY id ASC");
    $stmt_facilities->execute();
    $response['facilities'] = $stmt_facilities->fetchAll();

} catch (PDOException $e) {
    // Tangani error database dengan aman
    http_response_code(500);
    // Kirim pesan error yang aman ke klien. Pesan $e->getMessage() di-log di sisi server.
    $response = ['error' => 'Database error', 'message' => 'Gagal memuat data dari server.'];
}


// Keluarkan hasil dalam format JSON
echo json_encode($response);
?>