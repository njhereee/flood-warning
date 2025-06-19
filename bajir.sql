-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 04:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bajir`
--

-- --------------------------------------------------------

--
-- Table structure for table `flood_data`
--

CREATE TABLE `flood_data` (
  `id` int(11) NOT NULL,
  `region` varchar(100) DEFAULT NULL,
  `level` decimal(4,2) DEFAULT NULL,
  `status` enum('update','watch','warning') DEFAULT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flood_data`
--

INSERT INTO `flood_data` (`id`, `region`, `level`, `status`, `recorded_at`) VALUES
(1, 'North District', 3.80, 'warning', '2025-05-02 14:37:00'),
(2, 'Riverside', 3.50, 'watch', '2025-05-02 14:37:00'),
(3, 'South Valley', 3.20, 'update', '2025-05-02 14:37:00'),
(4, 'East Plains', 2.90, 'update', '2025-05-02 14:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'tata', '$2y$10$qr3YslpjlwW90.vD8IEZ4eVSk9f4zc/S4UV0SnVIhCg8CHuf8Gr1e', '2025-05-02 08:28:44'),
(2, 'user', '$2y$10$rsvkUNc9lJmE5TvxuF96LeGpt4eauEHsKqCsfILx2E0iEexw105fW', '2025-05-08 13:39:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flood_data`
--
ALTER TABLE `flood_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `flood_data`
--
ALTER TABLE `flood_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
