# Flood Monitoring and Early Warning System for Pekanbaru

-----

This web-based application is currently **95% complete** and designed to help **monitor flood conditions**, **provide early warnings**, and **generate comprehensive reports** for flood incidents in Pekanbaru City, Indonesia. It integrates local database information with external weather APIs to deliver relevant and up-to-date insights, aiming to be a vital tool for disaster preparedness.

-----

## Key Features

Here's what the system offers:

  - **Interactive Dashboard:** Get an overview of current flood statistics, affected areas, average water levels, and evacuation numbers.
  - **Water Level Trend Charts:** Visualize historical water level trends for various Pekanbaru regions using **Chart.js**.
  - **Dynamic Flood Alerts:** A dedicated page displaying active alerts, river levels, and real-time weather forecasts.
  - **Comprehensive Reports:** Generate detailed incident reports including damage assessments, historical data, and weather analysis.
  - **BMKG API Integration:** Fetches real-time weather forecasts from **BMKG** for current conditions and daily predictions.
  - **OpenWeather API Integration:** Provides **real-time temperature data** and supports **map functionalities** for enhanced geographical context.
  - **PDF Report Generation:** Export flood incident reports into PDF format (powered by **Dompdf**).
  - **Photo Documentation Upload (In Progress):** Functionality to upload flood-related photos that can be displayed in reports is actively being developed.
  - **Light/Dark Theme:** Toggle between light and dark user interface themes.
  - **User-Friendly Navigation:** Intuitive navigation across Dashboard, Alerts, and Reports pages.

-----

## Technologies Used

  - **Frontend:** HTML5, **Tailwind CSS**, JavaScript (ES6+), **Chart.js**, **Leaflet.js**, Font Awesome.
  - **Backend:** PHP 7.4+ (with **Composer**, **cURL**), MySQL/MariaDB (via **PDO**).
  - **External APIs:** **BMKG API** (Weather Forecasts), **OpenWeather API** (Temperature, Map Data), **kodewilayah.id** (Indonesian Administrative Regions).

-----

## Project Structure

```
projectdb/
├── api/                       # API endpoints for data handling and external services
├── assets/                    # Project assets (e.g., logo.jpg)
├── uploads/                   # Stores uploaded files (photos, thumbnails)
├── connection.php             # Database connection script
├── bajir.sql                  # Database schema (tables: flood_data, areas, users, flood_photos, damage_assessments)
├── index.php                  # Main Dashboard page
├── alert.php                  # Alerts page
├── report.php                 # Reports page
├── generate_pdf.php           # PDF generation script (Dompdf)
├── add_alert.php              # Form to add new flood alerts
├── upload_photo.php           # Form to upload new photos
├── login.php                  # Login page (currently unused)
├── register.php               # Register page (currently unused)
├── composer.json              # Composer configuration
├── composer.lock              # Composer dependency lock file
└── vendor/                    # PHP dependencies (Dompdf, etc.)
```

-----

## Installation and Setup

1.  **Clone the Repository:**

    ```bash
    git clone https://github.com/njhereee/flood-warning.git
    cd flood-warning
    ```

2.  **Web Server Setup (XAMPP/LAMP/MAMP/WAMP):**

      * Ensure your local server (Apache, MySQL) is running.
      * Place the `flood-warning` (or `projectdb` if you renamed it) folder inside your XAMPP's `htdocs` directory.

3.  **Database Configuration:**

      * Open phpMyAdmin (usually `http://localhost/phpmyadmin/`).
      * Create a new database named `bajir`.
      * Import the `bajir.sql` file into your `bajir` database.
      * **Important:** Ensure your `areas` table is populated with accurate Pekanbaru sub-district/village data, including coordinates.
      * Verify `connection.php` (`/projectdb/connection.php`) has the correct database credentials.

4.  **Install PHP Dependencies:**

      * Navigate to the project directory in your terminal:
        ```bash
        cd D:\xampp\htdocs\projectdb
        ```
      * Run Composer to install Dompdf and other dependencies:
        ```bash
        composer install
        ```

5.  **Configure Upload Folders:**

      * Create an `uploads` folder inside `projectdb`: `D:\xampp\htdocs\projectdb\uploads\`
      * Inside `uploads`, create `photos\` and `thumbnails\` folders.
      * Ensure these folders have appropriate **write permissions** for your web server.

6.  **Configure API Keys:**

      * **BMKG Weather API:** Open `api/bmkg_weather.php`. **CRITICAL:** Replace `$adm4_pekanbaru` with the accurate ADM4 code for the Pekanbaru sub-district/village you wish to monitor (e.g., `14.71.01.1002` from [kodewilayah.id](https://kodewilayah.id)).
      * **OpenWeather API:** You'll need to sign up for a free API key at [OpenWeatherMap](https://openweathermap.org/api). Integrate this key into your relevant PHP or JavaScript files where OpenWeather data is fetched. (You might want to create a separate config file for this key, e.g., `config.php`, and exclude it from Git using `.gitignore`).
      * Verify the API scripts work by accessing them in your browser (e.g., `http://localhost/projectdb/api/bmkg_weather.php`).

7.  **Access the Application:**

      * Open your web browser and visit: `http://localhost/projectdb/index.php`

-----

## Usage

  - **Dashboard** (`index.php`): View flood data summaries and water level trends.
  - **Alerts** (`alert.php`): Monitor active alerts, river levels, and weather forecasts.
  - **Reports** (`report.php`): Access comprehensive reports, historical data, damage assessments, and PDF download/photo upload options.
  - **Add New Alert** (`add_alert.php`): Input new flood warning data.
  - **Upload Photo** (`upload_photo.php`): Upload incident-related photos.

-----

## Contribution

Feel free to **fork** this repository and **submit pull requests** if you wish to contribute.

-----

## License

This project is licensed under the **MIT License**.

-----

## Screenshots

Below are some visual representations of the application's interface and features.

![Dashboard Overview](screenshot/dashboard.png)
*A quick look at the main dashboard, showing key statistics.*

![Alerts Page](screenshot/alerts_page.png)
*The alerts section, displaying active warnings and weather data.*

![Report Generation Example](screenshot/report_example.png)
*An example of the comprehensive flood report.*

---

