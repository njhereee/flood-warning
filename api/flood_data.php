<?php
header('Content-Type: application/json');
include "../connection.php"; // Adjust path if necessary

try {
    // 1. Fetch all flood data ordered by recorded_at ascending for chart trends
    // This is used by index.php for the chart.
    $stmt_all_data = $conn->prepare("
        SELECT
            fd.id,
            fd.region,
            fd.level,
            fd.status,
            fd.recorded_at,
            a.latitude,
            a.longitude,
            a.danger_level_m
        FROM
            flood_data fd
        LEFT JOIN
            areas a ON fd.region = a.name
        ORDER BY fd.recorded_at ASC
        LIMIT 100 -- Fetch enough historical data for charts
    ");
    $stmt_all_data->execute();
    $allFloodData = $stmt_all_data->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch LATEST flood data for each region for "Flood Level Monitoring" & Map Markers
    // This query finds the latest entry for each distinct region
    $stmt_latest_per_region = $conn->prepare("
        SELECT
            fd.region,
            fd.level,
            fd.status,
            fd.recorded_at,
            a.latitude,
            a.longitude,
            a.danger_level_m
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
        ) AS latest_records ON fd.region = latest_records.region AND fd.recorded_at = latest_records.max_recorded_at
        LEFT JOIN
            areas a ON fd.region = a.name
        ORDER BY fd.region ASC
    ");
    $stmt_latest_per_region->execute();
    $latestFloodDataPerRegion = $stmt_latest_per_region->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch summary statistics
    $stmtWarnings = $conn->prepare("SELECT COUNT(*) AS total_warnings FROM flood_data WHERE status = 'warning'");
    $stmtWarnings->execute();
    $totalWarnings = $stmtWarnings->fetch(PDO::FETCH_ASSOC)['total_warnings'];

    $stmtAffectedAreas = $conn->prepare("SELECT COUNT(DISTINCT region) AS total_affected_areas FROM flood_data");
    $stmtAffectedAreas->execute();
    $totalAffectedAreas = $stmtAffectedAreas->fetch(PDO::FETCH_ASSOC)['total_affected_areas'];

    // Dummy evacuated people for index.php dashboard. In a real system, fetch from DB.
    $evacuated_people_count = 100; // Placeholder value

    $response = [
        'all_reports' => $allFloodData, // Used for charts (index.php) and historical viewing
        'latest_per_region' => $latestFloodDataPerRegion, // Used for current gauges and map markers (alert.php)
        'summary' => [ // Used for dashboard stats (index.php)
            'affected_areas_count' => $totalAffectedAreas,
            'evacuated_count' => $evacuated_people_count,
            'total_warnings' => $totalWarnings
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("API Error in flood_data.php: " . $e->getMessage()); // Log error to server
    // Always return the expected structure, even on error
    echo json_encode([
        'all_reports' => [],
        'latest_per_region' => [],
        'summary' => [
            'affected_areas_count' => 0,
            'evacuated_count' => 0,
            'total_warnings' => 0
        ],
        'error' => "Database error: " . $e->getMessage()
    ]);
}
?>