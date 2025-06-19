<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 


$adm4_pekanbaru = "14.71.01.1002"; 

$api_url = "$ curl https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={$adm4_pekanbaru}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response_body = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($http_code != 200 || $response_body === false || $curl_error) {
    error_log("BMKG API fetch failed: HTTP Code {$http_code}, cURL Error: {$curl_error}");
    echo json_encode(["error" => "Failed to fetch data from BMKG API: " . ($curl_error ? $curl_error : "HTTP {$http_code}")]);
    exit;
}

$data = json_decode($response_body, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    error_log("BMKG API JSON decode error: " . json_last_error_msg() . " | Raw response: " . $response_body);
    echo json_encode(["error" => "Invalid JSON format from BMKG API: " . json_last_error_msg()]);
    exit;
}


$weather_output = [
    'weather_description' => 'N/A',
    'weather_icon_code' => 'fas fa-question',
    'current_temp' => 'N/A',
    'min_temp' => 'N/A',
    'max_temp' => 'N/A',
    'wind_speed' => 'N/A',
    'humidity' => 'N/A',
    'daily_forecast' => [],
    'rainfall_24h' => 'N/A',
    'expected_rainfall_24h' => 'N/A',
    'river_levels_danger' => 'N/A'
];

if (isset($data["lokasi"]) && isset($data["data"]) && is_array($data["data"])) {
    $lokasi = $data["lokasi"];
    $cuaca_harian_array = $data["data"][0]["cuaca"] ?? [];

    $now_timestamp = time();
    $closest_forecast = null;
    $closest_diff = PHP_INT_MAX;

    if (!empty($cuaca_harian_array) && !empty($cuaca_harian_array[0])) {
        foreach ($cuaca_harian_array[0] as $hour_forecast) {
            if (isset($hour_forecast['local_datetime'])) {
                try {
                    $forecast_datetime = new DateTime($hour_forecast['local_datetime']);
                    $forecast_timestamp = $forecast_datetime->getTimestamp();
                    $diff = abs($now_timestamp - $forecast_timestamp);

                    if ($diff < $closest_diff) {
                        $closest_diff = $diff;
                        $closest_forecast = $hour_forecast;
                    }
                } catch (Exception $e) {
                    error_log("Error parsing date in BMKG data for closest forecast: " . $e->getMessage());
                }
            }
        }
    }

    if ($closest_forecast) {
        $weather_output['current_temp'] = $closest_forecast['t'] ?? 'N/A';
        $weather_output['weather_description'] = $closest_forecast['weather_desc'] ?? 'N/A';
        $weather_output['wind_speed'] = $closest_forecast['ws'] ?? 'N/A';
        $weather_output['humidity'] = $closest_forecast['hu'] ?? 'N/A';

        $bmkg_weather_code = $closest_forecast['weather_code'] ?? null;
        $weather_output['weather_icon_code'] = getFontAwesomeIconFromBMKGCode($bmkg_weather_code, $closest_forecast['weather_desc'] ?? '');
    } else {
        error_log("No current/closest weather forecast found for today.");
    }
    
    $days_of_week_short = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    $today_temps_all_hours = [];
    if (isset($cuaca_harian_array[0]) && is_array($cuaca_harian_array[0])) {
        foreach ($cuaca_harian_array[0] as $hour_forecast) {
            if (isset($hour_forecast['t'])) {
                $today_temps_all_hours[] = (float)$hour_forecast['t'];
            }
        }
    }
    $weather_output['min_temp'] = !empty($today_temps_all_hours) ? min($today_temps_all_hours) : 'N/A';
    $weather_output['max_temp'] = !empty($today_temps_all_hours) ? max($today_temps_all_hours) : 'N/A';


    for ($i = 0; $i < min(4, count($cuaca_harian_array)); $i++) {
        $day_forecast_entries = $cuaca_harian_array[$i];
        if (!empty($day_forecast_entries)) {
            $temps_for_day = [];
            foreach ($day_forecast_entries as $hour_forecast) {
                if (isset($hour_forecast['t'])) {
                    $temps_for_day[] = (float)$hour_forecast['t'];
                }
            }
            $min_temp_for_day = !empty($temps_for_day) ? min($temps_for_day) : 'N/A';
            $max_temp_for_day = !empty($temps_for_day) ? max($temps_for_day) : 'N/A';

            $first_hour_of_day = $day_forecast_entries[0] ?? null;
            $day_desc = $first_hour_of_day['weather_desc'] ?? 'N/A';
            $day_bmkg_code = $first_hour_of_day['weather_code'] ?? null;
            $day_icon_fa = getFontAwesomeIconFromBMKGCode($day_bmkg_code, $day_desc);

            $display_day_name = 'N/A';
            if ($first_hour_of_day && isset($first_hour_of_day['local_datetime'])) {
                try {
                    $date_obj = new DateTime($first_hour_of_day['local_datetime']);
                    $day_name_index = (int)$date_obj->format('w');
                    $display_day_name = $days_of_week_short[$day_name_index] ?? 'N/A';
                } catch (Exception $e) {
                    error_log("Error parsing date for daily forecast: " . $e->getMessage());
                }
            } else {
                $relative_day_index = (gmdate('w') + $i) % 7;
                $display_day_name = $days_of_week_short[$relative_day_index] ?? 'N/A';
            }

            $weather_output['daily_forecast'][] = [
                'day' => $display_day_name,
                'min_temp' => $min_temp_for_day,
                'max_temp' => $max_temp_for_day,
                'description' => $day_desc,
                'icon_fa' => $day_icon_fa
            ];
        }
    }
    

    $weather_output['rainfall_24h'] = "12 mm"; 
    $weather_output['expected_rainfall_24h'] = "18 mm"; 
    $weather_output['river_levels_danger'] = "0.2 m di bawah normal"; 

} else {
    error_log("BMKG API response missing 'lokasi' or 'data' key or empty.");
    $weather_output['error'] = "Weather data structure not as expected from BMKG or no data.";
    $weather_output['rainfall_24h'] = $weather_output['rainfall_24h'] ?? 'N/A';
    $weather_output['expected_rainfall_24h'] = $weather_output['expected_rainfall_24h'] ?? 'N/A';
    $weather_output['river_levels_danger'] = $weather_output['river_levels_danger'] ?? 'N/A';
}

