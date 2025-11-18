<?php
// Sertakan file konfigurasi koneksi
require_once 'db_config.php';

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

// ===================================
// 1. Ambil Data Visi
// ===================================
// Asumsi: Hanya ada 1 baris untuk Visi
$sql_vision = "SELECT content FROM visions LIMIT 1";
$result_vision = $conn->query($sql_vision);

if ($result_vision && $result_vision->num_rows > 0) {
    $row = $result_vision->fetch_assoc();
    $response['vision'] = $row['content'];
}

// ===================================
// 2. Ambil Data Misi
// ===================================
$sql_mission = "SELECT point FROM missions ORDER BY id ASC";
$result_mission = $conn->query($sql_mission);

if ($result_mission && $result_mission->num_rows > 0) {
    while($row = $result_mission->fetch_assoc()) {
        $response['mission'][] = $row['point'];
    }
}

// ===================================
// 3. Ambil Data Activities/Kegiatan
// ===================================
$sql_activities = "SELECT title, description FROM activities ORDER BY id DESC LIMIT 3"; // Batasi 3 untuk contoh
$result_activities = $conn->query($sql_activities);

if ($result_activities && $result_activities->num_rows > 0) {
    while($row = $result_activities->fetch_assoc()) {
        $response['activities'][] = $row;
    }
}

// ===================================
// 4. Ambil Data Lecturers/Dosen
// ===================================
$sql_lecturers = "SELECT name, expertise, image_url FROM lecturers ORDER BY id ASC";
$result_lecturers = $conn->query($sql_lecturers);

if ($result_lecturers && $result_lecturers->num_rows > 0) {
    while($row = $result_lecturers->fetch_assoc()) {
        $response['lecturers'][] = $row;
    }
}

// ===================================
// 5. Ambil Data Facilities/Fasilitas
// ===================================
$sql_facilities = "SELECT name, description FROM facilities ORDER BY id ASC";
$result_facilities = $conn->query($sql_facilities);

if ($result_facilities && $result_facilities->num_rows > 0) {
    while($row = $result_facilities->fetch_assoc()) {
        $response['facilities'][] = $row;
    }
}

// Tutup koneksi
$conn->close();

// Output data sebagai JSON
echo json_encode($response);
?>