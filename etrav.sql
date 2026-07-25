-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 15, 2026 at 04:23 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etrav`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `package_id` bigint UNSIGNED NOT NULL,
  `pickup_datetime` datetime NOT NULL,
  `latitude` decimal(11,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `pax` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `admin_notify` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

-- INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `pickup_datetime`, `latitude`, `longitude`, `pax`, `total_price`, `deposit_amount`, `status`, `created_at`, `updated_at`, `notify`, `admin_notify`) VALUES
-- (2, 1, 7, '2026-07-22 05:00:00', 10.36022000, 123.74952800, 5, 6500.00, 1625.00, 'completed', '2026-07-14 00:26:45', '2026-07-14 19:10:16', 0, 1),
-- (3, 1, 8, '2026-07-22 05:26:00', 10.51945900, 124.02521800, 4, 2800.00, 700.00, 'completed', '2026-07-14 01:25:46', '2026-07-14 19:10:14', 0, 0),
-- (4, 1, 8, '2026-07-22 08:55:00', 10.32283600, 123.94938500, 5, 3000.00, 750.00, 'confirmed', '2026-07-14 03:54:38', '2026-07-14 19:10:10', 0, 0),
-- (5, 1, 10, '2026-07-17 00:25:00', 10.12501700, 123.53783300, 5, 10000.00, 2500.00, 'completed', '2026-07-14 19:24:30', '2026-07-14 19:25:27', 0, 0),
-- (6, 1, 9, '2026-07-16 12:26:00', 10.20500800, 123.64614700, 4, 1170.00, 292.50, 'confirmed', '2026-07-14 19:25:58', '2026-07-14 19:26:28', 0, 0),
-- (7, 1, 6, '2026-07-29 03:14:00', 10.54899100, 123.85442400, 4, 5800.00, 1450.00, 'confirmed', '2026-07-14 20:10:57', '2026-07-14 20:14:06', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `booking_places`
--

CREATE TABLE `booking_places` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `place_id` bigint UNSIGNED NOT NULL,
  `duration_minutes` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_places`
--

INSERT INTO `booking_places` (`id`, `booking_id`, `place_id`, `duration_minutes`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 120, '2026-07-14 00:26:45', '2026-07-14 00:26:45'),
(2, 2, 7, 180, '2026-07-14 00:26:45', '2026-07-14 00:26:45'),
(3, 3, 1, 120, '2026-07-14 01:25:46', '2026-07-14 01:25:46'),
(4, 3, 8, 120, '2026-07-14 01:25:46', '2026-07-14 01:25:46'),
(5, 3, 2, 120, '2026-07-14 01:25:46', '2026-07-14 01:25:46'),
(6, 4, 1, 120, '2026-07-14 03:54:38', '2026-07-14 03:54:38'),
(7, 4, 2, 180, '2026-07-14 03:54:38', '2026-07-14 03:54:38'),
(8, 4, 8, 120, '2026-07-14 03:54:38', '2026-07-14 03:54:38'),
(9, 5, 1, 120, '2026-07-14 19:24:31', '2026-07-14 19:24:31'),
(10, 5, 2, 120, '2026-07-14 19:24:31', '2026-07-14 19:24:31'),
(11, 5, 3, 120, '2026-07-14 19:24:31', '2026-07-14 19:24:31'),
(12, 6, 5, 120, '2026-07-14 19:25:58', '2026-07-14 19:25:58'),
(13, 6, 7, 180, '2026-07-14 19:25:58', '2026-07-14 19:25:58'),
(14, 6, 9, 120, '2026-07-14 19:25:58', '2026-07-14 19:25:58'),
(15, 7, 4, 120, '2026-07-14 20:10:57', '2026-07-14 20:10:57'),
(16, 7, 5, 120, '2026-07-14 20:10:57', '2026-07-14 20:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_13_020952_create_places_table', 1),
(5, '2026_07_14_132042_create_packages_table', 1),
(6, '2026_07_15_141901_create_package_places_table', 1),
(7, '2026_07_14_050445_add_is_admin_to_users_table', 2),
(8, '2026_07_14_074631_create_bookings_table', 3),
(9, '2026_07_14_080255_create_booking_places_table', 4),
(10, '2026_07_14_083345_add_phone_number_to_users_table', 5),
(11, '2026_07_14_093126_add_notify_to_bookings_table', 6),
(12, '2026_07_14_102449_add_admin_notify_to_bookings_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_price` int NOT NULL,
  `perhead_price` int NOT NULL,
  `pax` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `type`, `package_price`, `perhead_price`, `pax`, `image_path`, `description`, `created_at`, `updated_at`) VALUES
