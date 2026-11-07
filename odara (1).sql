-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2026 at 03:54 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `odara`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('odara_cache_5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1794065963),
('odara_cache_5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1794065963;', 1794065963);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `charger_items`
--

CREATE TABLE `charger_items` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charger_items`
--

INSERT INTO `charger_items` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Laser', NULL, 1, '2026-11-05 22:36:39', '2026-11-05 22:36:39'),
(4, 'Sound', NULL, 1, '2026-11-05 22:58:05', '2026-11-05 22:58:05'),
(5, 'Powerbank', NULL, 1, '2026-11-05 22:58:14', '2026-11-05 22:59:53'),
(6, 'MP3', NULL, 1, '2026-11-05 23:00:12', '2026-11-05 23:00:12'),
(7, 'MP4', NULL, 1, '2026-11-05 23:00:20', '2026-11-05 23:00:20'),
(9, 'Senter', NULL, 1, '2026-11-07 05:22:22', '2026-11-07 05:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `charger_transactions`
--

CREATE TABLE `charger_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `room_id` bigint UNSIGNED DEFAULT NULL,
  `charger_item_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `pin_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int UNSIGNED NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_picked_up` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charger_transactions`
--

INSERT INTO `charger_transactions` (`id`, `user_id`, `room_id`, `charger_item_id`, `transaction_date`, `pin_number`, `customer_name`, `item_type`, `qty`, `notes`, `is_picked_up`, `created_at`, `updated_at`) VALUES
(9, 2, 4, 9, '2026-11-07', '3', 'Najib', 'Senter', 1, NULL, 1, '2026-11-07 05:23:04', '2026-11-07 08:38:46'),
(10, 2, 2, 4, '2026-11-07', '4', 'Farel', 'Sound', 1, NULL, 1, '2026-11-07 05:23:32', '2026-11-07 08:38:49'),
(11, 2, 4, 5, '2026-11-07', '17', 'Fadi', 'Powerbank', 1, NULL, 1, '2026-11-07 05:39:59', '2026-11-07 08:38:52'),
(12, 2, 2, 4, '2026-11-07', '5', 'Farel', 'Sound', 1, NULL, 0, '2026-11-07 05:40:29', '2026-11-07 05:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `attempts` tinyint UNSIGNED NOT NULL,
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
(4, '2026_11_05_000001_add_role_and_status_to_users_table', 1),
(5, '2026_11_05_000002_create_rooms_table', 1),
(6, '2026_11_05_000003_create_shopee_transactions_table', 1),
(7, '2026_11_05_000004_create_charger_transactions_table', 1),
(8, '2026_11_05_000005_create_charger_items_table', 1),
(9, '2026_11_05_000006_add_is_picked_up_to_transactions', 1),
(10, '2026_11_05_000007_add_charger_item_to_charger_transactions', 1);

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
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'TAHFIDZ', NULL, 1, '2026-11-05 22:36:26', '2026-11-05 22:36:26'),
(2, 'AMZA', NULL, 1, '2026-11-05 22:40:05', '2026-11-05 22:40:05'),
(3, 'XL', NULL, 1, '2026-11-05 22:40:11', '2026-11-05 22:40:11'),
(4, 'TAHSIN', NULL, 1, '2026-11-05 22:40:20', '2026-11-05 22:40:20'),
(8, 'PSB', NULL, 1, '2026-11-06 16:33:39', '2026-11-06 16:33:39');

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
('81TlAqH98Bo6owZVPPjPI2u0R9dHk2GFUFCOlDYk', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36 Edg/157.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS0JVTTEzenRiWkJoTW5ya1doQVVLZmtyS1ZONEt6aFNycXZKeUducCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGFyZ2VyIjtzOjU6InJvdXRlIjtzOjEzOiJjaGFyZ2VyLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNzRiM2I0YTcxNTlmZDI0NzA5MWRiZjU3NDViMzQ0MTlkNzMzNWE3ZmU3ZjBiMGE2OWY0YmExOWQzNjA0ZTY1ZSI7fQ==', 1794055230),
('s40Bx6Ssrei3nH6zaSNDttUN6ApQuC5DMU8j5omS', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZTlqWlp2VFdYTHZ5THJFUlpkNW1uUlB0UEtoeVgyNGNEcWl3MVA3RSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zaG9wZWUiO3M6NToicm91dGUiO3M6MTI6InNob3BlZS5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6Ijc0YjNiNGE3MTU5ZmQyNDcwOTFkYmY1NzQ1YjM0NDE5ZDczMzVhN2ZlN2YwYjBhNjlmNGJhMTlkMzYwNGU2NWUiO30=', 1794065938),
('T2lM4SrZPa9ZKAS81VAzI2KdMUd2kGkSz4MXYS2Q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36 Edg/157.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHhWRFpTR0doRXdDcGxEWWdwUFJjVFc4QVI0ck9FYlRQOEhQdzhpVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794065703);

-- --------------------------------------------------------

--
-- Table structure for table `shopee_transactions`
--

CREATE TABLE `shopee_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `room_id` bigint UNSIGNED DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `receipt_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `service_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_picked_up` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'petugas',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@odara.com', NULL, '$2y$12$AXwbAW2uYTEXzLUhhG4bH.mUUSNYZ3Sb2UvWTxVJtIQrBoFc9pH2S', 'admin', 1, 'maw3ZRjPTpwPbGV7MKX9kVUrH9RRLAmYGPt5Y7uoyN3bSjCYZRACG7gFckwE', '2026-11-05 22:36:07', '2026-11-05 22:36:07'),
(2, 'petugas', 'petugas@odara.com', NULL, '$2y$12$9rLkCiruA2VmHX/cl4hvb.KFFJGOK69YTt3RU1n0LjqbODZe3kKw6', 'petugas', 1, '9wQpVWP8yIIzLgqsfuvwfysFJev4t5JDXQX6KcHBBoLmG8PZrR52dcOkl3L0', '2026-11-05 22:37:14', '2026-11-05 22:37:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `charger_items`
--
ALTER TABLE `charger_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `charger_items_name_unique` (`name`),
  ADD KEY `charger_items_is_active_index` (`is_active`);

--
-- Indexes for table `charger_transactions`
--
ALTER TABLE `charger_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charger_transactions_user_id_foreign` (`user_id`),
  ADD KEY `charger_transactions_room_id_foreign` (`room_id`),
  ADD KEY `charger_transactions_transaction_date_index` (`transaction_date`),
  ADD KEY `charger_transactions_customer_name_index` (`customer_name`),
  ADD KEY `charger_transactions_is_picked_up_index` (`is_picked_up`),
  ADD KEY `charger_transactions_charger_item_id_foreign` (`charger_item_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_name_unique` (`name`),
  ADD KEY `rooms_is_active_index` (`is_active`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shopee_transactions`
--
ALTER TABLE `shopee_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shopee_transactions_user_id_foreign` (`user_id`),
  ADD KEY `shopee_transactions_room_id_foreign` (`room_id`),
  ADD KEY `shopee_transactions_transaction_date_index` (`transaction_date`),
  ADD KEY `shopee_transactions_customer_name_index` (`customer_name`),
  ADD KEY `shopee_transactions_is_picked_up_index` (`is_picked_up`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_is_active_index` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `charger_items`
--
ALTER TABLE `charger_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `charger_transactions`
--
ALTER TABLE `charger_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shopee_transactions`
--
ALTER TABLE `shopee_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `charger_transactions`
--
ALTER TABLE `charger_transactions`
  ADD CONSTRAINT `charger_transactions_charger_item_id_foreign` FOREIGN KEY (`charger_item_id`) REFERENCES `charger_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `charger_transactions_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `charger_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `shopee_transactions`
--
ALTER TABLE `shopee_transactions`
  ADD CONSTRAINT `shopee_transactions_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shopee_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
