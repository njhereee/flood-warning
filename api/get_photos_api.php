<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include "../connection.php"; // Sesuaikan path jika diperlukan

try {
    // Mengambil semua foto, diurutkan berdasarkan tanggal upload terbaru
    $stmt = $conn->prepare("SELECT id, region, file_path, thumbnail_path, description, taken_at, uploaded_at FROM flood_photos ORDER BY uploaded_at DESC");
    $stmt->execute();
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['photos' => $photos]);

} catch (PDOException $e) {
    error_log("API Error in get_photos_api.php: " . $e->getMessage());
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>