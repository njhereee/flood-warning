<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Flood Photo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Upload New Flood Photo</h1>
            <a href="report.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Report
            </a>
        </div>

        <form id="uploadPhotoForm" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Select Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Affected Region (Kelurahan/Kecamatan)</label>
                <select id="region" name="region" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a Region</option>
                    </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div>
                <label for="taken_at" class="block text-sm font-medium text-gray-700 mb-1">Date Taken (Optional)</label>
                <input type="datetime-local" id="taken_at" name="taken_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end pt-4 border-t mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-upload mr-2"></i> Upload Photo
                </button>
            </div>
            <div id="formMessage" class="mt-4 p-3 rounded-lg text-sm hidden"></div>
        </form>
    </div>

    <script>
        // Fetch regions for dropdown (same as in add_alert.php)
        async function fetchPekanbaruRegions() {
            try {
                const response = await fetch('api/get_regions.php');
                const data = await response.json();

                const regionSelect = document.getElementById('region');
                regionSelect.innerHTML = '<option value="">Select a Region</option>';

                if (data.regions && Array.isArray(data.regions) && data.regions.length > 0) {
                    data.regions.forEach(region => {
                        const option = document.createElement('option');
                        option.value = region.name;
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

        document.addEventListener('DOMContentLoaded', fetchPekanbaruRegions);

        // Handle form submission for photo upload
        document.getElementById('uploadPhotoForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formMessage = document.getElementById('formMessage');
            formMessage.classList.remove('bg-green-100', 'bg-red-100', 'text-green-800', 'text-red-800');
            formMessage.classList.add('hidden');
            formMessage.textContent = '';

            const formData = new FormData(this); // Use FormData for file uploads

            try {
                const response = await fetch('api/upload_photo_api.php', {
                    method: 'POST',
                    body: formData // FormData object is sent directly, no Content-Type header needed
                });

                const result = await response.json();

                if (response.ok) {
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('bg-green-100', 'text-green-800');
                    formMessage.textContent = result.message || 'Photo uploaded successfully!';
                    this.reset();
                    // Optional: Redirect or refresh part of the report page
                    // setTimeout(() => { window.location.href = 'report.php'; }, 1500);
                } else {
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('bg-red-100', 'text-red-800');
                    formMessage.textContent = result.error || 'Failed to upload photo.';
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