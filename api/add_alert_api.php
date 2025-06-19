<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); 

// Tangani request OPTIONS (preflight) untuk CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

// Sesuaikan path ke file koneksi database Anda
// Pastikan file ini aman dan tidak dapat diakses langsung dari web.
// Sebaiknya letakkan di luar root dokumen web jika memungkinkan.
include "../connection.php"; // Misalnya: /var/www/includes/connection.php

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Only POST requests are accepted.']);
    exit;
}

// Ambil data JSON dari body request
$input = json_decode(file_get_contents('php://input'), true);

// Periksa apakah JSON valid
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

// Ambil dan trim input untuk menghindari spasi ekstra
$region = isset($input['region']) ? trim($input['region']) : null;
$level = isset($input['level']) ? $input['level'] : null; // Level akan divalidasi sebagai numerik nanti
$status = isset($input['status']) ? trim(strtolower($input['status'])) : null; // Ubah ke huruf kecil untuk konsistensi

// Validasi data
$errors = [];
if (empty($region)) {
    $errors[] = 'Region is required.';
}
if ($level === null) { // Periksa null secara eksplisit karena 0 adalah level yang valid
    $errors[] = 'Water level (level) is required.';
} elseif (!is_numeric($level)) {
    $errors[] = 'Water level must be a numeric value.';
}
if (empty($status)) {
    $errors[] = 'Status is required.';
} else {
    $allowed_statuses = ['update', 'watch', 'warning'];
    if (!in_array($status, $allowed_statuses)) {
        $errors[] = 'Invalid status value. Allowed values are: ' . implode(', ', $allowed_statuses) . '.';
    }
}

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    // Pastikan variabel $conn ada dan merupakan objek PDO yang valid dari connection.php
    if (!isset($conn) || !$conn instanceof PDO) {
        error_log("Database connection is not available in add_alert_api.php.");
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Internal Server Error: Database connection failed.']);
        exit;
    }

    // Masukkan data ke tabel flood_data
    // Menggunakan NOW() untuk kolom recorded_at agar waktu dicatat oleh server database
    $stmt = $conn->prepare("INSERT INTO flood_data (region, level, status, recorded_at) VALUES (:region, :level, :status, NOW())");
    
    // Bind parameters
    $stmt->bindParam(':region', $region, PDO::PARAM_STR);
    $stmt->bindParam(':level', $level); // PDO akan menangani tipe data numerik
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            http_response_code(201); // Created
            echo json_encode(['success' => true, 'message' => 'Flood alert added successfully!']);
        } else {
            // Ini seharusnya jarang terjadi jika execute berhasil tanpa exception tapi rowCount 0
            http_response_code(500); 
            echo json_encode(['success' => false, 'error' => 'Failed to insert data: No rows affected.']);
        }
    } else {
        // Jika execute mengembalikan false (jarang untuk PDO dengan error mode exception)
        $errorInfo = $stmt->errorInfo();
        error_log("Database insert failed in add_alert_api.php: " . ($errorInfo[2] ?? 'Unknown error'));
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to execute database statement.']);
    }

} catch (PDOException $e) {
    error_log("Database error adding alert in add_alert_api.php: " . $e->getMessage());
    http_response_code(500);
    // Untuk produksi, sebaiknya jangan tampilkan $e->getMessage() secara langsung ke klien
    // Cukup pesan error generik.
    echo json_encode(['success' => false, 'error' => 'Database operation failed. Please try again later.']);
    // echo json_encode(['success' => false, 'error' => 'Database operation failed: ' . $e->getMessage()]); // Untuk development
} catch (Exception $e) {
    // Menangkap exception umum lainnya
    error_log("General error in add_alert_api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An unexpected error occurred.']);
}
?>
