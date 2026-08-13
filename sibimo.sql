-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 13, 2026 at 08:58 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sibimo`
--

-- --------------------------------------------------------

--
-- Table structure for table `agendas`
--

CREATE TABLE `agendas` (
  `agenda_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `category_id` bigint UNSIGNED NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_loans`
--

CREATE TABLE `book_loans` (
  `loan_id` bigint UNSIGNED NOT NULL,
  `book_id` bigint UNSIGNED NOT NULL,
  `citizen_id` bigint UNSIGNED NOT NULL,
  `borrowed_at` date NOT NULL,
  `due_date` date NOT NULL,
  `returned_at` date DEFAULT NULL,
  `status` enum('Borrowed','Returned','Late') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Borrowed',
  `fine_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `citizens`
--

CREATE TABLE `citizens` (
  `citizen_id` bigint UNSIGNED NOT NULL,
  `national_id` char(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_place` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` bigint UNSIGNED NOT NULL,
  `citizen_id` bigint UNSIGNED NOT NULL,
  `category` enum('Infrastructure','Public Service','Environment','Security','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Submitted','In Progress','Resolved','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_attachments`
--

CREATE TABLE `complaint_attachments` (
  `attachment_id` bigint UNSIGNED NOT NULL,
  `complaint_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_status_histories`
--

CREATE TABLE `complaint_status_histories` (
  `history_id` bigint UNSIGNED NOT NULL,
  `complaint_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NULL DEFAULT NULL
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
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Unread','Read','Replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unread',
  `submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `gallery_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL
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
-- Table structure for table `letter_number_sequences`
--

CREATE TABLE `letter_number_sequences` (
  `sequence_id` bigint UNSIGNED NOT NULL,
  `letter_type_id` bigint UNSIGNED NOT NULL,
  `year` int NOT NULL,
  `last_sequence` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_requests`
--

CREATE TABLE `letter_requests` (
  `letter_request_id` bigint UNSIGNED NOT NULL,
  `citizen_id` bigint UNSIGNED NOT NULL,
  `letter_type_id` bigint UNSIGNED NOT NULL,
  `status` enum('submitted','verified','authorized','completed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `form_data` json DEFAULT NULL,
  `letter_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_type` enum('manual','digital') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `authorized_by` bigint UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `authorized_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `result_file_path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_request_attachments`
--

CREATE TABLE `letter_request_attachments` (
  `attachment_id` bigint UNSIGNED NOT NULL,
  `letter_request_id` bigint UNSIGNED NOT NULL,
  `letter_type_document_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_request_status_histories`
--

CREATE TABLE `letter_request_status_histories` (
  `history_id` bigint UNSIGNED NOT NULL,
  `letter_request_id` bigint UNSIGNED NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `change_by` bigint UNSIGNED NOT NULL,
  `changed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_types`
--

CREATE TABLE `letter_types` (
  `letter_type_id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `letter_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `blade_view` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_prefix` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_type_documents`
--

CREATE TABLE `letter_type_documents` (
  `letter_type_document_id` bigint UNSIGNED NOT NULL,
  `letter_type_id` bigint UNSIGNED NOT NULL,
  `document_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL
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
(4, '2026_08_11_144615_create_citizens_table', 1),
(5, '2026_08_11_144750_create_letter_types_table', 1),
(6, '2026_08_11_144857_create_profile_sections_table', 1),
(7, '2026_08_11_144934_create_service_table', 1),
(8, '2026_08_11_145057_create_village_potentials_table', 1),
(9, '2026_08_11_145229_create_news_categories_table', 1),
(10, '2026_08_11_145251_create_book_categories_table', 1),
(11, '2026_08_11_145319_create_books_table', 1),
(12, '2026_08_11_145500_create_feedbacks_table', 1),
(13, '2026_08_11_145524_create_letter_type_documents_table', 1),
(14, '2026_08_11_145550_create_letter_number_sequences_table', 1),
(15, '2026_08_11_145609_create_profile_contents_table', 1),
(16, '2026_08_11_145749_create_galleries_table', 1),
(17, '2026_08_11_145811_create_agendas_table', 1),
(18, '2026_08_11_145829_create_news_table', 1),
(19, '2026_08_11_145851_create_book_loans_table', 1),
(20, '2026_08_11_145920_create_letter_requests_table', 1),
(21, '2026_08_11_145937_create_complaints_table', 1),
(22, '2026_08_11_145958_create_letter_request_attachments_table', 1),
(23, '2026_08_11_150019_create_letter_request_status_histories_table', 1),
(24, '2026_08_11_150045_create_complaint_attachments_table', 1),
(25, '2026_08_11_150106_create_complaint_status_histories_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `author_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Published','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_categories`
--

CREATE TABLE `news_categories` (
  `category_id` bigint UNSIGNED NOT NULL,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_contents`
--

CREATE TABLE `profile_contents` (
  `profile_content_id` bigint UNSIGNED NOT NULL,
  `section_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('Draft','Published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_sections`
--

CREATE TABLE `profile_sections` (
  `section_id` bigint UNSIGNED NOT NULL,
  `section_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `service_id` bigint UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Operator') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `village_potentials`
--

CREATE TABLE `village_potentials` (
  `potential_id` bigint UNSIGNED NOT NULL,
  `category` enum('UMKM','Agriculture','Tourism','BUMDes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agendas`
--
ALTER TABLE `agendas`
  ADD PRIMARY KEY (`agenda_id`),
  ADD KEY `agendas_created_by_foreign` (`created_by`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `books_category_id_foreign` (`category_id`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `book_loans`
--
ALTER TABLE `book_loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `book_loans_book_id_foreign` (`book_id`),
  ADD KEY `book_loans_citizen_id_foreign` (`citizen_id`);

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
-- Indexes for table `citizens`
--
ALTER TABLE `citizens`
  ADD PRIMARY KEY (`citizen_id`),
  ADD UNIQUE KEY `citizens_national_id_unique` (`national_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `complaints_citizen_id_foreign` (`citizen_id`);

--
-- Indexes for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `complaint_attachments_complaint_id_foreign` (`complaint_id`);

--
-- Indexes for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `complaint_status_histories_complaint_id_foreign` (`complaint_id`),
  ADD KEY `complaint_status_histories_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `galleries_uploaded_by_foreign` (`uploaded_by`);

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
-- Indexes for table `letter_number_sequences`
--
ALTER TABLE `letter_number_sequences`
  ADD PRIMARY KEY (`sequence_id`),
  ADD KEY `letter_number_sequences_letter_type_id_foreign` (`letter_type_id`);

--
-- Indexes for table `letter_requests`
--
ALTER TABLE `letter_requests`
  ADD PRIMARY KEY (`letter_request_id`),
  ADD KEY `letter_requests_citizen_id_foreign` (`citizen_id`),
  ADD KEY `letter_requests_letter_type_id_foreign` (`letter_type_id`),
  ADD KEY `letter_requests_verified_by_foreign` (`verified_by`),
  ADD KEY `letter_requests_authorized_by_foreign` (`authorized_by`);

--
-- Indexes for table `letter_request_attachments`
--
ALTER TABLE `letter_request_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `letter_request_attachments_letter_request_id_foreign` (`letter_request_id`),
  ADD KEY `letter_request_attachments_letter_type_document_id_foreign` (`letter_type_document_id`);

--
-- Indexes for table `letter_request_status_histories`
--
ALTER TABLE `letter_request_status_histories`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `letter_request_status_histories_letter_request_id_foreign` (`letter_request_id`),
  ADD KEY `letter_request_status_histories_change_by_foreign` (`change_by`);

--
-- Indexes for table `letter_types`
--
ALTER TABLE `letter_types`
  ADD PRIMARY KEY (`letter_type_id`),
  ADD UNIQUE KEY `letter_types_code_unique` (`code`);

--
-- Indexes for table `letter_type_documents`
--
ALTER TABLE `letter_type_documents`
  ADD PRIMARY KEY (`letter_type_document_id`),
  ADD KEY `letter_type_documents_letter_type_id_foreign` (`letter_type_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`),
  ADD KEY `news_category_id_foreign` (`category_id`),
  ADD KEY `news_author_id_foreign` (`author_id`);

--
-- Indexes for table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `news_categories_slug_unique` (`slug`);

--
-- Indexes for table `profile_contents`
--
ALTER TABLE `profile_contents`
  ADD PRIMARY KEY (`profile_content_id`),
  ADD KEY `profile_contents_section_id_foreign` (`section_id`),
  ADD KEY `profile_contents_published_by_foreign` (`published_by`);

--
-- Indexes for table `profile_sections`
--
ALTER TABLE `profile_sections`
  ADD PRIMARY KEY (`section_id`),
  ADD UNIQUE KEY `profile_sections_slug_unique` (`slug`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `village_potentials`
--
ALTER TABLE `village_potentials`
  ADD PRIMARY KEY (`potential_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agendas`
--
ALTER TABLE `agendas`
  MODIFY `agenda_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_loans`
--
ALTER TABLE `book_loans`
  MODIFY `loan_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `citizens`
--
ALTER TABLE `citizens`
  MODIFY `citizen_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  MODIFY `attachment_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  MODIFY `history_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `gallery_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_number_sequences`
--
ALTER TABLE `letter_number_sequences`
  MODIFY `sequence_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_requests`
--
ALTER TABLE `letter_requests`
  MODIFY `letter_request_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_request_attachments`
--
ALTER TABLE `letter_request_attachments`
  MODIFY `attachment_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_request_status_histories`
--
ALTER TABLE `letter_request_status_histories`
  MODIFY `history_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_types`
--
ALTER TABLE `letter_types`
  MODIFY `letter_type_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_type_documents`
--
ALTER TABLE `letter_type_documents`
  MODIFY `letter_type_document_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_contents`
--
ALTER TABLE `profile_contents`
  MODIFY `profile_content_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_sections`
--
ALTER TABLE `profile_sections`
  MODIFY `section_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `service_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `village_potentials`
--
ALTER TABLE `village_potentials`
  MODIFY `potential_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agendas`
--
ALTER TABLE `agendas`
  ADD CONSTRAINT `agendas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `book_categories` (`category_id`);

--
-- Constraints for table `book_loans`
--
ALTER TABLE `book_loans`
  ADD CONSTRAINT `book_loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `book_loans_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`citizen_id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`citizen_id`);

--
-- Constraints for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD CONSTRAINT `complaint_attachments_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`);

--
-- Constraints for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  ADD CONSTRAINT `complaint_status_histories_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`),
  ADD CONSTRAINT `complaint_status_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `letter_number_sequences`
--
ALTER TABLE `letter_number_sequences`
  ADD CONSTRAINT `letter_number_sequences_letter_type_id_foreign` FOREIGN KEY (`letter_type_id`) REFERENCES `letter_types` (`letter_type_id`);

--
-- Constraints for table `letter_requests`
--
ALTER TABLE `letter_requests`
  ADD CONSTRAINT `letter_requests_authorized_by_foreign` FOREIGN KEY (`authorized_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `letter_requests_citizen_id_foreign` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`citizen_id`),
  ADD CONSTRAINT `letter_requests_letter_type_id_foreign` FOREIGN KEY (`letter_type_id`) REFERENCES `letter_types` (`letter_type_id`),
  ADD CONSTRAINT `letter_requests_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `letter_request_attachments`
--
ALTER TABLE `letter_request_attachments`
  ADD CONSTRAINT `letter_request_attachments_letter_request_id_foreign` FOREIGN KEY (`letter_request_id`) REFERENCES `letter_requests` (`letter_request_id`),
  ADD CONSTRAINT `letter_request_attachments_letter_type_document_id_foreign` FOREIGN KEY (`letter_type_document_id`) REFERENCES `letter_type_documents` (`letter_type_document_id`);

--
-- Constraints for table `letter_request_status_histories`
--
ALTER TABLE `letter_request_status_histories`
  ADD CONSTRAINT `letter_request_status_histories_change_by_foreign` FOREIGN KEY (`change_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `letter_request_status_histories_letter_request_id_foreign` FOREIGN KEY (`letter_request_id`) REFERENCES `letter_requests` (`letter_request_id`);

--
-- Constraints for table `letter_type_documents`
--
ALTER TABLE `letter_type_documents`
  ADD CONSTRAINT `letter_type_documents_letter_type_id_foreign` FOREIGN KEY (`letter_type_id`) REFERENCES `letter_types` (`letter_type_id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `news_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`category_id`);

--
-- Constraints for table `profile_contents`
--
ALTER TABLE `profile_contents`
  ADD CONSTRAINT `profile_contents_published_by_foreign` FOREIGN KEY (`published_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `profile_contents_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `profile_sections` (`section_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
