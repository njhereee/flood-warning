<?php
require_once 'vendor/autoload.php'; // This line is essential for Dompdf
include "connection.php"; // Adjust path if necessary for connection.php

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch data from the database
try {
    $stmt = $conn->prepare("SELECT region, level, status, recorded_at FROM flood_data ORDER BY recorded_at DESC");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch summary data
    $stmtWarnings = $conn->prepare("SELECT COUNT(*) AS total_warnings FROM flood_data WHERE status = 'warning'");
    $stmtWarnings->execute();
    $totalWarnings = $stmtWarnings->fetch(PDO::FETCH_ASSOC)['total_warnings'];

    $stmtAffectedAreas = $conn->prepare("SELECT COUNT(DISTINCT region) AS total_affected_areas FROM flood_data");
    $stmtAffectedAreas->execute();
    $totalAffectedAreas = $stmtAffectedAreas->fetch(PDO::FETCH_ASSOC)['total_affected_areas'];

    // Dummy data for evacuated, damaged homes, rescue teams (replace with real data if you have tables for them)
    $evacuated_count = 1248;
    $damaged_homes_count = 87;
    $rescue_teams_count = 18;

    // Dummy data for damage assessment table (replace with real data if you have a table for it)
    $damage_assessment = [
        ['area' => 'Central District', 'homes' => 48, 'infrastructure' => '3 bridges', 'agriculture' => '12 ha', 'status' => 'Critical'],
        ['area' => 'Riverside Area', 'homes' => 32, 'infrastructure' => '5 roads', 'agriculture' => '8 ha', 'status' => 'Severe'],
        ['area' => 'North Suburb', 'homes' => 7, 'infrastructure' => '1 road', 'agriculture' => '2 ha', 'status' => 'Moderate'],
    ];

} catch (PDOException $e) {
    // Log the error and return an error message to the browser
    error_log("Error fetching data for PDF: " . $e->getMessage());
    die("Error generating report: Could not retrieve data. Please check logs for details.");
}

// Generate HTML content for the PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flood Incident Report</title>
    <style>
        body { font-family: sans-serif; margin: 20px; font-size: 10pt; }
        h1, h2, h3 { color: #2C5282; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #E0E7FF; }
        .summary-box {
            border: 1px solid #BEE3F8;
            background-color: #EBF8FF;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .summary-item {
            flex: 1;
            min-width: 150px;
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-item h3 { font-size: 18pt; margin: 0; color: #2C5282; }
        .summary-item p { font-size: 8pt; margin-top: 5px; color: #4A5568; }
        .status-critical { background-color: #FED7D7; color: #C53030; padding: 3px 8px; border-radius: 3px; font-size: 8pt;}
        .status-severe { background-color: #FEEBC8; color: #D69E2E; padding: 3px 8px; border-radius: 3px; font-size: 8pt;}
        .status-moderate { background-color: #FEF3C7; color: #DDA606; padding: 3px 8px; border-radius: 3px; font-size: 8pt;}
        .status-watch { background-color: #E0E7FF; color: #2C5282; padding: 3px 8px; border-radius: 3px; font-size: 8pt;}
        .status-update { background-color: #C6F6D5; color: #2F855A; padding: 3px 8px; border-radius: 3px; font-size: 8pt;}

        .notes-box {
            background-color: #EBF8FF;
            border: 1px solid #BEE3F8;
            padding: 15px;
            border-radius: 5px;
            font-size: 9pt;
        }
        .notes-box h3 { color: #2C5282; margin-top: 0;}
        .notes-box ul { list-style: disc; margin-left: 20px;}
    </style>
</head>
<body>
    <h1>Flood Incident Report - Pekanbaru</h1>
    <p>Date: ' . date('Y-m-d H:i:s') . '</p>

    <h2>Summary Statistics</h2>
    <div class="summary-box">
        <div class="summary-item">
            <p>Affected Areas</p>
            <h3>' . htmlspecialchars($totalAffectedAreas) . '</h3>
        </div>
        <div class="summary-item">
            <p>Evacuated</p>
            <h3>' . htmlspecialchars($evacuated_count) . '</h3>
        </div>
        <div class="summary-item">
            <p>Damaged Homes</p>
            <h3>' . htmlspecialchars($damaged_homes_count) . '</h3>
        </div>
        <div class="summary-item">
            <p>Rescue Teams</p>
            <h3>' . htmlspecialchars($rescue_teams_count) . '</h3>
        </div>
    </div>

    <h2>Recent Flood Data</h2>
    <table>
        <thead>
            <tr>
                <th>Region</th>
                <th>Level (m)</th>
                <th>Status</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>';
        if (!empty($reports)) {
            foreach ($reports as $report) {
                $statusClass = '';
                if ($report['status'] == 'warning') {
                    $statusClass = 'status-critical';
                } elseif ($report['status'] == 'watch') {
                    $statusClass = 'status-severe';
                } elseif ($report['status'] == 'update') {
                    $statusClass = 'status-update';
                }
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($report['region']) . '</td>
                    <td>' . htmlspecialchars($report['level']) . '</td>
                    <td><span class="' . $statusClass . '">' . htmlspecialchars(ucfirst($report['status'])) . '</span></td>
                    <td>' . htmlspecialchars($report['recorded_at']) . '</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="4">No flood data available.</td></tr>';
        }
        $html .= '
        </tbody>
    </table>

    <h2>Damage Assessment</h2>
    <table>
        <thead>
            <tr>
                <th>Area</th>
                <th>Homes</th>
                <th>Infrastructure</th>
                <th>Agriculture</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
        if (!empty($damage_assessment)) {
            foreach ($damage_assessment as $damage) {
                $statusClass = '';
                if ($damage['status'] == 'Critical') {
                    $statusClass = 'status-critical';
                } elseif ($damage['status'] == 'Severe') {
                    $statusClass = 'status-severe';
                } elseif ($damage['status'] == 'Moderate') {
                    $statusClass = 'status-moderate';
                }
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($damage['area']) . '</td>
                    <td>' . htmlspecialchars($damage['homes']) . '</td>
                    <td>' . htmlspecialchars($damage['infrastructure']) . '</td>
                    <td>' . htmlspecialchars($damage['agriculture']) . '</td>
                    <td><span class="' . $statusClass . '">' . htmlspecialchars($damage['status']) . '</span></td>
                </tr>';
            }
        } else {
             $html .= '<tr><td colspan="5">No damage assessment data available.</td></tr>';
        }
        $html .= '
        </tbody>
    </table>

    <div class="notes-box">
        <h3>Assessment Notes</h3>
        <ul>
            <li>Damage assessments are preliminary and may change as waters recede</li>
            <li>Agricultural losses include rice fields and vegetable crops</li>
            <li>Infrastructure damage estimates include roads and bridges</li>
        </ul>
    </div>

    <p style="text-align: center; margin-top: 30px; font-size: 8pt; color: #A0AEC0;">&copy; ' . date('Y') . ' Flood Alert System. Report ID: FLD-RPT-' . date('Y-m-d') . '</p>
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Enable loading of remote CSS/images if needed, though for this report, it's not strictly necessary.

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Flood_Report_" . date('Y-m-d_H-i') . ".pdf", ["Attachment" => true]);
?>