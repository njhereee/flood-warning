


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flood Report System</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
  />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <style>
    /* Animasi dan style custom tetap dipertahankan kecuali water-animation dihapus dari peta */
    .pulse-alert {
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
      100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .map-marker {
      animation: bounce 1.5s infinite;
    }
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }
    .severity-low { background-color: #10B981; }
    .severity-medium { background-color: #F59E0B; }
    .severity-high { background-color: #EF4444; }
    .severity-extreme { background-color: #7C3AED; }
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }
    .report-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .report-card {
      transition: all 0.3s ease;
    }
    /* Style khusus untuk peta - Ini akan dihapus jika #floodMap dihapus total */
    /*
    #floodMap {
      height: 320px;
      border-radius: 0.75rem;
      box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
    }
    */
    /* Custom marker icon for Leaflet - Ini juga akan dihapus jika #floodMap dihapus total */
    /*
    .custom-div-icon {
        background-color: transparent;
        border: none;
    }
    .custom-div-icon i {
        filter: drop-shadow(0 0 3px rgba(0,0,0,0.5));
        animation: bounce 1.5s infinite;
    }
    */
    /* CSS for dark mode chart text colors (if implemented in index.php or general) */
    :root {
      --text-color-primary: black;
      --grid-color: rgba(0,0,0,0.1);
    }
    .dark {
      --text-color-primary: white;
      --grid-color: rgba(255,255,255,0.1);
    }
  </style>
</head>
<body class="bg-gray-50 font-sans">
  <div class="min-h-screen flex flex-col">
    <header class="bg-blue-800 text-white shadow-lg">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <i class="fas fa-file-alt text-3xl"></i>
          <h1 class="text-2xl font-bold">Flood Report System</h1>
        </div>
        <div class="flex items-center space-x-4">
          <span class="hidden md:block">Last report: <span id="update-time" class="font-semibold">Today</span></span>
          <a href="index.php" class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
          </a>
        </div>
      </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
      <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
              <h2 class="text-2xl font-bold mb-2">Flood Incident Report</h2>
              <p class="opacity-90">Comprehensive analysis of flood events and impacts</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
              <button id="pdf-report-btn" class="bg-white text-blue-800 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 flex items-center">
                <i class="fas fa-download mr-2"></i> PDF Report
              </button>
              <button class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg font-semibold flex items-center">
                <i class="fas fa-share-alt mr-2"></i> Share
              </button>
            </div>
          </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm text-blue-800 font-medium">Affected Areas</p>
                <h3 class="text-2xl font-bold text-blue-900 mt-1" id="affected-areas-count">--</h3>
              </div>
              <div class="bg-blue-100 p-2 rounded-full">
                <i class="fas fa-map-marked-alt text-blue-600"></i>
              </div>
            </div>
            <p class="text-xs text-blue-600 mt-2">+3 from yesterday</p>
          </div>

          <div class="bg-red-50 p-4 rounded-lg border border-red-100">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm text-red-800 font-medium">Evacuated</p>
                <h3 class="text-2xl font-bold text-red-900 mt-1" id="evacuated-count">--</h3>
              </div>
              <div class="bg-red-100 p-2 rounded-full">
                <i class="fas fa-people-carry text-red-600"></i>
              </div>
            </div>
            <p class="text-xs text-red-600 mt-2">+412 from yesterday</p>
          </div>

          <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm text-yellow-800 font-medium">Damaged Homes</p>
                <h3 class="text-2xl font-bold text-yellow-900 mt-1" id="damaged-homes-count">--</h3>
              </div>
              <div class="bg-yellow-100 p-2 rounded-full">
                <i class="fas fa-home text-yellow-600"></i>
              </div>
            </div>
            <p class="text-xs text-yellow-600 mt-2">+22 from yesterday</p>
          </div>

          <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm text-green-800 font-medium">Rescue Teams</p>
                <h3 class="text-2xl font-bold text-green-900 mt-1" id="rescue-teams-count">--</h3>
              </div>
              <div class="bg-green-100 p-2 rounded-full">
                <i class="fas fa-ambulance text-green-600"></i>
              </div>
            </div>
            <p class="text-xs text-green-600 mt-2">+5 deployed today</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200">
              <h3 class="text-lg font-semibold">Historical Flood Data</h3>
            </div>
            <div class="p-5">
              <div class="chart-container">
                <canvas id="historicalFloodChart"></canvas>
              </div>

              <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border-l-4 border-blue-500 pl-3 py-1">
                  <p class="text-sm text-gray-500">Peak Flood Level</p>
                  <p class="font-bold" id="peak-flood-level">-- meters</p>
                  <p class="text-xs text-gray-400" id="peak-flood-time">--</p>
                </div>
                <div class="border-l-4 border-red-500 pl-3 py-1">
                  <p class="text-sm text-gray-500">Duration</p>
                  <p class="font-bold" id="flood-duration">-- hours</p>
                  <p class="text-xs text-gray-400">Current ongoing duration</p>
                </div>
                <div class="border-l-4 border-yellow-500 pl-3 py-1">
                  <p class="text-sm text-gray-500">Recovery Time</p>
                  <p class="font-bold">5-7 days</p>
                  <p class="text-xs text-gray-400">Estimated based on current data</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200">
              <h3 class="text-lg font-semibold">Damage Assessment</h3>
            </div>
            <div class="p-5">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Area
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Homes
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Infrastructure
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Agriculture
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200" id="damage-assessment-table-body">
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" colspan="5">
                        Loading damage assessment data...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div
                class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4"
              >
                <div class="flex">
                  <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Assessment Notes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                      <p>• Damage assessments are preliminary and may change as waters recede</p>
                      <p>• Agricultural losses include rice fields and vegetable crops</p>
                      <p>• Infrastructure damage estimates include roads and bridges</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200">
              <h3 class="text-lg font-semibold">Response Activities</h3>
            </div>
            <div class="p-5">
              <div class="space-y-4">
                <div class="flex items-start">
                  <div class="flex-shrink-0 bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-ambulance text-purple-600"></i>
                  </div>
                  <div class="ml-4">
                    <h4 class="text-sm font-semibold">Rescue Operations</h4>
                    <p class="text-sm text-gray-600 mt-1">
                      Ongoing in Central District and Riverside Area. 412 people evacuated in last 6
                      hours.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      <i class="far fa-clock mr-1"></i> Updated 1 hour ago
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                    <i class="fas fa-utensils text-green-600"></i>
                  </div>
                  <div class="ml-4">
                    <h4 class="text-sm font-semibold">Relief Distribution</h4>
                    <p class="text-sm text-gray-600 mt-1">
                      Food and water being distributed at 5 shelters. Medical teams on standby.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      <i class="far fa-clock mr-1"></i> Updated 2 hours ago
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-tools text-blue-600"></i>
                  </div>
                  <div class="ml-4">
                    <h4 class="text-sm font-semibold">Infrastructure Repair</h4>
                    <p class="text-sm text-gray-600 mt-1">
                      Temporary bridges being constructed. Power restoration in progress.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      <i class="far fa-clock mr-1"></i> Updated 3 hours ago
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-bullhorn text-yellow-600"></i>
                  </div>
                  <div class="ml-4">
                    <h4 class="text-sm font-semibold">Public Information</h4>
                    <p class="text-sm text-gray-600 mt-1">
                      Emergency broadcasts ongoing. Hotline established: 0800-FLOOD-HELP
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      <i class="far fa-clock mr-1"></i> Updated 30 min ago
                    </p>
                  </div>
                </div>
              </div>

              <button
                id="addActivityReportBtn" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium flex items-center justify-center"
              >
                <i class="fas fa-plus-circle mr-2"></i> Add Activity Report
              </button>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200">
              <h3 class="text-lg font-semibold">Photo Documentation</h3>
            </div>
            <div class="p-5">
              <div class="grid grid-cols-2 gap-3">
                <div
                  class="bg-gray-100 rounded-lg overflow-hidden h-24 flex items-center justify-center"
                >
                  <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
                <div
                  class="bg-gray-100 rounded-lg overflow-hidden h-24 flex items-center justify-center"
                >
                  <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
                <div
                  class="bg-gray-100 rounded-lg overflow-hidden h-24 flex items-center justify-center"
                >
                  <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
                <div
                  class="bg-gray-100 rounded-lg overflow-hidden h-24 flex items-center justify-center"
                >
                  <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
              </div>

              <!-- ...existing code... -->
<div class="mt-4 flex items-center text-sm text-blue-600">
    <i class="fas fa-cloud-upload-alt mr-2"></i>
    <form id="photo-upload-form" enctype="multipart/form-data" class="flex items-center space-x-2">
        <input type="file" id="photo-input" name="photo" accept="image/*" class="hidden" />
        <button type="button" id="photo-upload-btn" class="underline">Upload new photo</button>
        <span id="upload-status" class="ml-2 text-xs text-gray-500"></span>
    </form>
</div>
<!-- ...existing code... -->

              <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-gray-500"></i>
                  </div>
                  <div class="ml-3">
                    <p class="text-xs text-gray-600">
                      Photos should show flood conditions, damage, and response efforts. Maximum 10MB
                      per photo.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
  <div class="p-5 border-b border-gray-200">
    <h3 class="text-lg font-semibold">Flood Victims</h3>
  </div>
  <div class="p-5 max-h-64 overflow-y-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left font-medium text-gray-700">Name</th>
          <th class="px-3 py-2 text-left font-medium text-gray-700">Age</th>
          <th class="px-3 py-2 text-left font-medium text-gray-700">Location</th>
          <th class="px-3 py-2 text-left font-medium text-gray-700">Status</th>
          <th class="px-3 py-2 text-left font-medium text-gray-700">Needs Assistance</th>
        </tr>
      </thead>
      <tbody id="victims-table-body">
        <tr><td colspan="5" class="text-center py-4 text-gray-500">Loading victim data...</td></tr>
      </tbody>
    </table>
  </div>
</div>


          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-lg font-semibold">Weather Analysis</h3>
              <div class="flex items-center space-x-2 text-blue-600">
                <i id="weather-icon" class="fas fa-cloud-showers-heavy text-3xl"></i>
                <div class="flex flex-col">
                  <span id="weather-description" class="text-sm font-semibold">Loading...</span>
                  <span id="weather-temp" class="text-xs">-- °C</span>
                </div>
              </div>
            </div>
            <div class="p-5 space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Rainfall (24h)</span>
                <span id="rainfall" class="text-sm font-semibold">-- mm</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Expected Rainfall (next 24h)</span>
                <span id="expected-rainfall" class="text-sm font-semibold text-red-600">-- mm</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">River Levels</span>
                <span id="river-levels" class="text-sm font-semibold text-red-600">-- m above danger</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Wind Speed</span>
                <span id="wind-speed" class="text-sm font-semibold">-- km/h</span>
              </div>

              <div
                class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start"
              >
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                <div>
                  <h3 class="text-sm font-medium text-yellow-800">Weather Advisory</h3>
                  <p class="mt-2 text-sm text-yellow-700">
                    Heavy rainfall expected to continue for next 48 hours. Flood conditions likely
                    to worsen.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-lg overflow-hidden report-card">
            <div class="p-5 border-b border-gray-200">
              <h3 class="text-lg font-semibold">Report Actions</h3>
            </div>
            <div class="p-5 space-y-3">
              <button
                class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-800 rounded-lg"
              >
                <span>Share with Authorities</span>
                <i class="fas fa-share"></i>
              </button>

              <button
                class="w-full flex items-center justify-between px-4 py-3 bg-green-50 hover:bg-green-100 text-green-800 rounded-lg"
              >
                <span>Request Assistance</span>
                <i class="fas fa-hands-helping"></i>
              </button>

              <button
                class="w-full flex items-center justify-between px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-800 rounded-lg"
              >
                <span>Generate PDF</span>
                <i class="fas fa-file-pdf"></i>
              </button>

              <button
                class="w-full flex items-center justify-between px-4 py-3 bg-red-50 hover:bg-red-100 text-red-800 rounded-lg"
              >
                <span>Emergency Broadcast</span>
                <i class="fas fa-broadcast-tower"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="bg-gray-800 text-white py-6">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">
            <h2 class="text-xl font-bold flex items-center">
              <i class="fas fa-file-alt mr-2"></i> Flood Report System
            </h2>
            <p class="text-gray-400 text-sm mt-1">Comprehensive flood incident documentation</p>
          </div>
          <div class="flex space-x-4">
            <a href="#" class="hover:text-blue-300"><i class="fas fa-print"></i></a>
            <a href="#" class="hover:text-blue-300"><i class="fas fa-question-circle"></i></a>
            <a href="#" class="hover:text-blue-300"><i class="fas fa-envelope"></i></a>
          </div>
        </div>
        <div class="border-t border-gray-700 mt-6 pt-6 text-sm text-gray-400">
          <p class="text-center">&copy; 2023 Flood Alert System. Report ID: FLD-RPT-2023-0428</p>
        </div>
      </div>
    </footer>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <script>
    // Update time display
    function updateTime() {
      const now = new Date();
      const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      };
      document.getElementById("update-time").textContent = now.toLocaleDateString(
        "en-US",
        options
      );
    }
    updateTime();

    // Make report cards interactive
    document.querySelectorAll(".report-card").forEach((card) => {
      card.addEventListener("click", function (e) {
        if (!e.target.closest("button") && !e.target.closest("a")) {
          console.log("Card clicked:", this.querySelector("h3").textContent);
          // Future: navigate to detail view
        }
      });
    });

    document.getElementById('pdf-report-btn').addEventListener('click', function() {
    window.open('generate_pdf.php', '_blank'); // Ini harus sama persis
});

    // Simulate loading photos icons color change
    setTimeout(() => {
      const loadingElements = document.querySelectorAll(".bg-gray-100, .bg-gray-50");
      loadingElements.forEach((el) => {
        el.classList.remove("bg-gray-100", "bg-gray-50");
        el.classList.add("bg-blue-50");
        const icon = el.querySelector("i.fa-camera");
        if (icon) {
          icon.classList.remove("text-gray-400");
          icon.classList.add("text-blue-400");
        }
      });
    }, 1500);

    // ...existing code...
document.getElementById('photo-upload-btn').addEventListener('click', function() {
    document.getElementById('photo-input').click();
});

// Panggil fungsi-fungsi fetch saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateTime();
        fetchWeatherData();
        fetchVictimData();
        fetchReportData();

        // --- KODE BARU UNTUK TOMBOL ADD ACTIVITY REPORT ---
        const addActivityBtn = document.getElementById('addActivityReportBtn');
        if (addActivityBtn) {
            addActivityBtn.addEventListener('click', function() {
                // Mengarahkan ke halaman add_report.php
                // Sesuaikan path jika add_report.php berada di direktori lain
                window.location.href = 'add_report.php';
            });
        }
        // --- AKHIR KODE BARU ---

        // Atur interval refresh data (opsional)
        setInterval(updateTime, 60000); // Update waktu setiap menit
        setInterval(fetchWeatherData, 300000); // Refresh data cuaca setiap 5 menit
        setInterval(fetchVictimData, 120000); // Refresh data korban setiap 2 menit
        setInterval(fetchReportData, 90000);  // Refresh data laporan umum setiap 1.5 menit
    });

