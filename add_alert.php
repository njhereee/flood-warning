<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Flood Alert</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Flood Alert</h1>
            <a href="alert.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Alerts
            </a>
        </div>

        <form id="addAlertForm" class="space-y-4">
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Affected Region (Kelurahan/Kecamatan)</label>
                <select id="region" name="region" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a Region</option>
                    </select>
            </div>

            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Water Level (meters)</label>
                <input type="number" id="level" name="level" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Alert Status</label>
                <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Status</option>
                    <option value="update">Update</option>
                    <option value="watch">Watch</option>
                    <option value="warning">Warning</option>
                </select>
            </div>

            <div class="flex justify-end pt-4 border-t mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-plus-circle mr-2"></i> Add Alert
                </button>
            </div>
            <div id="formMessage" class="mt-4 p-3 rounded-lg text-sm hidden"></div>
        </form>
    </div>

    <script>
        // Function to fetch Pekanbaru districts for the dropdown
        async function fetchPekanbaruRegions() {
            try {
                // Asumsi Anda memiliki API yang mengembalikan daftar kelurahan/kecamatan
                // Contoh: api/pekanbaru_districts.php (yang kita bahas sebelumnya untuk kodewilayah.id)
                // Atau, jika Anda hanya menggunakan nama-nama dari tabel areas, Anda bisa buat API baru
                // yang mengambil semua nama dari tabel areas.
                const response = await fetch('api/get_regions.php'); // API baru untuk mengambil nama region
                const data = await response.json();

                const regionSelect = document.getElementById('region');
                regionSelect.innerHTML = '<option value="">Select a Region</option>'; // Reset options

                if (data.regions && Array.isArray(data.regions) && data.regions.length > 0) {
                    data.regions.forEach(region => {
                        const option = document.createElement('option');
                        option.value = region.name; // Asumsi API mengembalikan objek {name: "Kelurahan A"}
                        option.textContent = region.name;
                        regionSelect.appendChild(option);
                    });
                } else {
                    console.error("No regions found or error in API response for regions.");
                    regionSelect.innerHTML += '<option value="">Failed to load regions</option>';
                }

            } catch (error) {
                console.error("Error fetching regions for dropdown:", error);
                const regionSelect = document.getElementById('region');
                regionSelect.innerHTML = '<option value="">Error loading regions</option>';
            }
        }

        // Call the function to populate regions on page load
        document.addEventListener('DOMContentLoaded', fetchPekanbaruRegions);

        // Handle form submission
        document.getElementById('addAlertForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            const formMessage = document.getElementById('formMessage');
            formMessage.classList.remove('bg-green-100', 'bg-red-100', 'text-green-800', 'text-red-800');
            formMessage.classList.add('hidden');
            formMessage.textContent = ''; // Clear previous message

            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            try {
                const response = await fetch('api/add_alert_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();

                if (response.ok) { // Check if HTTP status is 2xx
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('bg-green-100', 'text-green-800');
                    formMessage.textContent = result.message || 'Alert added successfully!';
                    this.reset(); // Clear form fields
                    // Optional: Refresh dashboard/alerts page after successful submission
                    // setTimeout(() => { window.location.href = 'alert.php'; }, 1500);
                } else {
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('bg-red-100', 'text-red-800');
                    formMessage.textContent = result.error || 'Failed to add alert.';
                    console.error("API error:", result.error);
                }

            } catch (error) {
                formMessage.classList.remove('hidden');
                formMessage.classList.add('bg-red-100', 'text-red-800');
                formMessage.textContent = 'An error occurred while connecting to the server.';
                console.error("Network error:", error);
            }
        });
    </script>
</body>
</html>