<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Monitoring Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@preline/preline@2.0.0/dist/preline.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        darkblue: {
                            900: '#0a1a2e',
                            800: '#0f2a4a',
                            700: '#143a66',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .flood-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            height: 300px; /* Define height for the chart */
            width: 100%; /* Penting agar Chart.js responsif penuh */
            position: relative; /* Penting untuk Chart.js agar responsif */
        }
        .water-level-indicator {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #38bdf8 0%, #0ea5e9 50%, #0369a1 100%);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-darkblue-900 min-h-full">
    <div class="flex flex-col min-h-screen">
        <header class="bg-white dark:bg-darkblue-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <img src="asset/logo.jpg" alt="FloodWatch Logo" class="h-8 w-8 mr-2">
                            <span class="text-xl font-bold text-gray-800 dark:text-white">FloodWatch</span>
                        </div>
                        <nav class="hidden md:flex space-x-8">
                            <a href="index.php" class="text-primary-600 dark:text-primary-400 font-medium">Dashboard</a>
                            <a href="alert.php" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium">Alerts</a>
                            <a href="report.php" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 font-medium">Reports</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="theme-toggle" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-darkblue-700">
                            <i class="fas fa-moon dark:hidden"></i>
                            <i class="fas fa-sun hidden dark:inline"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Flood Monitoring Dashboard</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Real-time flood data and alerts for your area</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow flood-card transition-all duration-300 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Flood Warnings</p>
                                <p class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-400" id="active-warnings">--</p>
                            </div>
                            <div class="p-3 rounded-full bg-primary-50 dark:bg-darkblue-700 text-primary-600 dark:text-primary-400">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Since yesterday</span>
                                <span class="font-medium text-red-500">+2</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow flood-card transition-all duration-300 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Affected Areas</p>
                                <p class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-400" id="affected-areas">--</p>
                            </div>
                            <div class="p-3 rounded-full bg-primary-50 dark:bg-darkblue-700 text-primary-600 dark:text-primary-400">
                                <i class="fas fa-map-marked-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Since yesterday</span>
                                <span class="font-medium text-red-500">+3</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow flood-card transition-all duration-300 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Water Level (avg)</p>
                                <p class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-400" id="avg-water-level">--</p>
                            </div>
                            <div class="p-3 rounded-full bg-primary-50 dark:bg-darkblue-700 text-primary-600 dark:text-primary-400">
                                <i class="fas fa-tint text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Danger level: 4.0m</span>
                                <span class="font-medium text-yellow-500">+0.5m</span>
                            </div>
                            <div class="mt-2 water-level-indicator">
                                <div class="h-full bg-primary-500 rounded" style="width: 80%" id="avg-water-level-bar"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow flood-card transition-all duration-300 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Evacuated People</p>
                                <p class="mt-1 text-3xl font-semibold text-primary-600 dark:text-primary-400" id="evacuated-people">--</p>
                            </div>
                            <div class="p-3 rounded-full bg-primary-50 dark:bg-darkblue-700 text-primary-600 dark:text-primary-400">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Since yesterday</span>
                                <span class="font-medium text-red-500">+320</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow p-6 lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Water Level Trends</h2>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 text-xs bg-primary-50 dark:bg-darkblue-700 text-primary-600 dark:text-primary-400 rounded-full">7 Days</button>
                                <button class="px-3 py-1 text-xs bg-white dark:bg-darkblue-800 text-gray-600 dark:text-gray-300 rounded-full border border-gray-200 dark:border-darkblue-700">30 Days</button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="waterLevelChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Most Affected Areas</h2>
                        <div class="space-y-4" id="affected-areas-list">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-50 dark:bg-darkblue-700 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Loading...</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading flood data...</p>
                                    <div class="mt-1 water-level-indicator">
                                        <div class="h-full bg-primary-500 rounded" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-darkblue-800 rounded-lg shadow overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-darkblue-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Recent Flood Alerts</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-darkblue-700" id="recent-alerts-list">
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-darkblue-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Loading alerts...</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Fetching recent flood alerts.</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Just now</p>
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="px-6 py-4 bg-gray-50 dark:bg-darkblue-700 text-center">
    <a href="alert.php" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300">
        View All Alerts <i class="fas fa-chevron-right ml-1"></i>
    </a>
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
        document.addEventListener("DOMContentLoaded", function () {
    const html = document.documentElement;
    const themeToggle = document.getElementById("theme-toggle");

    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        html.classList.add("dark");
    } else {
        html.classList.remove("dark");
    }

    themeToggle.addEventListener("click", () => {
        html.classList.toggle("dark");

        if (html.classList.contains("dark")) {
            localStorage.setItem("theme", "dark");
        } else {
            localStorage.setItem("theme", "light");
        }

        themeToggle.querySelector(".fa-sun").classList.toggle("hidden");
        themeToggle.querySelector(".fa-moon").classList.toggle("hidden");
    });

    // Function to calculate time ago
    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // Declare a chart variable outside the function to manage its instance
    let waterLevelChartInstance = null;

    // Function to fetch and display flood data from the backend API
    async function fetchFloodData() {
        try {
            const response = await fetch('api/flood_data.php');
            const apiData = await response.json(); // Menggunakan apiData untuk objek penuh

            // Pastikan apiData.all_reports ada dan merupakan array untuk data chart
            const floodDataForChart = apiData.all_reports && Array.isArray(apiData.all_reports) ? apiData.all_reports : [];
            // Pastikan apiData.latest_per_region ada dan merupakan array untuk daftar area terdampak dan alerts
            const latestRegionData = apiData.latest_per_region && Array.isArray(apiData.latest_per_region) ? apiData.latest_per_region : [];
            // Pastikan summaryData ada
            const summaryData = apiData.summary || {};


            // Update "Most Affected Areas" dan hitung statistik
            const affectedAreasList = document.getElementById('affected-areas-list');
            affectedAreasList.innerHTML = ''; // Clear existing content

            let totalWarnings = 0;
            let totalAffectedAreas = 0;
            let totalEvacuated = 0;
            let totalWaterLevel = 0; // Untuk hitung rata-rata
            let countWaterLevel = 0; // Untuk hitung rata-rata
            const dangerLevel = 4.0; // Tentukan level bahaya di sini

            if (latestRegionData.length > 0) { // Hanya lanjutkan jika ada data
                latestRegionData.forEach(item => {
                    // Perhatikan: item.danger_level_m bisa null dari database jika tidak diisi
                    const itemDangerLevel = parseFloat(item.danger_level_m) || dangerLevel; // Fallback ke dangerLevel default jika null
                    const percentage = (parseFloat(item.level) / itemDangerLevel) * 100;

                    let statusColorClass = '';
                    let iconColorClass = '';
                    let bgColorClass = '';

                    if (item.status === 'warning') {
                        statusColorClass = 'text-red-500';
                        iconColorClass = 'text-red-600';
                        bgColorClass = 'bg-red-100';
                    } else if (item.status === 'watch') {
                        statusColorClass = 'text-yellow-500';
                        iconColorClass = 'text-yellow-600';
                        bgColorClass = 'bg-yellow-100';
                    } else { // update
                        statusColorClass = 'text-green-500';
                        iconColorClass = 'text-blue-600'; // Using blue for normal updates
                        bgColorClass = 'bg-blue-100';
                    }

                    totalWaterLevel += parseFloat(item.level);
                    countWaterLevel++;

                    affectedAreasList.innerHTML += `
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full ${bgColorClass} dark:bg-darkblue-700 flex items-center justify-center ${iconColorClass} dark:text-primary-400">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${item.region}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Water level: ${item.level}m (${percentage.toFixed(0)}% of danger level)</p>
                                <div class="mt-1 water-level-indicator">
                                    <div class="h-full ${statusColorClass.replace('text-', 'bg-')} rounded" style="width: ${percentage > 100 ? 100 : percentage}%"></div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                affectedAreasList.innerHTML = `
                    <div class="text-center text-gray-500 py-4">No affected areas data available.</div>
                `;
            }


            // Update stats cards menggunakan summaryData dari API
            document.getElementById('active-warnings').textContent = summaryData.total_warnings !== undefined ? summaryData.total_warnings : 'N/A';
            document.getElementById('affected-areas').textContent = summaryData.affected_areas_count !== undefined ? summaryData.affected_areas_count : 'N/A';
            document.getElementById('evacuated-people').textContent = (summaryData.evacuated_count !== undefined) ? summaryData.evacuated_count.toLocaleString() : 'N/A';

            // Perhitungan rata-rata level air (masih berdasarkan data yang diambil)
            if (countWaterLevel > 0) {
                const avgLevel = (totalWaterLevel / countWaterLevel).toFixed(1);
                document.getElementById('avg-water-level').textContent = `${avgLevel}m`;
                const avgPercentage = (avgLevel / dangerLevel) * 100;
                let avgLevelBarColor = 'bg-primary-500'; // Default
                if (avgPercentage > 80) avgLevelBarColor = 'bg-red-500';
                else if (avgPercentage > 50) avgLevelBarColor = 'bg-yellow-500';
                document.getElementById('avg-water-level-bar').style.width = `${avgPercentage > 100 ? 100 : avgPercentage}%`;
                document.getElementById('avg-water-level-bar').className = `h-full ${avgLevelBarColor} rounded`;
            } else {
                document.getElementById('avg-water-level').textContent = '--';
                document.getElementById('avg-water-level-bar').style.width = '0%';
                document.getElementById('avg-water-level-bar').className = `h-full bg-primary-500 rounded`;
            }


            // --- Start Water Level Chart Logic (Menggunakan floodDataForChart = all_reports) ---
            const chartLabels = []; // Labels for X-axis (timestamps)
            const datasets = []; // Data series for each region
            const colors = ['#38bdf8', '#0ea5e9', '#0284c7', '#0369a1', '#7DD3FC', '#00D2FF']; // More colors

            if (floodDataForChart.length > 0) { // Hanya gambar chart jika ada data
                const groupedData = {};
                // floodDataForChart sudah diurutkan berdasarkan recorded_at ASC dari PHP API
                floodDataForChart.forEach(item => {
                    if (!groupedData[item.region]) {
                        groupedData[item.region] = [];
                    }
                    groupedData[item.region].push(item);
                    // Collect all unique recorded_at timestamps (formatted as readable time) for labels
                    // Menggunakan full date-time agar unique dan urut di sumbu X
                    const recordedAtLabel = new Date(item.recorded_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    if (!chartLabels.includes(recordedAtLabel)) {
                        chartLabels.push(recordedAtLabel);
                    }
                });

                // Urutkan chartLabels secara kronologis (berdasarkan objek Date, bukan hanya string)
                chartLabels.sort((a, b) => {
                    const dateA = new Date(a);
                    const dateB = new Date(b);
                    return dateA - dateB;
                });


                let colorIndex = 0;
                for (const region in groupedData) {
                    const dataPoints = [];
                    const regionData = groupedData[region];

                    // Isi data points untuk setiap label (waktu), menangani nilai null untuk gap data
                    chartLabels.forEach(label => {
                        const found = regionData.find(item => new Date(item.recorded_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) === label);
                        dataPoints.push(found ? parseFloat(found.level) : null);
                    });

                    datasets.push({
                        label: region,
                        data: dataPoints,
                        borderColor: colors[colorIndex % colors.length],
                        backgroundColor: colors[colorIndex % colors.length] + '40', // 40 for transparency
                        fill: false, // Jangan mengisi area di bawah garis
                        tension: 0.1, // Garis melengkung
                        spanGaps: true // Menghubungkan titik-titik data yang terlewat (jika ada null)
                    });
                    colorIndex++;
                }

                const ctx = document.getElementById('waterLevelChart').getContext('2d');

                // Hancurkan instance chart yang ada sebelum membuat yang baru
                if (waterLevelChartInstance) {
                    waterLevelChartInstance.destroy();
                }

                waterLevelChartInstance = new Chart(ctx, {
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
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'white' : 'black'
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
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'white' : 'black'
                                },
                                ticks: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'white' : 'black'
                                },
                                grid: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                                }
                            },
                            x: {
                                type: 'category', // Tetap 'category' karena label sudah diformat string unik per waktu
                                title: {
                                    display: true,
                                    text: 'Time',
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'white' : 'black'
                                },
                                ticks: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'white' : 'black',
                                    maxRotation: 45, // Rotasi label agar tidak tumpang tindih
                                    minRotation: 45
                                },
                                grid: {
                                    color: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                                }
                            }
                        }
                    }
                });
            } else {
                // Tampilkan pesan jika tidak ada data chart
                if (waterLevelChartInstance) {
                    waterLevelChartInstance.destroy();
                    waterLevelChartInstance = null;
                }
                const chartContainer = document.getElementById('waterLevelChart').closest('.chart-container');
                if (chartContainer) {
                    chartContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-darkblue-700 rounded">
                            <p class="text-gray-500 dark:text-gray-400">No chart data available. Add historical flood data for trends.</p>
                        </div>
                    `;
                }
            }
            // --- End Water Level Chart Logic ---


            // Update "Recent Flood Alerts"
            const recentAlertsList = document.getElementById('recent-alerts-list');
            recentAlertsList.innerHTML = '';

            if (latestRegionData.length > 0) {
                // Urutkan berdasarkan yang paling baru untuk alerts (latestRegionData sudah diurutkan berdasarkan status, tapi bisa disort ulang jika perlu)
                latestRegionData.sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at)); // Urutkan lagi berdasarkan waktu
                latestRegionData.slice(0, 3).forEach(item => { // Ambil 3 teratas
                    let alertText = '';
                    let alertIconClass = '';
                    let alertBgClass = '';
                    let alertTextColor = '';
                    let alertDetail = `Water levels at ${item.level}m`;

                    if (item.status === 'warning') {
                        alertText = `Severe Flood Warning - ${item.region}`;
                        alertIconClass = 'fas fa-exclamation-circle';
                        alertBgClass = 'bg-red-100 dark:bg-red-900/30';
                        alertTextColor = 'text-red-600 dark:text-red-400';
                        alertDetail += ', evacuation advised.';
                    } else if (item.status === 'watch') {
                        alertText = `Flood Watch - ${item.region}`;
                        alertIconClass = 'fas fa-exclamation-triangle';
                        alertBgClass = 'bg-yellow-100 dark:bg-yellow-900/30';
                        alertTextColor = 'text-yellow-600 dark:text-yellow-400';
                        alertDetail += ', monitor conditions.';
                    } else { // update
                        alertText = `Water Level Update - ${item.region}`;
                        alertIconClass = 'fas fa-info-circle';
                        alertBgClass = 'bg-blue-100 dark:bg-blue-900/30';
                        alertTextColor = 'text-blue-600 dark:text-blue-400';
                    }

                    const recordedAt = new Date(item.recorded_at);
                    const timeAgo = getTimeAgo(recordedAt);

                    recentAlertsList.innerHTML += `
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-darkblue-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full ${alertBgClass} flex items-center justify-center ${alertTextColor}">
                                    <i class="${alertIconClass}"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${alertText}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">${alertDetail}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><i class="far fa-clock mr-1"></i> ${timeAgo}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                recentAlertsList.innerHTML = `
                    <div class="px-6 py-4 text-center text-gray-500">No recent flood alerts available.</div>
                `;
            }


        } catch (error) {
            console.error('Error fetching flood data:', error);
            document.getElementById('active-warnings').textContent = 'N/A';
            document.getElementById('affected-areas').textContent = 'N/A';
            document.getElementById('evacuated-people').textContent = 'N/A';
            document.getElementById('avg-water-level').textContent = 'N/A';
            document.getElementById('affected-areas-list').innerHTML = '<p class="text-red-500 px-4 py-2">Failed to load flood data.</p>';
            document.getElementById('recent-alerts-list').innerHTML = '<p class="px-6 py-4 text-red-500">Failed to load recent alerts.</p>';
            // Destroy chart on error
            if (waterLevelChartInstance) {
                waterLevelChartInstance.destroy();
                waterLevelChartInstance = null;
            }
            const chartContainer = document.getElementById('waterLevelChart').closest('.chart-container');
            if (chartContainer) {
                chartContainer.innerHTML = `
                    <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-darkblue-700 rounded">
                        <p class="text-red-500 dark:text-red-400">Failed to load chart data.</p>
                    </div>
                `;
            }
        }
    }

    fetchFloodData(); // Call the function to load data on page load
    setInterval(fetchFloodData, 60000); // Refresh data every 60 seconds
});
</script>
</body>
</html>