(6, 'Mactan to Oslob', 'popular', 5000, 200, 8, 'http://localhost:8000/storage/packages/JqtJo4LF2rJ7Y4evJwcLoS2QeLRnMvm4cz7jgadV.jpg', 'Enjoy from mactan to oslob', '2026-07-13 20:18:44', '2026-07-13 20:18:44'),
(7, 'Sea Tour', 'best_combo', 5000, 300, 5, 'http://localhost:8000/storage/packages/einGWd8POJjfb76LasIoStuCHU8MuRlPlIFCCpOG.png', 'Adventure for 7 hours', '2026-07-13 22:56:59', '2026-07-13 22:56:59'),
(8, 'Liloal and copper mine combo', 'best_combo', 2000, 200, 5, 'http://localhost:8000/storage/packages/MTxQ44tzLTT1Bdrk2Ens4BJV9MDb0uitov2EnVEV.jpg', 'Enjoy the 3 parts of cebu', '2026-07-14 01:24:37', '2026-07-14 01:24:37'),
(9, 'Carcar', 'trending', 234, 234, 12, 'http://localhost:8000/storage/packages/4YKexYiENJUiZx8i1wtc8IBgErijkXCAnvCKPPlo.jpg', 'Heosyam', '2026-07-14 03:32:50', '2026-07-14 03:32:50'),
(10, 'NATURE TOUR', 'trending', 5000, 1000, 3, 'http://localhost:8000/storage/packages/8367kESTGwd2hGf3yaSBFk6HXrfBhp1fwQ9vzKnT.jpg', 'Best foor tourrrr', '2026-07-14 19:19:03', '2026-07-14 19:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `package_places`
--

CREATE TABLE `package_places` (
  `id` bigint UNSIGNED NOT NULL,
  `package_id` bigint UNSIGNED NOT NULL,
  `place_id` bigint UNSIGNED NOT NULL,
  `position` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_places`
--

INSERT INTO `package_places` (`id`, `package_id`, `place_id`, `position`, `created_at`, `updated_at`) VALUES
(8, 6, 4, 1, '2026-07-13 20:18:44', '2026-07-13 20:18:44'),
(9, 6, 5, 2, '2026-07-13 20:18:44', '2026-07-13 20:18:44'),
(10, 7, 7, 1, '2026-07-13 22:56:59', '2026-07-13 22:56:59'),
(11, 7, 6, 2, '2026-07-13 22:56:59', '2026-07-13 22:56:59'),
(12, 8, 1, 1, '2026-07-14 01:24:37', '2026-07-14 01:24:37'),
(13, 8, 8, 2, '2026-07-14 01:24:37', '2026-07-14 01:24:37'),
(14, 8, 2, 3, '2026-07-14 01:24:37', '2026-07-14 01:24:37'),
(15, 9, 5, 1, '2026-07-14 03:32:50', '2026-07-14 19:13:05'),
(16, 9, 7, 2, '2026-07-14 03:32:50', '2026-07-14 19:13:05'),
(17, 9, 9, 3, '2026-07-14 03:32:50', '2026-07-14 19:13:05'),
(24, 10, 2, 1, '2026-07-14 19:19:03', '2026-07-14 19:19:03'),
(25, 10, 1, 2, '2026-07-14 19:19:03', '2026-07-14 19:19:03'),
(26, 10, 3, 3, '2026-07-14 19:19:03', '2026-07-14 19:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(11,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name`, `price`, `description`, `image_path`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'copper mine', 255, 'asdfwa', 'http://localhost:8000/storage/packages/places/QVIzdXfcoiurSj1HbDfI5rlUJOUao8qAIEpKOOfO.jpg', 10.32910548, 123.73696446, '2026-07-13 19:04:19', '2026-07-13 19:04:19'),
(2, 'Bairan', 25, 'sfwa', 'http://localhost:8000/storage/packages/places/x6oRnfYo0ExnYzWBDq696y1lKoHiyJnVs6XJPD88.jpg', 10.20039311, 123.71979296, '2026-07-13 19:04:41', '2026-07-13 19:04:41'),
(3, 'Lut od', 255, 'chuy gyud', 'http://localhost:8000/storage/packages/places/gz2ZqU0pEY6XV7n3jlqNjGTQY4jlbaLkz0qqzMDn.jpg', 10.27531278, 123.61387320, '2026-07-13 19:40:28', '2026-07-13 19:40:28'),
(4, 'Mactan Terminal', 0, 'Temrinal', 'http://localhost:8000/storage/packages/places/DlD2NMxo7p91EfarjSotpf0TpgKerX4PPlkwqRXz.png', 10.30892374, 123.97771016, '2026-07-13 20:15:20', '2026-07-13 20:15:20'),
(5, 'Oslob', 800, 'Whale Watching', 'http://localhost:8000/storage/packages/places/v3UaMkxuHyI5TNu8WQH94yGr1V6DeITXw9yeIXZw.jpg', 9.51999414, 123.43458312, '2026-07-13 20:16:49', '2026-07-13 20:16:49'),
(6, 'Bogo', 0, 'Bogo Place', 'http://localhost:8000/storage/packages/places/kC8a0gfKAzFWtat55sBPj8DHTs9zjjNvZw56xOrQ.jpg', 11.05038618, 124.00363103, '2026-07-13 22:55:19', '2026-07-13 22:55:19'),
(7, 'Moalboal', 0, 'Experience The Sea', 'http://localhost:8000/storage/packages/places/1vEaa6pwTxZyPyCopdpJvBeUcoLVy0QGlQixLfGt.jpg', 9.93714851, 123.39247294, '2026-07-13 22:56:06', '2026-07-13 22:56:06'),
(8, 'Liloan Sea', 200, 'Enjoy the foods', 'http://localhost:8000/storage/packages/places/6Y4Lim8QONaYHUHA3e53LI8GY6hRfqHILCfP12jf.jpg', 10.39981578, 124.00011130, '2026-07-14 01:23:12', '2026-07-14 01:23:12'),
(9, 'Carcar', 0, 'testing', 'http://localhost:8000/storage/packages/places/cQPh4a8WN9kv4iUORQWfdcd7jL6jwZ6ATiSbfSXo.png', 10.10154314, 123.64185601, '2026-07-14 03:31:13', '2026-07-14 03:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('FS8FJ8cczAFm6yJw4d3yWXTsEuUqe57S230Uem37', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ4TGZQVWRaQjZsWVpDeGNCOWFnYjdHWmhObjZTM2RCV1U2UUREUW40IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9ib29raW5ncyIsInJvdXRlIjoiYm9va2luZ3MudmlldyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1784088847),
('rplri2E8lEM19X0QZH8HS2LoIrmQU9ae7bWZb1wL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'eyJfdG9rZW4iOiI1T0d5MGZjd3IzTG5XY0NRckxyVnpiSkZGcEluNFFoU2tRUWhOcnh1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1784084621);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_admin`, `phone_number`) VALUES
(1, 'John Doe', 'johndoe@gmail.com', NULL, '$2y$12$728/VTmcYD9cKQvUS.NqIekJwwACqoEiAQO3vPWT8AvNKlLrB8TGi', NULL, '2026-07-13 21:05:41', '2026-07-13 21:05:41', 1, ''),
(2, 'Joy Boy', 'joyboy@gmail.com', NULL, '$2y$12$Z3FC.4JUlxG1boXCzLpOQODaziWSW.QrC96R.oF.MOxRsIg9Bwcwm', NULL, '2026-07-14 00:45:32', '2026-07-14 00:45:32', 0, '608.383.0713');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_package_id_foreign` (`package_id`);

--
-- Indexes for table `booking_places`
--
ALTER TABLE `booking_places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_places_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_places_place_id_foreign` (`place_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_places`
--
ALTER TABLE `package_places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_places_package_id_foreign` (`package_id`),
  ADD KEY `package_places_place_id_foreign` (`place_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_places`
--
ALTER TABLE `booking_places`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `package_places`
--
ALTER TABLE `package_places`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_places`
--
ALTER TABLE `booking_places`
  ADD CONSTRAINT `booking_places_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_places_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_places`
--
ALTER TABLE `package_places`
  ADD CONSTRAINT `package_places_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_places_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
