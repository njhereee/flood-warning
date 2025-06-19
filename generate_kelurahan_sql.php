<?php

// Data kelurahan di Pekanbaru berdasarkan daftar yang Anda berikan
$kelurahan_pekanbaru = [
    // Kecamatan Sukajadi
    ['code' => '14.71.01.1002', 'name' => 'Jadirejo', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1003', 'name' => 'Kampung Tengah', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1004', 'name' => 'Kampung Melayu', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1005', 'name' => 'Kedung Sari', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1006', 'name' => 'Harjosari', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1007', 'name' => 'Sukajadi', 'kecamatan' => 'Sukajadi'],
    ['code' => '14.71.01.1008', 'name' => 'Pulau Karomah', 'kecamatan' => 'Sukajadi'],

    // Kecamatan Pekanbaru Kota
    ['code' => '14.71.02.1001', 'name' => 'Simpang Empat', 'kecamatan' => 'Pekanbaru Kota'],
    ['code' => '14.71.02.1002', 'name' => 'Sumahilang', 'kecamatan' => 'Pekanbaru Kota'],
    ['code' => '14.71.02.1003', 'name' => 'Tanah Datar', 'kecamatan' => 'Pekanbaru Kota'],
    ['code' => '14.71.02.1004', 'name' => 'Kota Baru', 'kecamatan' => 'Pekanbaru Kota'],
    ['code' => '14.71.02.1005', 'name' => 'Sukaramai', 'kecamatan' => 'Pekanbaru Kota'],
    ['code' => '14.71.02.1006', 'name' => 'Kota Tinggi', 'kecamatan' => 'Pekanbaru Kota'],

    // Kecamatan Sail
    ['code' => '14.71.03.1001', 'name' => 'Cinta Raja', 'kecamatan' => 'Sail'],
    ['code' => '14.71.03.1002', 'name' => 'Sukamulya', 'kecamatan' => 'Sail'],
    ['code' => '14.71.03.1003', 'name' => 'Sukamaju', 'kecamatan' => 'Sail'],

    // Kecamatan Lima Puluh
    ['code' => '14.71.04.1001', 'name' => 'Rintis', 'kecamatan' => 'Lima Puluh'],
    ['code' => '14.71.04.1002', 'name' => 'Tanjung RHU', 'kecamatan' => 'Lima Puluh'],
    ['code' => '14.71.04.1003', 'name' => 'Pesisir', 'kecamatan' => 'Lima Puluh'],
    ['code' => '14.71.04.1004', 'name' => 'Sekip', 'kecamatan' => 'Lima Puluh'],

    // Kecamatan Senapelan
    ['code' => '14.71.05.1001', 'name' => 'Padang Bulan', 'kecamatan' => 'Senapelan'],
    ['code' => '14.71.05.1002', 'name' => 'Sago', 'kecamatan' => 'Senapelan'],
    ['code' => '14.71.05.1003', 'name' => 'Kampung Baru', 'kecamatan' => 'Senapelan'],
    ['code' => '14.71.05.1004', 'name' => 'Kampung Dalam', 'kecamatan' => 'Senapelan'],
    ['code' => '14.71.05.1005', 'name' => 'Kampung Bandar', 'kecamatan' => 'Senapelan'],
    ['code' => '14.71.05.1006', 'name' => 'Padang Terubuk', 'kecamatan' => 'Senapelan'],

    // Kecamatan Rumbai
    ['code' => '14.71.06.1003', 'name' => 'Rumbai Bukit', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1005', 'name' => 'Muarafajar Timur', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1006', 'name' => 'Umban Sari', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1008', 'name' => 'Sri Meranti', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1009', 'name' => 'Palas', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1010', 'name' => 'Muarafajar Barat', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1011', 'name' => 'Rantaupanjang', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1012', 'name' => 'Maharani', 'kecamatan' => 'Rumbai'],
    ['code' => '14.71.06.1013', 'name' => 'Agrowisata', 'kecamatan' => 'Rumbai'],

    // Kecamatan Bukit Raya
    ['code' => '14.71.07.1005', 'name' => 'Simpang Tiga', 'kecamatan' => 'Bukit Raya'],
    ['code' => '14.71.07.1006', 'name' => 'Tangkerang Selatan', 'kecamatan' => 'Bukit Raya'],
    ['code' => '14.71.07.1008', 'name' => 'Tangkerang Utara', 'kecamatan' => 'Bukit Raya'],
    ['code' => '14.71.07.1011', 'name' => 'Tangkerang Labuai', 'kecamatan' => 'Bukit Raya'],
    ['code' => '14.71.07.1012', 'name' => 'Airdingin', 'kecamatan' => 'Bukit Raya'],

    // Kecamatan Tampan (Perhatikan: Tampan sudah dipecah menjadi Binawidya dan Tuah Madani,
    // namun kita akan menggunakan nama Tampan sesuai daftar Anda.
    // Jika Anda ingin nama baru, Anda perlu menyesuaikan di sini dan di flood_data jika sudah diubah ke nama baru)
    ['code' => '14.71.08.1001', 'name' => 'Simpangbaru', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1006', 'name' => 'Sidomulyo Barat', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1007', 'name' => 'Tuahkarya', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1008', 'name' => 'Delima', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1009', 'name' => 'Tobekgodang', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1010', 'name' => 'Binawidya', 'kecamatan' => 'Tampan'], // Nama kelurahan yang sama dengan nama kecamatan baru
    ['code' => '14.71.08.1011', 'name' => 'Airputih', 'kecamatan' => 'Tampan'],
    ['code' => '14.71.08.1012', 'name' => 'Tuahmadani', 'kecamatan' => 'Tampan'], // Nama kelurahan yang sama dengan nama kecamatan baru
    ['code' => '14.71.08.1013', 'name' => 'Sialangmunggu', 'kecamatan' => 'Tampan'],

    // Kecamatan Marpoyan Damai
    ['code' => '14.71.09.1001', 'name' => 'Tangkerang Barat', 'kecamatan' => 'Marpoyan Damai'],
    ['code' => '14.71.09.1002', 'name' => 'Tangkerang Tengah', 'kecamatan' => 'Marpoyan Damai'],
    ['code' => '14.71.09.1003', 'name' => 'Sidomulyo Timur', 'kecamatan' => 'Marpoyan Damai'],
    ['code' => '14.71.09.1004', 'name' => 'Wonorejo', 'kecamatan' => 'Marpoyan Damai'],
    ['code' => '14.71.09.1005', 'name' => 'Maharatu', 'kecamatan' => 'Marpoyan Damai'],
    ['code' => '14.71.09.1006', 'name' => 'Perhentianmarpoyan', 'kecamatan' => 'Marpoyan Damai'],

    // Kecamatan Tenayan Raya
    ['code' => '14.71.10.1001', 'name' => 'Kulim', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1002', 'name' => 'Bencahlesung', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1003', 'name' => 'Tangkerang Timur', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1004', 'name' => 'Rejosari', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1005', 'name' => 'Bambukuning', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1006', 'name' => 'Pebatuan', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1007', 'name' => 'Sialangrampai', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1008', 'name' => 'Mentangor', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1009', 'name' => 'Pematangkapau', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1010', 'name' => 'Melebung', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1011', 'name' => 'Industritenayan', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1012', 'name' => 'Sialangsakti', 'kecamatan' => 'Tenayan Raya'],
    ['code' => '14.71.10.1013', 'name' => 'Tuahnegeri', 'kecamatan' => 'Tenayan Raya'],

    // Kecamatan Payung Sekaki
    ['code' => '14.71.11.1001', 'name' => 'Tampan', 'kecamatan' => 'Payung Sekaki'], // Perhatikan: nama kelurahan yang sama dengan nama kecamatan lama
    ['code' => '14.71.11.1002', 'name' => 'Labuh Baru Timur', 'kecamatan' => 'Payung Sekaki'],
    ['code' => '14.71.11.1003', 'name' => 'Labuh Baru Barat', 'kecamatan' => 'Payung Sekaki'],
    ['code' => '14.71.11.1004', 'name' => 'Air Hitam', 'kecamatan' => 'Payung Sekaki'],
    ['code' => '14.71.11.1005', 'name' => 'Bandarraya', 'kecamatan' => 'Payung Sekaki'],
    ['code' => '14.71.11.1006', 'name' => 'Sungaisibam', 'kecamatan' => 'Payung Sekaki'],
    ['code' => '14.71.11.1007', 'name' => 'Tirtasiak', 'kecamatan' => 'Payung Sekaki'],

    // Kecamatan Rumbai Pesisir
    ['code' => '14.71.12.1001', 'name' => 'Meranti Pandak', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1002', 'name' => 'Limbungan', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1003', 'name' => 'Lembah Sari', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1004', 'name' => 'Lembah Damai', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1005', 'name' => 'Tebing Tinggi Okura', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1006', 'name' => 'Limbungan Baru', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1007', 'name' => 'Sungaiambang', 'kecamatan' => 'Rumbai Pesisir'],
    ['code' => '14.71.12.1008', 'name' => 'Sungaiukai', 'kecamatan' => 'Rumbai Pesisir'],
];

// Anda perlu melengkapi ini dengan koordinat Lintang (latitude) dan Bujur (longitude)
// untuk setiap kelurahan. Ini adalah tugas manual yang harus Anda lakukan.
// Contoh:
// $kelurahan_koordinat = [
//     'Jadirejo' => ['lat' => 0.511, 'lon' => 101.428],
//     'Simpang Empat' => ['lat' => 0.530, 'lon' => 101.445],
//     // ... dan seterusnya untuk semua kelurahan
// ];

// Untuk tujuan demonstrasi ini, saya akan menggunakan koordinat dummy (N/A)
// Anda HARUS mengganti ini dengan koordinat yang sebenarnya setelah Anda mencarinya.
foreach ($kelurahan_pekanbaru as &$k) {
    // Anda bisa coba Google Search untuk setiap kelurahan: "latitude longitude Kelurahan Jadirejo Pekanbaru"
    // Dan isi di sini:
    // if ($k['name'] == 'Jadirejo') { $k['latitude'] = 0.511; $k['longitude'] = 101.428; }
    // else if ($k['name'] == 'Simpang Empat') { $k['latitude'] = 0.530; $k['longitude'] = 101.445; }
    // else { // Default jika koordinat belum ditemukan
        $k['latitude'] = null; // HARUS DIGANTI
        $k['longitude'] = null; // HARUS DIGANTI
    // }
    $k['danger_level_m'] = 3.5; // Default danger level, bisa disesuaikan per kelurahan
    $k['type'] = 'district';
}
unset($k); // Putuskan referensi dari &$k

// SQL untuk Insert/Update ke tabel 'areas'
$sql_insert_areas = "
INSERT INTO `areas` (`name`, `latitude`, `longitude`, `danger_level_m`, `type`) VALUES
";

$values = [];
foreach ($kelurahan_pekanbaru as $k) {
    $lat = is_null($k['latitude']) ? 'NULL' : $k['latitude'];
    $lon = is_null($k['longitude']) ? 'NULL' : $k['longitude'];
    $values[] = "('" . $k['name'] . "', " . $lat . ", " . $lon . ", " . $k['danger_level_m'] . ", '" . $k['type'] . "')";
}

$sql_insert_areas .= implode(",\n", $values) . "
ON DUPLICATE KEY UPDATE
    latitude = VALUES(latitude),
    longitude = VALUES(longitude),
    danger_level_m = VALUES(danger_level_m),
    type = VALUES(type);
";

echo "<pre>";
echo "<h2>1. SQL untuk Memperbarui Tabel `areas` dengan Kelurahan Pekanbaru:</h2>";
echo "Silakan Jalankan SQL ini di phpMyAdmin Anda:\n\n";
echo $sql_insert_areas;
echo "</pre>";

// --- Opsional: Perbarui `flood_data` jika Anda ingin data banjir menjadi per kelurahan ---
// Ini akan membutuhkan data flood_data Anda untuk memiliki entri kelurahan,
// bukan hanya kecamatan. Anda mungkin ingin membuat data flood_data baru
// yang menargetkan kelurahan-kelurahan ini.
// Contoh: Ganti 'Sukajadi' di flood_data dengan 'Jadirejo' atau 'Simpang Empat'

echo "<pre>";
echo "<h2>2. SQL untuk Memperbarui Tabel `flood_data` (Opsional - Jika ingin per kelurahan):</h2>";
echo "Jika Anda ingin data banjir di `flood_data` menjadi per kelurahan, Anda harus memetakan ulang secara manual.\n";
echo "Misalnya, Anda bisa membuat entri baru atau memperbarui yang lama:\n\n";
echo "INSERT INTO `flood_data` (`region`, `level`, `status`, `recorded_at`) VALUES\n";
echo "('Jadirejo', 3.80, 'warning', NOW()),\n";
echo "('Simpang Empat', 3.50, 'watch', NOW()),\n";
echo "('Rumbai Bukit', 3.20, 'update', NOW());\n\n";
echo "Atau update yang sudah ada:\n";
echo "UPDATE `flood_data` SET `region` = 'Jadirejo' WHERE `region` = 'Sukajadi';\n";
echo "UPDATE `flood_data` SET `region` = 'Simpang Empat' WHERE `region` = 'Pekanbaru Kota';\n";
echo "UPDATE `flood_data` SET `region` = 'Rumbai Bukit' WHERE `region` = 'Rumbai';\n";
echo "</pre>";

// --- Kode untuk BMKG API (Hanya Contoh ADM4 untuk Kelurahan) ---
echo "<pre>";
echo "<h2>3. Contoh Penggunaan Kode ADM4 Kelurahan di `api/bmkg_weather.php`:</h2>";
echo "Setelah Anda menemukan kode ADM4 kelurahan yang ingin Anda pantau (misalnya, untuk 'Jadirejo'), Anda dapat menggunakannya di `api/bmkg_weather.php`.\n\n";
echo "// Dalam api/bmkg_weather.php:\n";
echo "$adm4_pekanbaru = '14.71.01.1002'; // Contoh untuk Kelurahan Jadirejo, GANTI INI DENGAN KODE AKURAT\n";
echo "$api_url = \"https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={\$adm4_pekanbaru}\";\n";
echo "</pre>";

?>