<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Alert System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Map Container Styling */
        #map {
            height: 400px;
            width: 100%;
        }
        .water-animation {
            background: linear-gradient(180deg, #3a7bd5 0%, #00d2ff 100%);
            background-size: 200% 200%;
            animation: waterFlow 8s ease infinite;
        }

        @keyframes waterFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .pulse-alert {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
        }

        .flood-gauge {
            transition: width 1.5s ease-out;
        }

        /* Kelas kustom untuk ikon marker Leaflet */
        .custom-div-icon {
            background-color: transparent;
            border: none;
        }
        .custom-div-icon i {
            filter: drop-shadow(0 0 3px rgba(0,0,0,0.5));
            animation: bounce 1.5s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }

        /* Warna severity (opsional, bisa diatur juga via JS) */
        .severity-low { background-color: #10B981; }
        .severity-medium { background-color: #F59E0B; }
        .severity-high { background-color: #EF4444; }
        .severity-extreme { background-color: #7C3AED; }

        /* CSS untuk modal dan efek blur */
        #floodWarningModal {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-water text-3xl"></i>
                    <h1 class="text-2xl font-bold">FloodAlert System</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="hidden md:block">Last updated: <span id="update-time" class="font-semibold">Just now</span></span>

                    <a href="index.php" class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>

                </div>
            </div>
        </header>

        <main class="flex-grow container mx-auto px-4 py-6">
            <div id="floodWarningModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 relative">
                    <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                    <h3 class="text-2xl font-bold text-red-700 mb-4">FLOOD WARNING DETAILS</h3>
                    <div id="modalContent" class="text-gray-700 space-y-3">
                        <p><strong class="font-semibold">Affected Area:</strong> <span id="modalArea">--</span></p>
                        <p><strong class="font-semibold">Severity:</strong> <span id="modalSeverity" class="text-red-600">--</span></p>
                        <p><strong class="font-semibold">Current Level:</strong> <span id="modalLevel">-- m</span></p>
                        <p><strong class="font-semibold">Danger Level:</strong> <span id="modalDangerLevel">-- m</span></p>
                        <p><strong class="font-semibold">Status:</strong> <span id="modalStatus">--</span></p>
                        <p><strong class="font-semibold">Last Updated:</strong> <span id="modalUpdated">--</span></p>
                        <p><strong class="font-semibold">Recommendations:</strong> <span id="modalRecommendation">--</span></p>
                        <p class="text-sm mt-4 text-gray-600 border-t pt-3">For more comprehensive reports, please visit the <a href="report.php" class="text-blue-600 hover:underline">Reports Page</a>.</p>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700" onclick="window.open('report.php', '_blank')">View Full Report</button>
                    </div>
                </div>
            </div>

            <div class="bg-red-600 text-white rounded-xl shadow-lg mb-6 pulse-alert" id="main-alert-banner">
                <div class="p-4 flex flex-col items-start">
                    <div class="flex items-center space-x-4 w-full">
                        <i class="fas fa-exclamation-triangle text-3xl flex-shrink-0"></i>
                        <div class="flex-grow">
                            <h2 class="text-xl font-bold" id="banner-title">FLOOD WARNING</h2>
                            <p class="text-sm" id="banner-description">Severe flooding expected in Central District - Evacuation recommended</p>
                        </div>
                    </div>
                    <button id="banner-view-details-btn" class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 mt-4 self-end">
                        View Details
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold">Flood Risk Map</h3>
                            <div class="flex space-x-2">
                                <button class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm">
                                    <i class="fas fa-layer-group mr-1"></i> Layers
                                </button>
                                <button class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm">
                                    <i class="fas fa-expand mr-1"></i> Fullscreen
                                </button>
                            </div>
                        </div>
                        <div id="map"></div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Flood Level Monitoring</h3>
                        </div>
                        <div class="p-4" id="river-level-monitoring">
                            <p class="text-gray-500 text-center">Loading river level data...</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg weather-forecast">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Weather Forecast</h3>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-semibold">Today</h4>
                                    <p class="text-sm text-gray-600">Loading...</p>
                                </div>
                                <div class="text-3xl text-blue-600">
                                    <i class="fas fa-cloud"></i> </div>
                                <div class="text-right">
                                    <span class="font-bold">--°C</span>
                                    <span class="text-sm text-gray-500">/ --°C</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="p-2">
                                    <p class="text-xs font-medium">Mon</p>
                                    <i class="fas fa-cloud-rain text-blue-500 my-1"></i>
                                    <p class="text-xs">--° / --°</p>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-medium">Tue</p>
                                    <i class="fas fa-cloud-sun text-yellow-500 my-1"></i>
                                    <p class="text-xs">30° / 25°</p>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-medium">Wed</p>
                                    <i class="fas fa-sun text-yellow-500 my-1"></i>
                                    <p class="text-xs">32° / 26°</p>
                                </div>
                                <div class="p-2">
                                    <p class="text-xs font-medium">Thu</p>
                                    <i class="fas fa-cloud text-gray-400 my-1"></i>
                                    <p class="text-xs">29° / 25°</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Active Flood Alerts</h3>
                        </div>
                        <div class="divide-y divide-gray-200" id="active-flood-alerts">
                            <div class="p-4 text-gray-500 text-center">Loading alerts...</div>
                        </div>
                        <div class="p-4 border-t border-gray-200 text-center">
    <a href="alert.php" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
        View All Alerts <i class="fas fa-chevron-right ml-1"></i>
    </a>
</div>

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-4 text-center">
        <a href="add_alert.php" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> Add New Alert
        </a>
    </div>

    </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Flood Safety Tips</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 bg-blue-100 p-2 rounded-full">
                                        <i class="fas fa-home text-blue-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold">Prepare Your Home</h4>
                                        <p class="text-sm text-gray-600">Move valuables to higher floors and turn off utilities if instructed.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-car text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold">Avoid Driving</h4>
                                        <p class="text-sm text-gray-600">Just 15cm of moving water can knock you down, 30cm can sweep your vehicle away.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex-shrink-0 bg-purple-100 p-2 rounded-full">
                                        <i class="fas fa-phone-alt text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold">Emergency Contacts</h4>
                                        <p class="text-sm text-gray-600">Local emergency: 112<br>Flood hotline: 0800 123 4567</p>
                                    </div>
                                </div>
                            </div>
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
                            <i class="fas fa-water mr-2"></i> FloodAlert System
                        </h2>
                        <p class="text-gray-400 text-sm mt-1">Real-time flood monitoring and early warning system</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-blue-300"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-blue-300"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-blue-300"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-blue-300"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-6 pt-6 text-sm text-gray-400">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div class="mb-4 md:mb-0">
                            <p>&copy; 2023 FloodAlert System. All rights reserved.</p>
                        </div>
                        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-6">
                            <a href="#" class="hover:text-white">Privacy Policy</a>
                            <a href="#" class="hover:text-white">Terms of Service</a>
                            <a href="#" class="hover:text-white">Contact Us</a>
                            <a href="#" class="hover:text-white">API Documentation</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Fungsi untuk mengupdate waktu terakhir diperbarui
        function updateLastUpdatedTime() {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('update-time').textContent = now.toLocaleDateString('en-US', options);
        }

        // Fungsi untuk mengambil data cuaca dari API proxy BMKG
        async function fetchWeatherData() {
            try {
                const response = await fetch('api/bmkg_weather.php');
                const data = await response.json();

                if (data.error) {
                    console.error("Error from weather API proxy:", data.error);
                    document.querySelector('.weather-forecast h4 + p').textContent = 'Error loading weather';
                    document.querySelector('.weather-forecast .font-bold').textContent = '--°C';
                    const weatherIcon = document.querySelector('.weather-forecast .text-3xl i');
                    if(weatherIcon) {
                        weatherIcon.className = 'fas fa-exclamation-circle text-red-500';
                    }
                    const dailyForecastDivs = document.querySelectorAll('.weather-forecast .grid.grid-cols-4 > div');
                    dailyForecastDivs.forEach(div => {
                        div.querySelector('p:first-child').textContent = 'N/A';
                        const icon = div.querySelector('i');
                        if(icon) icon.className = 'fas fa-question text-gray-400 my-1';
                        div.querySelector('p:last-child').textContent = '--° / --°';
                    });
                    return;
                }

                // --- Perbarui "Today" Weather ---
                const weatherIconElement = document.querySelector('.weather-forecast .text-3xl i');
                const weatherDescElement = document.querySelector('.weather-forecast h4 + p');
                const weatherTempElement = document.querySelector('.weather-forecast .text-right .font-bold');
                const weatherMinMaxElement = document.querySelector('.weather-forecast .text-right .text-sm');

                if (weatherIconElement) {
                    weatherIconElement.className = data.weather_icon_code;
                }
                if (weatherDescElement) {
                    weatherDescElement.textContent = data.weather_description;
                }
                if (weatherTempElement) {
                    weatherTempElement.textContent = `${parseFloat(data.current_temp).toFixed(0)}°C`;
                }
                if (weatherMinMaxElement) {
                    weatherMinMaxElement.textContent = `/ ${parseFloat(data.min_temp).toFixed(0)}°C`;
                }

                // --- Perbarui Ramalan Cuaca Harian (Mon, Tue, Wed, Thu) ---
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
                    dailyForecastDivs.forEach(div => {
                        div.querySelector('p:first-child').textContent = 'N/A';
                        const icon = div.querySelector('i');
                        if(icon) icon.className = 'fas fa-question text-gray-400 my-1';
                        div.querySelector('p:last-child').textContent = '--° / --°';
                    });
                }

            } catch (error) {
                console.error('Error fetching weather data from proxy:', error);
                document.querySelector('.weather-forecast h4 + p').textContent = 'Koneksi error';
                document.querySelector('.weather-forecast .font-bold').textContent = '--°C';
                const weatherIcon = document.querySelector('.weather-forecast .text-3xl i');
                if(weatherIcon) weatherIcon.className = 'fas fa-wifi text-red-500';
                const dailyForecastDivs = document.querySelectorAll('.weather-forecast .grid.grid-cols-4 > div');
                dailyForecastDivs.forEach(div => {
                    div.querySelector('p:first-child').textContent = 'N/A';
                    const icon = div.querySelector('i');
                    if(icon) icon.className = 'fas fa-question text-gray-400 my-1';
                    div.querySelector('p:last-child').textContent = '--° / --°';
                });
            }
        }


        // Initialize the Leaflet map, centered on Pekanbaru, Indonesia
        const map = L.map('map').setView([0.507068, 101.447743], 12);

        // Set up the map tiles using OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let activeMarkers = {};

        // Function to fetch and display flood data from the backend API for alerts page
        async function fetchAlertData() {
            try {
                const response = await fetch('api/alert_data.php');
                const apiData = await response.json();

                // Dapatkan peringatan paling parah (misal: warning status, atau level tertinggi)
                const mainWarning = apiData.alerts.length > 0 ? apiData.alerts[0] : null;

                // Update Main Alert Banner
                const mainAlertBanner = document.getElementById('main-alert-banner');
                const bannerTitle = document.getElementById('banner-title');
                const bannerDescription = document.getElementById('banner-description');
                const bannerViewDetailsBtn = document.getElementById('banner-view-details-btn');

                if (mainWarning) {
                    mainAlertBanner.classList.remove('bg-red-600', 'bg-yellow-600', 'bg-blue-600', 'bg-gray-500');
                    mainAlertBanner.classList.add('pulse-alert');

                    if (mainWarning.status === 'warning') {
                        bannerTitle.textContent = 'FLOOD WARNING';
                        bannerDescription.textContent = `Severe flooding expected in ${mainWarning.region} - Evacuation recommended`;
                        mainAlertBanner.classList.add('bg-red-600');
                        mainAlertBanner.classList.add('pulse-alert');
                    } else if (mainWarning.status === 'watch') {
                        bannerTitle.textContent = 'FLOOD WATCH';
                        bannerDescription.textContent = `Moderate flooding possible in ${mainWarning.region} - Monitor conditions`;
                        mainAlertBanner.classList.add('bg-yellow-600');
                        mainAlertBanner.classList.remove('pulse-alert');
                    } else { // status === 'update'
                         bannerTitle.textContent = 'WATER LEVEL UPDATE';
                         bannerDescription.textContent = `Current level in ${mainWarning.region} is ${mainWarning.level}m.`;
                         mainAlertBanner.classList.add('bg-blue-600');
                         mainAlertBanner.classList.remove('pulse-alert');
                    }

                    bannerViewDetailsBtn.style.display = '';
                    bannerViewDetailsBtn.onclick = function() {
                        showFloodWarningDetails(mainWarning);
                    };

                } else {
                    bannerTitle.textContent = 'NO ACTIVE FLOOD WARNINGS';
                    bannerDescription.textContent = 'All monitored areas are currently at normal levels.';
                    mainAlertBanner.classList.remove('pulse-alert', 'bg-red-600', 'bg-yellow-600', 'bg-blue-600');
                    mainAlertBanner.classList.add('bg-gray-500');
                    bannerViewDetailsBtn.style.display = 'none';
                }


                // Update "Active Flood Alerts" list
                const activeAlertsList = document.getElementById('active-flood-alerts');
                activeAlertsList.innerHTML = '';

                if (apiData.alerts && apiData.alerts.length > 0) {
                    apiData.alerts.forEach(item => {
                        let alertIconClass = '';
                        let alertBgClass = '';
                        let alertTextColor = '';
                        let alertTitle = '';
                        let alertDetail = '';

                        const recordedAt = new Date(item.recorded_at);
                        const timeDiffMinutes = Math.round((new Date() - recordedAt) / (1000 * 60));
                        let timeAgoText;
                        if (timeDiffMinutes < 60) {
                            timeAgoText = `${timeDiffMinutes} min ago`;
                        } else {
                            timeAgoText = `${Math.round(timeDiffMinutes / 60)} hour(s) ago`;
                        }


                        if (item.status === 'warning') {
                            alertTitle = `Severe Flood Warning`;
                            alertDetail = `${item.region} - Evacuation in progress`;
                            alertIconClass = 'fas fa-exclamation-circle';
                            alertBgClass = 'bg-red-100';
                            alertTextColor = 'text-red-600';
                        } else if (item.status === 'watch') {
                            alertTitle = `Flood Warning`;
                            alertDetail = `${item.region} - Prepare for possible evacuation`;
                            alertIconClass = 'fas fa-exclamation-triangle';
                            alertBgClass = 'bg-yellow-100';
                            alertTextColor = 'text-yellow-600';
                        } else { // status === 'update'
                            alertTitle = `Flood Watch`;
                            alertDetail = `${item.region} - Monitor conditions`;
                            alertIconClass = 'fas fa-info-circle';
                            alertBgClass = 'bg-blue-100';
                            alertTextColor = 'text-blue-600';
                        }

                        activeAlertsList.innerHTML += `
                            <div class="p-4 hover:bg-gray-50 cursor-pointer">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 ${alertBgClass} p-2 rounded-full">
                                        <i class="${alertIconClass} ${alertTextColor}"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold ${alertTextColor}">${alertTitle}</h4>
                                        <p class="text-sm text-gray-600">${alertDetail}</p>
                                        <p class="text-xs text-gray-500 mt-1"><i class="far fa-clock mr-1"></i> Updated ${timeAgoText}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Update map markers
                        if (item.latitude && item.longitude) {
                            const markerId = item.region;
                            let markerColor = 'blue';
                            if (item.status === 'warning') markerColor = 'red';
                            else if (item.status === 'watch') markerColor = 'orange';

                            const popupContent = `<b>${item.region}</b><br>Level: ${item.level}m<br>Status: ${item.status}<br>Updated: ${new Date(item.recorded_at).toLocaleString()}`;

                            if (activeMarkers[markerId]) {
                                activeMarkers[markerId].setLatLng([item.latitude, item.longitude]).setPopupContent(popupContent);
                                const iconElement = activeMarkers[markerId].getIcon()._element.querySelector('i');
                                if (iconElement) {
                                    iconElement.style.color = markerColor;
                                }
                            } else {
                                const marker = L.marker([item.latitude, item.longitude], {
                                    icon: L.divIcon({
                                        className: `custom-div-icon map-marker`,
                                        html: `<i class="fa-solid fa-location-dot fa-2xl" style="color: ${markerColor};"></i>`,
                                        iconSize: [30, 42],
                                        iconAnchor: [15, 42],
                                        popupAnchor: [0, -35]
                                    })
                                }).addTo(map).bindPopup(popupContent);
                                activeMarkers[markerId] = marker;
                            }
                        }
                    });

                    const currentRegions = new Set(apiData.alerts.map(item => item.region));
                    for (const id in activeMarkers) {
                        if (!currentRegions.has(id)) {
                            map.removeLayer(activeMarkers[id]);
                            delete activeMarkers[id];
                        }
                    }

                } else {
                    activeAlertsList.innerHTML = '<div class="p-4 text-gray-500 text-center">No active flood alerts.</div>';
                    for (const id in activeMarkers) {
                        map.removeLayer(activeMarkers[id]);
                    }
                    activeMarkers = {};
                }

                // Update "Flood Level Monitoring" (River Levels)
                const riverLevelMonitoringDiv = document.getElementById('river-level-monitoring');
                riverLevelMonitoringDiv.innerHTML = '';

                if (apiData.river_levels && apiData.river_levels.length > 0) {
                    apiData.river_levels.forEach(river => {
                        const maxLevel = parseFloat(river.danger_level_m) || 8.0;
                        const currentLevel = parseFloat(river.level);
                        const percentage = (currentLevel / maxLevel) * 100;
                        let levelStatusText = 'Normal Level';
                        let gaugeColorClass = 'bg-green-500';

                        if (river.status === 'warning') {
                            levelStatusText = 'Warning Level';
                            gaugeColorClass = 'bg-yellow-500';
                        } else if (currentLevel >= maxLevel) {
                            levelStatusText = 'Critical Level';
                            gaugeColorClass = 'bg-red-600';
                        } else if (river.status === 'extreme') {
                             levelStatusText = 'Extreme Level';
                             gaugeColorClass = 'bg-purple-600';
                        }

                        const labels = [];
                        const numLabels = 5;
                        for (let i = 0; i < numLabels; i++) {
                            labels.push((maxLevel / (numLabels - 1) * i).toFixed(1) + 'm');
                        }

                        riverLevelMonitoringDiv.innerHTML += `
                            <div class="mt-4 flex justify-between mb-2">
                                <span class="text-sm font-medium">${river.region}</span>
                                <span class="text-sm font-semibold ${gaugeColorClass.replace('bg-', 'text-')}">${levelStatusText} (${currentLevel}m)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="flood-gauge ${gaugeColorClass} h-4 rounded-full" style="width: ${percentage > 100 ? 100 : percentage}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-500">
                                ${labels.map(label => `<span>${label}</span>`).join('')}
                            </div>
                        `;
                    });
                } else {
                    riverLevelMonitoringDiv.innerHTML = '<p class="text-gray-500 text-center">No river level data available.</p>';
                }


                updateLastUpdatedTime();

            } catch (error) {
                console.error('Error fetching alert data:', error);
                document.getElementById('active-flood-alerts').innerHTML = '<div class="p-4 text-red-500 text-center">Failed to load alerts.</div>';
                document.getElementById('river-level-monitoring').innerHTML = '<p class="text-red-500 text-center">Failed to load river level data.</p>';
                updateLastUpdatedTime();
            }
        }

        // --- Modal Logic ---
        const floodWarningModal = document.getElementById('floodWarningModal');
        const closeModalButton = document.getElementById('closeModal');

        function showFloodWarningDetails(alertData) {
            document.getElementById('modalArea').textContent = alertData.region || '--';
            document.getElementById('modalSeverity').textContent = (alertData.status ? alertData.status.charAt(0).toUpperCase() + alertData.status.slice(1) : '--');
            document.getElementById('modalLevel').textContent = (alertData.level !== undefined ? `${alertData.level} m` : '-- m');
            document.getElementById('modalDangerLevel').textContent = (alertData.danger_level_m !== undefined ? `${alertData.danger_level_m} m` : '-- m');
            document.getElementById('modalStatus').textContent = (alertData.status === 'warning' ? 'Evacuation Recommended' : (alertData.status === 'watch' ? 'Monitor Conditions' : (alertData.status === 'update' ? 'Normal Level' : '--')));
            document.getElementById('modalUpdated').textContent = alertData.recorded_at ? new Date(alertData.recorded_at).toLocaleString() : '--';
            document.getElementById('modalRecommendation').textContent = (alertData.status === 'warning' ? 'Evacuate immediately to higher ground. Follow local emergency instructions.' : (alertData.status === 'watch' ? 'Prepare for possible evacuation. Stay informed about changing conditions.' : 'Remain vigilant and monitor official updates.'));

            floodWarningModal.classList.remove('hidden');
        }

        closeModalButton.addEventListener('click', function() {
            floodWarningModal.classList.add('hidden');
        });

        floodWarningModal.addEventListener('click', function(event) {
            if (event.target === floodWarningModal) {
                floodWarningModal.classList.add('hidden');
            }
        });
        // --- End Modal Logic ---


        // Run on page load and periodically
        document.addEventListener('DOMContentLoaded', function() {
            fetchAlertData();
            fetchWeatherData();
        });
        setInterval(fetchAlertData, 30000);
        setInterval(fetchWeatherData, 10 * 60 * 1000);
    </script>
</body>
</html>