echo json_encode($weather_output);


function getFontAwesomeIconFromBMKGCode($bmkgCode, $description) {
    $map = [
        '0' => 'fas fa-sun',
        '1' => 'fas fa-cloud-sun',
        '2' => 'fas fa-cloud',
        '3' => 'fas fa-cloud',
        '4' => 'fas fa-smog',
        '5' => 'fas fa-cloud-rain', // Hujan Ringan (dari deskripsi)
        '60' => 'fas fa-cloud-rain', // Hujan Ringan (dari kode BMKG)
        '61' => 'fas fa-cloud-showers-heavy', // Hujan Sedang (dari kode BMKG)
        '63' => 'fas fa-cloud-showers-heavy', // Hujan Lebat (dari kode BMKG, asumsi)
        // Kode BMKG lain untuk hujan: 95, 97 (Hujan Petir) bisa dipetakan ke fas fa-bolt
        // Anda mungkin perlu menyesuaikan ini berdasarkan dokumentasi kode cuaca BMKG yang lebih detail
    ];

    if (isset($map[$bmkgCode])) {
        return $map[$bmkgCode];
    }

    $lower_desc = strtolower($description);
    if (strpos($lower_desc, 'cerah berawan') !== false) return 'fas fa-cloud-sun';
    if (strpos($lower_desc, 'cerah') !== false) return 'fas fa-sun';
    if (strpos($lower_desc, 'berawan') !== false) return 'fas fa-cloud'; // Umumnya berawan
    if (strpos($lower_desc, 'hujan ringan') !== false) return 'fas fa-cloud-rain';
    if (strpos($lower_desc, 'hujan sedang') !== false) return 'fas fa-cloud-showers-heavy';
    if (strpos($lower_desc, 'hujan lebat') !== false) return 'fas fa-cloud-showers-heavy'; // Bisa juga fa-poo-storm untuk badai
    if (strpos($lower_desc, 'hujan petir') !== false || strpos($lower_desc, 'guntur') !== false) return 'fas fa-bolt';
    if (strpos($lower_desc, 'kabut') !== false || strpos($lower_desc, 'asap') !== false) return 'fas fa-smog';
    
    // Fallback jika tidak ada yang cocok
    return 'fas fa-question-circle'; // Ikon default jika tidak ada yang cocok
}
?>