document.getElementById('photo-input').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) { // 10MB limit
        document.getElementById('upload-status').textContent = "File too large (max 10MB)";
        return;
    }
    const formData = new FormData();
    formData.append('photo', file);

    document.getElementById('upload-status').textContent = "Uploading...";

    fetch('upload_photo.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('upload-status').textContent = "Upload successful!";
            // Optionally, refresh photo gallery here
        } else {
            document.getElementById('upload-status').textContent = data.error || "Upload failed.";
        }
    })
    .catch(() => {
        document.getElementById('upload-status').textContent = "Upload error.";
    });
});
// ...existing code...

    // Leaflet map init - DIHAPUS
    // const map = L.map("floodMap").setView([0.507068, 101.447743], 12); // Pekanbaru coords
    // L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    //   attribution: "&copy; OpenStreetMap contributors",
    // }).addTo(map);

    // Fetch OpenWeatherMap Data (Now uses your BMKG proxy)
    async function fetchWeatherData() {
        try {
            const response = await fetch('api/bmkg_weather.php'); // Your BMKG weather proxy
            const data = await response.json();

            if (data.error) {
                console.error("Error from weather API proxy:", data.error);
                document.getElementById("weather-description").textContent = 'Error loading weather';
                document.getElementById("weather-temp").textContent = '--°C';
                document.getElementById("weather-icon").className = 'fas fa-exclamation-circle text-red-500';
                // Reset other weather fields
                document.getElementById("rainfall").textContent = "-- mm";
                document.getElementById("expected-rainfall").textContent = "-- mm";
                document.getElementById("river-levels").textContent = "-- m above danger";
                document.getElementById("wind-speed").textContent = "-- km/h";
                return;
            }

            // Update "Today" Weather
            const weatherIcon = document.getElementById("weather-icon");
            weatherIcon.className = data.weather_icon_code; // Use FA class from proxy

            document.getElementById("weather-description").textContent = data.weather_description;
            document.getElementById("weather-temp").textContent = `${parseFloat(data.current_temp).toFixed(1)} °C`;

            // Update other weather details if available from BMKG proxy
            document.getElementById("rainfall").textContent = data.rainfall_24h || "-- mm"; // Assuming proxy provides these
            document.getElementById("expected-rainfall").textContent = data.expected_rainfall_24h || "-- mm";
            document.getElementById("river-levels").textContent = data.river_levels_danger || "-- m above danger";
            document.getElementById("wind-speed").textContent = `${data.wind_speed || '--'} km/h`;


            // Daily forecast updates (Mon, Tue, Wed, Thu)
            const dailyForecastDivs = document.querySelectorAll('.weather-forecast .grid.grid-cols-4 > div');
            if (data.daily_forecast && data.daily_forecast.length > 0) {
                data.daily_forecast.forEach((day_data, index) => {
                    if (dailyForecastDivs[index]) {
                        dailyForecastDivs[index].querySelector('p:first-child').textContent = day_data.day;
                        const icon = dailyForecastDivs[index].querySelector('i');
                        if (icon) icon.className = day_data.icon_fa;
                        dailyForecastDivs[index].querySelector('p:last-child').textContent = `${parseFloat(day_data.max_temp).toFixed(0)}° / ${parseFloat(day_data.min_temp).toFixed(0)}°`;
                    }
                });
            } else {
                // Clear or reset static forecast if no dynamic data
                dailyForecastDivs.forEach(div => {
                    div.querySelector('p:first-child').textContent = 'N/A';
                    const icon = div.querySelector('i');
                    if(icon) icon.className = 'fas fa-question text-gray-400 my-1';
                    div.querySelector('p:last-child').textContent = '--° / --°';
                });
            }

        } catch (error) {
            console.error('Error fetching weather data for report:', error);
            document.getElementById("weather-description").textContent = "Connection Error";
            document.getElementById("weather-temp").textContent = "--°C";
            document.getElementById("weather-icon").className = 'fas fa-wifi text-red-500';
            document.getElementById("rainfall").textContent = "-- mm";
            document.getElementById("expected-rainfall").textContent = "-- mm";
            document.getElementById("river-levels").textContent = "-- m above danger";
            document.getElementById("wind-speed").textContent = "-- km/h";
            // Reset daily forecast
            const dailyForecastDivs = document.querySelectorAll('.weather-forecast .grid.grid-cols-4 > div');
            dailyForecastDivs.forEach(div => {
                div.querySelector('p:first-child').textContent = 'N/A';
                const icon = div.querySelector('i');
                if(icon) icon.className = 'fas fa-question text-gray-400 my-1';
                div.querySelector('p:last-child').textContent = '--° / --°';
            });
        }
    }

    async function fetchVictimData() {
  try {
    const response = await fetch('api/flood_victims.php');
    const data = await response.json();

    const tbody = document.getElementById('victims-table-body');
    tbody.innerHTML = '';

    if (data.success && data.victims.length > 0) {
      data.victims.forEach(victim => {
        tbody.innerHTML += `
          <tr>
            <td class="px-3 py-2">${victim.name}</td>
            <td class="px-3 py-2">${victim.age || '-'}</td>
            <td class="px-3 py-2">${victim.location || '-'}</td>
            <td class="px-3 py-2">${victim.status || '-'}</td>
            <td class="px-3 py-2">${victim.assistance_needed ? 'Yes' : 'No'}</td>
          </tr>
        `;
      });
    } else {
      tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">No victim data available.</td></tr>`;
    }
  } catch (error) {
    console.error('Error fetching victim data:', error);
    document.getElementById('victims-table-body').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Failed to load victim data.</td></tr>`;
  }
}

// Call on page load
fetchVictimData();

// Optional: refresh victim data every 2 minutes
setInterval(fetchVictimData, 120000);


    // Function to fetch and display report data from the backend API
    async function fetchReportData() {
        try {
            const response = await fetch('api/report_data.php'); // Your new API endpoint
            const data = await response.json();

            // Update summary banner
            document.getElementById('affected-areas-count').textContent = data.summary.affected_areas_count;
            document.getElementById('evacuated-count').textContent = data.summary.evacuated_count;
            document.getElementById('damaged-homes-count').textContent = data.summary.damaged_homes_count;
            document.getElementById('rescue-teams-count').textContent = data.summary.rescue_teams_count;

            // Update Damage Assessment table
            const damageTableBody = document.getElementById('damage-assessment-table-body');
            damageTableBody.innerHTML = ''; // Clear existing content
            if (data.damage_assessment && data.damage_assessment.length > 0) {
                data.damage_assessment.forEach(item => {
                    let statusClass = '';
                    if (item.status === 'Critical') statusClass = 'bg-red-100 text-red-800';
                    else if (item.status === 'Severe') statusClass = 'bg-orange-100 text-orange-800';
                    else if (item.status === 'Moderate') statusClass = 'bg-yellow-100 text-yellow-800';

                    damageTableBody.innerHTML += `
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${item.area}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.homes}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.infrastructure}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.agriculture}</td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}"
                              >${item.status}</span
                            >
                          </td>
                        </tr>
                    `;
                });
            } else {
                damageTableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No damage assessment data available.</td></tr>`;
            }


            // Update Historical Flood Data (Peak Level and Duration - simplified)
            if (data.reports && data.reports.length > 0) {
                // Sort reports by recorded_at DESC to find latest/peak easily
                data.reports.sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at));
                const latestReport = data.reports[0];
                document.getElementById('peak-flood-level').textContent = `${latestReport.level} meters`;
                document.getElementById('peak-flood-time').textContent = `Recorded at ${new Date(latestReport.recorded_at).toLocaleString()}`;

                // Calculate duration if more than one report exists
                let floodDuration = 'N/A';
                if (data.reports.length > 1) {
                    // Find the oldest report for duration calculation
                    const oldestReport = data.reports[data.reports.length - 1];
                    const diffTime = Math.abs(new Date(latestReport.recorded_at) - new Date(oldestReport.recorded_at));
                    const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
                    floodDuration = `${diffHours} hours`;
                } else {
                    floodDuration = 'Data limited for duration';
                }
                document.getElementById('flood-duration').textContent = floodDuration;

                // --- Historical Flood Chart Logic ---
                const chartCanvas = document.getElementById('historicalFloodChart');
                const chartContainer = chartCanvas.closest('.chart-container');

                if (chartCanvas) {
                    // Destroy existing chart instance if it exists
                    if (window.historicalFloodChartInstance) {
                        window.historicalFloodChartInstance.destroy();
                    }

                    const chartLabels = [];
                    const datasets = [];
                    const colors = ['#38bdf8', '#0ea5e9', '#0284c7', '#0369a1']; // Colors for different regions

                    // Group data by region
                    const groupedData = {};
                    // Sort the raw reports data by recorded_at ascending for chronological chart
                    data.reports.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));

                    data.reports.forEach(item => {
                        if (!groupedData[item.region]) {
                            groupedData[item.region] = [];
                        }
                        groupedData[item.region].push(item);

                        // Collect all unique recorded_at timestamps for labels
                        const recordedAtLabel = new Date(item.recorded_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                        if (!chartLabels.includes(recordedAtLabel)) {
                            chartLabels.push(recordedAtLabel);
                        }
                    });

                    chartLabels.sort((a, b) => new Date(a) - new Date(b)); // Sort labels chronologically

                    let colorIndex = 0;
                    for (const region in groupedData) {
                        const dataPoints = [];
                        const regionData = groupedData[region];

                        chartLabels.forEach(label => {
                            const found = regionData.find(item => new Date(item.recorded_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) === label);
                            dataPoints.push(found ? parseFloat(found.level) : null);
                        });

                        datasets.push({
                            label: region,
                            data: dataPoints,
                            borderColor: colors[colorIndex % colors.length],
                            backgroundColor: colors[colorIndex % colors.length] + '40',
                            fill: false,
                            tension: 0.1,
                            spanGaps: true
                        });
                        colorIndex++;
                    }

                    const ctx = chartCanvas.getContext('2d');
                    window.historicalFloodChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: datasets.length > 1,
                                    position: 'top',
                                    labels: {
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color-primary')
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y + 'm';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Water Level (m)',
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color-primary')
                                    },
                                    ticks: {
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color-primary')
                                    },
                                    grid: {
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--grid-color')
                                    }
                                },
                                x: {
                                    type: 'category',
                                    title: {
                                        display: true,
                                        text: 'Time',
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color-primary')
                                    },
                                    ticks: {
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color-primary'),
                                        maxRotation: 45,
                                        minRotation: 45
                                    },
                                    grid: {
                                        color: getComputedStyle(document.documentElement).getPropertyValue('--grid-color')
                                    }
                                }
                            }
                        }
                    });
                } else {
                     if (window.historicalFloodChartInstance) {
                        window.historicalFloodChartInstance.destroy();
                        window.historicalFloodChartInstance = null;
                    }
                    chartContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 rounded">
                            <p class="text-gray-500">No chart data available.</p>
                        </div>
                    `;
                }
            } else {
                document.getElementById('peak-flood-level').textContent = '-- meters';
                document.getElementById('peak-flood-time').textContent = '--';
                document.getElementById('flood-duration').textContent = '-- hours';
                
                // Clear and display message for the chart when no reports data
                const chartCanvas = document.getElementById('historicalFloodChart');
                const chartContainer = chartCanvas.closest('.chart-container');
                 if (window.historicalFloodChartInstance) {
                    window.historicalFloodChartInstance.destroy();
                    window.historicalFloodChartInstance = null;
                }
                if (chartContainer) {
                    chartContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 rounded">
                            <p class="text-gray-500">No chart data available.</p>
                        </div>
                    `;
                }
            }


            // Map markers are removed in this version for simplicity, as per previous discussion.
            // If you want them back, uncomment the Leaflet map init in HTML and add the following logic here:
            /*
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            if (data.reports && data.reports.length > 0) {
                data.reports.forEach(report => {
                    if (report.latitude && report.longitude) {
                        let markerColor = 'blue';
                        if (report.status === 'warning') markerColor = 'red';
                        else if (report.status === 'watch') markerColor = 'orange';
                        else if (report.status === '') markerColor = 'gray';

                        const popupContent = `<b>${report.region}</b><br>Level: ${report.level}m<br>Status: ${report.status || 'N/A'}<br>Updated: ${new Date(report.recorded_at).toLocaleString()}`;

                        const marker = L.marker([report.latitude, report.longitude], {
                            icon: L.divIcon({
                                className: `custom-div-icon map-marker`,
                                html: `<i class="fa-solid fa-location-dot fa-2xl" style="color: ${markerColor};"></i>`,
                                iconSize: [30, 42],
                                iconAnchor: [15, 42],
                                popupAnchor: [0, -35]
                            })
                        }).addTo(map).bindPopup(popupContent);
                    }
                });
            }
            */


        } catch (error) {
            console.error('Error fetching report data:', error);
            document.getElementById('affected-areas-count').textContent = 'N/A';
            document.getElementById('evacuated-count').textContent = 'N/A';
            document.getElementById('damaged-homes-count').textContent = 'N/A';
            document.getElementById('rescue-teams-count').textContent = 'N/A';
            document.getElementById('damage-assessment-table-body').innerHTML = `
                <tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Failed to load damage assessment data.</td></tr>
            `;
             document.getElementById('peak-flood-level').textContent = 'N/A';
             document.getElementById('peak-flood-time').textContent = 'N/A';
             document.getElementById('flood-duration').textContent = 'N/A';
            
            // Clear and display message for the chart when error occurs
            const chartCanvas = document.getElementById('historicalFloodChart');
            const chartContainer = chartCanvas.closest('.chart-container');
            if (window.historicalFloodChartInstance) {
                window.historicalFloodChartInstance.destroy();
                window.historicalFloodChartInstance = null;
            }
            if (chartContainer) {
                chartContainer.innerHTML = `
                    <div class="w-full h-full flex items-center justify-center bg-gray-50 rounded">
                        <p class="text-red-500">Failed to load chart data.</p>
                    </div>
                `;
            }
        }
    }

    fetchReportData(); // Call on page load
    setInterval(fetchReportData, 60000); // Refresh data every minute
  </script>
</body>
</html>