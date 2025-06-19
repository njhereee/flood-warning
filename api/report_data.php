<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Izinkan CORS untuk pengembangan

include "../connection.php"; // Sesuaikan path jika diperlukan

try {
    // Mengambil semua data banjir, diurutkan berdasarkan recorded_at DESC untuk laporan
    $stmt = $conn->prepare("
        SELECT
            fd.id,
            fd.region,
            fd.level,
            fd.status,
            fd.recorded_at,
            a.latitude,
            a.longitude,
            a.danger_level_m,
            a.type
        FROM
            flood_data fd
        LEFT JOIN
            areas a ON fd.region = a.name
        ORDER BY fd.recorded_at DESC
    ");
    $stmt->execute();
    $all_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung statistik ringkasan
    $totalAffectedAreas = 0;
    $totalWarnings = 0;
    $totalEvacuated = 0; // Dummy, harusnya dari DB jika ada tabel evacuations/summary
    $damagedHomes = 87; // Dummy, harusnya dari DB jika ada tabel damage_assessments
    $rescueTeams = 18;  // Dummy, harusnya dari DB

    $countedRegions = [];
    foreach ($all_reports as $report) {
        if (!in_array($report['region'], $countedRegions)) {
            $totalAffectedAreas++;
            $countedRegions[] = $report['region'];
        }
        if ($report['status'] === 'warning') {
            $totalWarnings++;
        }
        // Asumsi: setiap warning memicu evakuasi 100 orang
        // Ini dummy, Anda harus mendapatkan ini dari tabel `evacuations` jika ada
        if ($report['status'] === 'warning') {
             $totalEvacuated += 100;
        }
    }


    // Mengambil data Damage Assessment
    // Anda perlu membuat tabel 'damage_assessments' dan mengisinya dengan data
    // Untuk saat ini, saya akan menggunakan data dummy yang konsisten dengan frontend
    // ATAU: Ambil dari database jika tabel `damage_assessments` sudah ada dan terisi
    $stmt_damage = $conn->prepare("
        SELECT
            da.area,
            da.homes,
            da.infrastructure,
            da.agriculture,
            da.status
        FROM
            damage_assessments da -- Asumsi tabel ini sudah ada dan terisi
        ORDER BY
            da.report_date DESC -- Atau order lain yang relevan
    ");
    // $stmt_damage->execute();
    // $damage_assessments_from_db = $stmt_damage->fetchAll(PDO::FETCH_ASSOC);

    // Data dummy untuk damage_assessments jika tabel belum ada atau kosong
    $damage_assessments_data = [
        ['area' => 'Sukajadi', 'homes' => 15, 'infrastructure' => '1 bridge', 'agriculture' => '2 ha', 'status' => 'Critical'],
        ['area' => 'Rumbai', 'homes' => 10, 'infrastructure' => '2 roads', 'agriculture' => '1.5 ha', 'status' => 'Severe'],
        ['area' => 'Marpoyan Damai', 'homes' => 5, 'infrastructure' => 'No major', 'agriculture' => '0.5 ha', 'status' => 'Moderate'],
    ];
    // Ganti $damage_assessments_data dengan $damage_assessments_from_db jika Anda mengambil dari DB

    $response = [
        'reports' => $all_reports, // Data umum
        'summary' => [
            'affected_areas_count' => $totalAffectedAreas,
            'evacuated_count' => $totalEvacuated,
            'damaged_homes_count' => $damagedHomes,
            'rescue_teams_count' => $rescueTeams,
            'total_warnings' => $totalWarnings // Tambahkan total warnings
        ],
        'damage_assessment' => $damage_assessments_data, // Data penilaian kerusakan
        // Tambahan: Untuk peta insiden, Anda bisa menggunakan data 'reports' yang sudah ada
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("API Error in report_data.php: " . $e->getMessage());
    echo json_encode(["error" => "Database error: " . $e->getMessage(), "details" => $e->getMessage()]);
}
?>