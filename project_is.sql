-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 05:51 PM
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
-- Database: `project_is`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-boost.roster.scan', 'a:2:{s:6:\"roster\";O:21:\"Laravel\\Roster\\Roster\":3:{s:13:\"\0*\0approaches\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:8:{i:0;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^12.0\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:LARAVEL\";s:14:\"\0*\0packageName\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"12.43.1\";s:6:\"\0*\0dev\";b:0;}i:1;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.3.8\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PROMPTS\";s:14:\"\0*\0packageName\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:5:\"0.3.8\";s:6:\"\0*\0dev\";b:0;}i:2;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^2.3\";s:10:\"\0*\0package\";E:36:\"Laravel\\Roster\\Enums\\Packages:BREEZE\";s:14:\"\0*\0packageName\";s:14:\"laravel/breeze\";s:10:\"\0*\0version\";s:5:\"2.3.8\";s:6:\"\0*\0dev\";b:1;}i:3;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.5.1\";s:10:\"\0*\0package\";E:33:\"Laravel\\Roster\\Enums\\Packages:MCP\";s:14:\"\0*\0packageName\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.5.1\";s:6:\"\0*\0dev\";b:1;}i:4;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.24\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PINT\";s:14:\"\0*\0packageName\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.26.0\";s:6:\"\0*\0dev\";b:1;}i:5;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.41\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:SAIL\";s:14:\"\0*\0packageName\";s:12:\"laravel/sail\";s:10:\"\0*\0version\";s:6:\"1.51.0\";s:6:\"\0*\0dev\";b:1;}i:6;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PEST\";s:14:\"\0*\0packageName\";s:12:\"pestphp/pest\";s:10:\"\0*\0version\";s:5:\"3.8.4\";s:6:\"\0*\0dev\";b:1;}i:7;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:7:\"11.5.33\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PHPUNIT\";s:14:\"\0*\0packageName\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"11.5.33\";s:6:\"\0*\0dev\";b:1;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:21:\"\0*\0nodePackageManager\";E:43:\"Laravel\\Roster\\Enums\\NodePackageManager:NPM\";}s:9:\"timestamp\";i:1766419443;}', 1766505843);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_archives`
--

CREATE TABLE `data_archives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `privacy_level` enum('Public Domain','Restricted','Highly Confidential') DEFAULT 'Restricted',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size_kb` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'uploaded',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_archive_settings`
--

CREATE TABLE `data_archive_settings` (
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `policy_compliance` varchar(255) DEFAULT NULL,
  `total_size_bytes` bigint(20) UNSIGNED DEFAULT 0,
  `is_finalized` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `final_reports`
--

CREATE TABLE `final_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `submission_date` date NOT NULL,
  `executive_summary` text DEFAULT NULL,
  `utilization_impact` text DEFAULT NULL,
  `curriculum_suggestions` text DEFAULT NULL,
  `faculty_suggestions` text DEFAULT NULL,
  `file_report_pdf` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','approved') DEFAULT 'draft',
  `checklist_report_sent` tinyint(1) DEFAULT 0,
  `checklist_budget_cleared` tinyint(1) DEFAULT 0,
  `checklist_outputs_registered` tinyint(1) DEFAULT 0,
  `checklist_project_closed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funders`
--

CREATE TABLE `funders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Internal','External') NOT NULL DEFAULT 'External',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `funders`
--

INSERT INTO `funders` (`id`, `name`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ทุนอุดหนุนการวิจัยจากคณะ (Faculty Research Grant)', 'Internal', 'Active', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(2, 'สำนักงานการวิจัยแห่งชาติ (วช.)', 'External', 'Active', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(3, 'ทุน สกสว. (TSRI)', 'External', 'Active', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(4, 'ทุนมหาวิทยาลัยเพื่อทำนุบำรุงศิลปวัฒนธรรม', 'Internal', 'Inactive', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(5, 'ทุนอุดหนุนการวิจัยจากคณะ (Faculty Research Grant)', 'Internal', 'Active', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(6, 'สำนักงานการวิจัยแห่งชาติ (วช.)', 'External', 'Active', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(7, 'ทุน สกสว. (TSRI)', 'External', 'Active', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(8, 'ทุนมหาวิทยาลัยเพื่อทำนุบำรุงศิลปวัฒนธรรม', 'Internal', 'Inactive', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(9, 'ทุนอุดหนุนการวิจัยจากคณะ (Faculty Research Grant)', 'Internal', 'Active', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(10, 'สำนักงานการวิจัยแห่งชาติ (วช.)', 'External', 'Active', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(11, 'ทุน สกสว. (TSRI)', 'External', 'Active', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(12, 'ทุนมหาวิทยาลัยเพื่อทำนุบำรุงศิลปวัฒนธรรม', 'Internal', 'Inactive', '2026-05-26 07:31:35', '2026-05-26 07:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `funding_sources`
--

CREATE TABLE `funding_sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_budget_limit` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `funding_sources`
--

INSERT INTO `funding_sources` (`id`, `name`, `description`, `total_budget_limit`, `created_at`, `updated_at`) VALUES
(4, 'งบประมาณภายใน', NULL, NULL, '2026-03-09 14:50:46', '2026-03-09 14:50:46'),
(5, 'งบประมาณภายนอก', NULL, NULL, '2026-03-09 14:50:46', '2026-03-09 14:50:46'),
(6, 'งบประมาณส่วนตัว', NULL, NULL, '2026-03-09 14:50:46', '2026-03-09 14:50:46');

-- --------------------------------------------------------

--
-- Table structure for table `ip_assets`
--

CREATE TABLE `ip_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_creations`
--

CREATE TABLE `ip_creations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `researcher_id` bigint(20) UNSIGNED NOT NULL,
  `name_th` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `abstract_details` text DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `ip_type` enum('Patent','Copyright','Creative') NOT NULL,
  `legal_status` varchar(100) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `registration_agency` varchar(255) DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `commercial_status` varchar(100) DEFAULT NULL,
  `economic_value` decimal(15,2) DEFAULT 0.00,
  `impact_description` text DEFAULT NULL,
  `file_submission` varchar(255) DEFAULT NULL,
  `file_certificate` varchar(255) DEFAULT NULL,
  `file_evidence` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_inventors`
--

CREATE TABLE `ip_inventors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip_creation_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `proportion_percent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `issn` varchar(100) DEFAULT NULL,
  `quartile` varchar(50) DEFAULT NULL,
  `database_index` enum('Scopus','WoS','TCI','Scopus Proceeding','Other') NOT NULL DEFAULT 'Other',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`id`, `name`, `issn`, `quartile`, `database_index`, `created_at`, `updated_at`) VALUES
(1, 'Journal of Digital Marketing and FinTech', '2450-123X', 'Q1', 'Scopus', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(2, 'วารสารบริหารธุรกิจ มทร.ธัญบุรี', '2530-9980', 'TCI Q1', 'TCI', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(3, 'IEEE International Conference', '978-1-6654-7389-7', 'N/A', 'Scopus Proceeding', '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(4, 'Journal of Digital Marketing and FinTech', '2450-123X', 'Q1', 'Scopus', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(5, 'วารสารบริหารธุรกิจ มทร.ธัญบุรี', '2530-9980', 'TCI Q1', 'TCI', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(6, 'IEEE International Conference', '978-1-6654-7389-7', 'N/A', 'Scopus Proceeding', '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(7, 'Journal of Digital Marketing and FinTech', '2450-123X', 'Q1', 'Scopus', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(8, 'วารสารบริหารธุรกิจ มทร.ธัญบุรี', '2530-9980', 'TCI Q1', 'TCI', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(9, 'IEEE International Conference', '978-1-6654-7389-7', 'N/A', 'Scopus Proceeding', '2026-05-26 07:31:35', '2026-05-26 07:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `kpi_metrics`
--

CREATE TABLE `kpi_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metric_snapshots`
--

CREATE TABLE `metric_snapshots` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `fiscal_year` int(11) NOT NULL,
  `h_index` int(11) DEFAULT 0,
  `total_citations` int(11) DEFAULT 0,
  `external_grants_value` decimal(15,2) DEFAULT 0.00,
  `q1_q2_publications` int(11) DEFAULT 0,
  `total_publications` int(11) DEFAULT 0,
  `ip_count` int(11) DEFAULT 0,
  `last_calculated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metric_tiers`
--

CREATE TABLE `metric_tiers` (
  `id` int(11) NOT NULL,
  `category` enum('Journal','IP') NOT NULL,
  `level_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `points` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `metric_tiers`
--

INSERT INTO `metric_tiers` (`id`, `category`, `level_name`, `description`, `points`, `created_at`, `updated_at`) VALUES
(1, 'Journal', 'Q1 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q1 หรือ เทียบเท่า', 2.00, '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(2, 'Journal', 'Q2 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q2', 1.50, '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(3, 'Journal', 'TCI Q1 / ระดับชาติ', 'วารสาร TCI กลุ่ม 1', 1.00, '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(4, 'IP', 'อนุสิทธิบัตร (Petty Patent)', '', 1.50, '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(5, 'IP', 'สิทธิบัตร (Patent)', '', 3.00, '2026-03-02 18:06:54', '2026-03-02 18:06:54'),
(6, 'Journal', 'Q1 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q1 หรือ เทียบเท่า', 2.00, '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(7, 'Journal', 'Q2 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q2', 1.50, '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(8, 'Journal', 'TCI Q1 / ระดับชาติ', 'วารสาร TCI กลุ่ม 1', 1.00, '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(9, 'IP', 'อนุสิทธิบัตร (Petty Patent)', '', 1.50, '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(10, 'IP', 'สิทธิบัตร (Patent)', '', 3.00, '2026-03-02 18:07:24', '2026-03-02 18:07:24'),
(11, 'Journal', 'Q1 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q1 หรือ เทียบเท่า', 2.00, '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(12, 'Journal', 'Q2 / ระดับนานาชาติ', 'วารสาร Scopus/WoS Q2', 1.50, '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(13, 'Journal', 'TCI Q1 / ระดับชาติ', 'วารสาร TCI กลุ่ม 1', 1.00, '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(14, 'IP', 'อนุสิทธิบัตร (Petty Patent)', '', 1.50, '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(15, 'IP', 'สิทธิบัตร (Patent)', '', 3.00, '2026-05-26 07:31:35', '2026-05-26 07:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_22_154905_create_proposal_tables', 1),
(5, '2025_12_22_154908_create_project_tables', 1),
(6, '2025_12_22_154909_create_output_tables', 1),
(7, '2025_12_22_154910_create_kpi_tables', 1),
(8, '2025_12_22_155209_create_permission_tables', 1),
(9, '2026_02_10_235342_enhance_proposals_table', 2),
(10, '2026_02_10_235347_create_proposal_teams_table', 2),
(11, '2026_02_11_000347_add_budget_details_to_proposals_table', 3),
(12, '2026_02_11_000936_create_project_budget_tables', 4),
(13, '2026_02_11_004208_create_project_reviews_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(9, 'App\\Models\\User', 12),
(9, 'App\\Models\\User', 13),
(12, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_reports`
--

CREATE TABLE `progress_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `report_period` varchar(255) NOT NULL,
  `percentage_complete` int(11) NOT NULL DEFAULT 0,
  `planned_progress_percentage` int(11) DEFAULT 0,
  `budget_spending_status` varchar(255) DEFAULT 'ตามแผน',
  `risk_level` enum('Low','Medium','High') DEFAULT 'Low',
  `summary_text` text DEFAULT NULL,
  `status` enum('submitted','verified') NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `problems_obstacles` text DEFAULT NULL,
  `next_milestone_plan` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('ongoing','completed','closed','terminated') NOT NULL DEFAULT 'ongoing',
  `budget_used` decimal(15,2) DEFAULT 0.00,
  `closure_requested` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_budget_allocations`
--

CREATE TABLE `project_budget_allocations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year` varchar(4) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `funding_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_files`
--

CREATE TABLE `project_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_fundings`
--

CREATE TABLE `project_fundings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'internal',
  `status` varchar(255) NOT NULL DEFAULT 'received',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_milestones`
--

CREATE TABLE `project_milestones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `milestone_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_milestones`
--

INSERT INTO `project_milestones` (`id`, `project_id`, `milestone_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'เดือน 1 (1 - 30 ก.ค.)', '1. ทบทวนวรรณกรรมและจัดทำโครงร่างการวิจัย', 'completed', '2026-02-25 13:28:52', '2026-02-25 13:28:52'),
(2, 1, 'เดือน 2 (1 - 31 ส.ค.)', '2. เก็บข้อมูลภาคสนามและวิเคราะห์ข้อมูลเบื้องต้น', 'in_progress', '2026-02-25 13:28:52', '2026-02-25 13:28:52'),
(3, 1, 'เดือน 3 (1 - 30 ก.ย.)', '3. เขียนรายงานฉบับร่างและจัดทำข้อเสนอแนะเชิงนโยบาย', 'pending', '2026-02-25 13:28:52', '2026-02-25 13:28:52'),
(4, 1, 'เดือน 4 (1 - 31 ต.ค.)', '4. จัดทำรายงานฉบับสมบูรณ์และนำเสนอผลงาน', 'pending', '2026-02-25 13:28:52', '2026-02-25 13:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `project_reviews`
--

CREATE TABLE `project_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `score_concept` int(11) NOT NULL,
  `score_team` int(11) NOT NULL,
  `score_alignment` int(11) NOT NULL,
  `score_impact` int(11) NOT NULL,
  `total_score` int(11) NOT NULL,
  `comments_strengths` text DEFAULT NULL,
  `comments_suggestions` text DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_reviews`
--

INSERT INTO `project_reviews` (`id`, `proposal_id`, `reviewer_id`, `score_concept`, `score_team`, `score_alignment`, `score_impact`, `total_score`, `comments_strengths`, `comments_suggestions`, `status`, `created_at`, `updated_at`) VALUES
(13, 11, 1, 10, 10, 10, 10, 40, '', '', 'under_review', '2026-05-26 09:17:51', '2026-05-26 09:17:51');

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE `proposals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `research_type` varchar(255) DEFAULT NULL,
  `research_group` varchar(255) DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `pi_proportion` int(11) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `budget_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `budget_details` text DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected') NOT NULL DEFAULT 'draft',
  `funding_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `funding_source_internal` varchar(255) DEFAULT NULL,
  `funding_source_external` varchar(255) DEFAULT NULL,
  `funding_status_external` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `milestones` text DEFAULT NULL,
  `strategic_link` varchar(255) DEFAULT NULL,
  `impact_indicator` varchar(255) DEFAULT NULL,
  `expected_outputs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expected_outputs`)),
  `file_proposal` varchar(255) DEFAULT NULL,
  `file_budget` varchar(255) DEFAULT NULL,
  `file_cv` varchar(255) DEFAULT NULL,
  `file_ethics` varchar(255) DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `fiscal_year` varchar(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`id`, `user_id`, `title`, `title_en`, `research_type`, `research_group`, `keywords`, `pi_proportion`, `abstract`, `budget_total`, `budget_details`, `status`, `funding_source_id`, `funding_source_internal`, `funding_source_external`, `funding_status_external`, `start_date`, `end_date`, `milestones`, `strategic_link`, `impact_indicator`, `expected_outputs`, `file_proposal`, `file_budget`, `file_cv`, `file_ethics`, `submission_date`, `fiscal_year`, `created_at`, `updated_at`) VALUES
(11, 1, 'เทส', 'test', 'Applied Research', NULL, 'test,test2', 99, 'ทดสอบ', 200000.00, 'ค่าอาหาร', 'under_review', 6, NULL, NULL, NULL, '2026-05-01', '2026-05-30', '[{\"name\":\"งวดที่ 1\",\"description\":\"ไก่ทอด\"},{\"name\":\"งวดที่ 2\",\"description\":\"หมูทอด\"}]', 'ด้านอาหาร', 'kfc', '[\"Policy Impact\"]', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 08:57:48', '2026-05-26 09:17:51');

-- --------------------------------------------------------

--
-- Table structure for table `proposal_approvals`
--

CREATE TABLE `proposal_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `committee_user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `comment` text DEFAULT NULL,
  `approval_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_budget_items`
--

CREATE TABLE `proposal_budget_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_funding_sources`
--

CREATE TABLE `proposal_funding_sources` (
  `id` int(11) NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Internal','External') NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_teams`
--

CREATE TABLE `proposal_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proposal_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `proportion` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proposal_teams`
--

INSERT INTO `proposal_teams` (`id`, `proposal_id`, `user_id`, `name`, `role`, `proportion`, `created_at`, `updated_at`) VALUES
(8, 11, NULL, 'นายสมวัน สัมนา', 'Co-Investigator', 1, '2026-05-26 09:00:57', '2026-05-26 09:00:57');

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `researcher_id` bigint(20) UNSIGNED NOT NULL,
  `title_th` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `publication_type` varchar(100) DEFAULT NULL,
  `publish_year` varchar(4) DEFAULT NULL,
  `journal_name` varchar(255) DEFAULT NULL,
  `issn` varchar(50) DEFAULT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `issue` varchar(50) DEFAULT NULL,
  `page_length` varchar(50) DEFAULT NULL,
  `indexing_database` varchar(100) DEFAULT NULL,
  `quartile` varchar(10) DEFAULT NULL,
  `journal_level` varchar(100) DEFAULT NULL,
  `impact_factor` varchar(50) DEFAULT NULL,
  `status` enum('draft','under_review','accepted','published') DEFAULT 'draft',
  `doi_url` varchar(255) DEFAULT NULL,
  `utilization_summary` text DEFAULT NULL,
  `file_full_text` varchar(255) DEFAULT NULL,
  `file_acceptance` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_authors`
--

CREATE TABLE `publication_authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publication_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('First Author','Co-Author','Corresponding') DEFAULT 'Co-Author'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qa_records`
--

CREATE TABLE `qa_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recordable_type` varchar(50) NOT NULL COMMENT 'publication or ip_creation',
  `recordable_id` bigint(20) UNSIGNED NOT NULL,
  `similarity_score` int(11) DEFAULT 0,
  `similarity_file` varchar(255) DEFAULT NULL,
  `qa_notes` text DEFAULT NULL,
  `qa_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `export_scopus_id` varchar(100) DEFAULT NULL,
  `export_scopus_status` varchar(50) DEFAULT 'ready',
  `export_wos_id` varchar(100) DEFAULT NULL,
  `export_wos_status` varchar(50) DEFAULT 'ready',
  `export_tci_id` varchar(100) DEFAULT NULL,
  `export_tci_status` varchar(50) DEFAULT 'ready',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_outputs`
--

CREATE TABLE `research_outputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `citation_info` text DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `doi_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-12-22 08:58:52', '2025-12-22 08:58:52'),
(9, 'Researcher', 'web', '2026-03-13 18:39:21', '2026-03-13 18:39:21'),
(10, 'Research Admin', 'web', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(11, 'Review Committee', 'web', '2026-05-26 07:31:35', '2026-05-26 07:31:35'),
(12, 'System Administrator', 'web', '2026-05-26 07:31:35', '2026-05-26 07:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `strategic_reports`
--

CREATE TABLE `strategic_reports` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `report_type` enum('EdPEx','BSC','AUN-QA','Custom') NOT NULL,
  `fiscal_year` int(11) NOT NULL,
  `generated_by` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Generated','Archived') DEFAULT 'Generated',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prefix` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `prefix`, `first_name`, `last_name`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 'System Administrator', 'admin@rims.local', '2025-12-22 08:58:52', '$2y$10$s28xbUGcXpRu0y0KwKzbA.4RF.LS0fyvEZUMwurrqFR3N5fR4YW2y', NULL, '2025-12-22 08:58:52', '2026-02-10 15:58:33'),
(12, NULL, NULL, NULL, 'พีรพัฒน์ แก้วสุรงค์', 'pkaewsurong@gmail.com', NULL, '$2y$10$lP514dOP4Y8CIqU6rf6XuuZgCEA1glXSS5gO391WI0JJECuJ07nQu', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_kpi_scores`
--

CREATE TABLE `user_kpi_scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year` varchar(4) NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `score_value` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for table `data_archives`
--
ALTER TABLE `data_archives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `data_archive_settings`
--
ALTER TABLE `data_archive_settings`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `final_reports`
--
ALTER TABLE `final_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `funders`
--
ALTER TABLE `funders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funding_sources`
--
ALTER TABLE `funding_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_assets`
--
ALTER TABLE `ip_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_assets_project_id_foreign` (`project_id`);

--
-- Indexes for table `ip_creations`
--
ALTER TABLE `ip_creations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `researcher_id` (`researcher_id`);

--
-- Indexes for table `ip_inventors`
--
ALTER TABLE `ip_inventors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_creation_id` (`ip_creation_id`);

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
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpi_metrics`
--
ALTER TABLE `kpi_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `metric_snapshots`
--
ALTER TABLE `metric_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `metric_tiers`
--
ALTER TABLE `metric_tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `progress_reports`
--
ALTER TABLE `progress_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `progress_reports_project_id_foreign` (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_code_unique` (`code`),
  ADD KEY `projects_proposal_id_foreign` (`proposal_id`);

--
-- Indexes for table `project_budget_allocations`
--
ALTER TABLE `project_budget_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_budget_allocations_project_id_foreign` (`project_id`),
  ADD KEY `project_budget_allocations_funding_source_id_foreign` (`funding_source_id`);

--
-- Indexes for table `project_files`
--
ALTER TABLE `project_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_files_project_id_foreign` (`project_id`);

--
-- Indexes for table `project_fundings`
--
ALTER TABLE `project_fundings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_fundings_project_id_foreign` (`project_id`);

--
-- Indexes for table `project_milestones`
--
ALTER TABLE `project_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_reviews_proposal_id_foreign` (`proposal_id`),
  ADD KEY `project_reviews_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `proposals`
--
ALTER TABLE `proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposals_user_id_foreign` (`user_id`),
  ADD KEY `proposals_funding_source_id_foreign` (`funding_source_id`);

--
-- Indexes for table `proposal_approvals`
--
ALTER TABLE `proposal_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_approvals_proposal_id_foreign` (`proposal_id`),
  ADD KEY `proposal_approvals_committee_user_id_foreign` (`committee_user_id`);

--
-- Indexes for table `proposal_budget_items`
--
ALTER TABLE `proposal_budget_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_budget_items_proposal_id_foreign` (`proposal_id`);

--
-- Indexes for table `proposal_funding_sources`
--
ALTER TABLE `proposal_funding_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_id` (`proposal_id`);

--
-- Indexes for table `proposal_teams`
--
ALTER TABLE `proposal_teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_teams_proposal_id_foreign` (`proposal_id`),
  ADD KEY `proposal_teams_user_id_foreign` (`user_id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `researcher_id` (`researcher_id`);

--
-- Indexes for table `publication_authors`
--
ALTER TABLE `publication_authors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`);

--
-- Indexes for table `qa_records`
--
ALTER TABLE `qa_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `research_outputs`
--
ALTER TABLE `research_outputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `research_outputs_project_id_foreign` (`project_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `strategic_reports`
--
ALTER TABLE `strategic_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_kpi_scores`
--
ALTER TABLE `user_kpi_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_kpi_scores_user_id_foreign` (`user_id`),
  ADD KEY `user_kpi_scores_metric_id_foreign` (`metric_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_archives`
--
ALTER TABLE `data_archives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `final_reports`
--
ALTER TABLE `final_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funders`
--
ALTER TABLE `funders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `funding_sources`
--
ALTER TABLE `funding_sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ip_assets`
--
ALTER TABLE `ip_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_creations`
--
ALTER TABLE `ip_creations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_inventors`
--
ALTER TABLE `ip_inventors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kpi_metrics`
--
ALTER TABLE `kpi_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metric_snapshots`
--
ALTER TABLE `metric_snapshots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metric_tiers`
--
ALTER TABLE `metric_tiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progress_reports`
--
ALTER TABLE `progress_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_budget_allocations`
--
ALTER TABLE `project_budget_allocations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_files`
--
ALTER TABLE `project_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_fundings`
--
ALTER TABLE `project_fundings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_milestones`
--
ALTER TABLE `project_milestones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_reviews`
--
ALTER TABLE `project_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `proposals`
--
ALTER TABLE `proposals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `proposal_approvals`
--
ALTER TABLE `proposal_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `proposal_budget_items`
--
ALTER TABLE `proposal_budget_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `proposal_funding_sources`
--
ALTER TABLE `proposal_funding_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `proposal_teams`
--
ALTER TABLE `proposal_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_authors`
--
ALTER TABLE `publication_authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qa_records`
--
ALTER TABLE `qa_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research_outputs`
--
ALTER TABLE `research_outputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `strategic_reports`
--
ALTER TABLE `strategic_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_kpi_scores`
--
ALTER TABLE `user_kpi_scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_archives`
--
ALTER TABLE `data_archives`
  ADD CONSTRAINT `data_archives_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `data_archive_settings`
--
ALTER TABLE `data_archive_settings`
  ADD CONSTRAINT `data_archive_settings_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_reports`
--
ALTER TABLE `final_reports`
  ADD CONSTRAINT `final_reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_assets`
--
ALTER TABLE `ip_assets`
  ADD CONSTRAINT `ip_assets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_creations`
--
ALTER TABLE `ip_creations`
  ADD CONSTRAINT `ip_creations_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ip_creations_ibfk_2` FOREIGN KEY (`researcher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_inventors`
--
ALTER TABLE `ip_inventors`
  ADD CONSTRAINT `ip_inventors_ibfk_1` FOREIGN KEY (`ip_creation_id`) REFERENCES `ip_creations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `metric_snapshots`
--
ALTER TABLE `metric_snapshots`
  ADD CONSTRAINT `metric_snapshots_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_reports`
--
ALTER TABLE `progress_reports`
  ADD CONSTRAINT `progress_reports_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_proposal_id_foreign` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`);

--
-- Constraints for table `project_budget_allocations`
--
ALTER TABLE `project_budget_allocations`
  ADD CONSTRAINT `project_budget_allocations_funding_source_id_foreign` FOREIGN KEY (`funding_source_id`) REFERENCES `project_fundings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_budget_allocations_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_files`
--
ALTER TABLE `project_files`
  ADD CONSTRAINT `project_files_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_fundings`
--
ALTER TABLE `project_fundings`
  ADD CONSTRAINT `project_fundings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_milestones`
--
ALTER TABLE `project_milestones`
  ADD CONSTRAINT `project_milestones_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD CONSTRAINT `project_reviews_proposal_id_foreign` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposals`
--
ALTER TABLE `proposals`
  ADD CONSTRAINT `proposals_funding_source_id_foreign` FOREIGN KEY (`funding_source_id`) REFERENCES `funding_sources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proposals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposal_approvals`
--
ALTER TABLE `proposal_approvals`
  ADD CONSTRAINT `proposal_approvals_committee_user_id_foreign` FOREIGN KEY (`committee_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `proposal_approvals_proposal_id_foreign` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposal_budget_items`
--
ALTER TABLE `proposal_budget_items`
  ADD CONSTRAINT `proposal_budget_items_proposal_id_foreign` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposal_funding_sources`
--
ALTER TABLE `proposal_funding_sources`
  ADD CONSTRAINT `proposal_funding_sources_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposal_teams`
--
ALTER TABLE `proposal_teams`
  ADD CONSTRAINT `proposal_teams_proposal_id_foreign` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proposal_teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publications_ibfk_2` FOREIGN KEY (`researcher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publication_authors`
--
ALTER TABLE `publication_authors`
  ADD CONSTRAINT `publication_authors_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `research_outputs`
--
ALTER TABLE `research_outputs`
  ADD CONSTRAINT `research_outputs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `strategic_reports`
--
ALTER TABLE `strategic_reports`
  ADD CONSTRAINT `strategic_reports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_kpi_scores`
--
ALTER TABLE `user_kpi_scores`
  ADD CONSTRAINT `user_kpi_scores_metric_id_foreign` FOREIGN KEY (`metric_id`) REFERENCES `kpi_metrics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_kpi_scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
