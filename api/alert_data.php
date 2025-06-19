<?php
header('Content-Type: application/json');
include "../connection.php"; // <--- Ensure this path is correct

try {
    // Mengambil data level air terbaru untuk setiap region/sungai
    // Menggunakan subquery untuk memastikan hanya mengambil entry terbaru per region
   // Dalam api/alert_data.php
$stmt = $conn->prepare("
    SELECT
        fd.region,
        fd.level,
        fd.status, -- Pastikan kolom ini ada dan benar
        fd.recorded_at,
        a.latitude,
        a.longitude,
        a.danger_level_m,
        a.type
    FROM
        flood_data fd
    JOIN (
        SELECT
            region,
            MAX(recorded_at) AS max_recorded_at
        FROM
            flood_data
        GROUP BY
            region
    ) AS latest_fd ON fd.region = latest_fd.region AND fd.recorded_at = latest_fd.max_recorded_at
    LEFT JOIN
        areas a ON fd.region = a.name
    ORDER BY
        fd.status DESC, fd.level DESC
");
    $stmt->execute();
    $alertData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mengelompokkan data untuk "Flood Level Monitoring" secara spesifik (misal, untuk 3 sungai)
    $riverLevels = [];
    foreach ($alertData as $item) {
        if ($item['type'] === 'river') { // Make sure 'type' column exists and is populated in 'areas' table
            $riverLevels[] = $item;
        }
    }

    echo json_encode([
        'alerts' => $alertData,
        'river_levels' => $riverLevels
    ]);

} catch (PDOException $e) {
    error_log("API Error in alert_data.php: " . $e->getMessage());
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>