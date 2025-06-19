<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include "../connection.php"; // Sesuaikan path jika diperlukan

try {
    // Mengambil semua nama wilayah (kelurahan/kecamatan) dari tabel 'areas'
    // Filter hanya tipe 'district' jika Anda tidak ingin sungai di daftar ini
    $stmt = $conn->prepare("SELECT name FROM areas WHERE type = 'district' ORDER BY name ASC");
    $stmt->execute();
    $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['regions' => $regions]);

} catch (PDOException $e) {
    error_log("API Error in get_regions.php: " . $e->getMessage());
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>