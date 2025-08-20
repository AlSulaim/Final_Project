-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2025 at 10:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartactivity`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `activity_id` int(11) NOT NULL,
  `activity_name` varchar(100) NOT NULL,
  `proposed_date` date DEFAULT NULL,
  `actual_date` date DEFAULT NULL,
  `proposed_start_time` datetime DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `proposed_duration_min` int(11) DEFAULT NULL,
  `actual_duration_min` int(11) DEFAULT NULL,
  `proposed_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `proposed_audience_number` int(11) DEFAULT NULL,
  `actual_audience_number` int(11) DEFAULT NULL,
  `purpose_of_activity` text DEFAULT NULL,
  `serves_student_plan` tinyint(1) DEFAULT NULL,
  `serves_student` tinyint(1) DEFAULT NULL,
  `serves_special_needs` tinyint(1) DEFAULT NULL,
  `activity_manager_id` int(11) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `status` enum('pending','active','ended') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `activity_name`, `proposed_date`, `actual_date`, `proposed_start_time`, `actual_start_time`, `proposed_duration_min`, `actual_duration_min`, `proposed_cost`, `actual_cost`, `proposed_audience_number`, `actual_audience_number`, `purpose_of_activity`, `serves_student_plan`, `serves_student`, `serves_special_needs`, `activity_manager_id`, `club_id`, `status`) VALUES
(1, 'WAS Web Application Security', '2025-01-13', '2025-01-16', '2025-01-16 19:00:00', '2025-01-16 19:00:00', 60, 60, 2000.00, 2000.00, 90, 67, NULL, NULL, NULL, NULL, 1, 1, 'ended'),
(2, 'دورة الإسعافات الأولية', '2025-03-11', '2025-03-11', '2025-03-11 10:00:00', '2025-03-11 10:00:00', 60, 60, 1500.00, 1500.00, 90, 47, NULL, NULL, NULL, NULL, 2, 2, 'ended'),
(3, 'الثقافة الرياضية', '2025-05-04', '2025-05-04', '2025-05-04 10:00:00', '2025-05-04 11:00:00', 90, 90, 0.00, 0.00, 90, 43, NULL, NULL, NULL, NULL, 3, 3, 'ended'),
(4, 'يوم في المحكمة', '2025-07-11', '2025-07-13', '2025-07-13 09:00:00', '2025-07-19 09:00:00', 90, 90, 1000.00, 1000.00, 90, 90, NULL, NULL, NULL, NULL, 4, 4, 'pending'),
(5, 'ديوانية وميض (1)', '2025-02-20', '2025-02-21', '2025-02-21 10:00:00', '2025-02-21 10:00:00', 120, 90, 500.00, 600.00, 70, 32, NULL, NULL, NULL, NULL, 23, 5, 'ended'),
(6, 'دورة تعليم العقد والربطات', '2025-06-01', '2025-06-03', '2025-06-03 17:00:00', '2025-06-03 10:00:00', 60, 60, 1500.00, 1500.00, 100, 82, NULL, NULL, NULL, NULL, 6, 6, 'active'),
(7, 'وجيز', '2025-11-12', '2025-11-12', '2025-11-12 10:00:00', '2025-11-12 10:00:00', 90, 90, 2000.00, 2000.00, 90, 73, NULL, NULL, NULL, NULL, 7, 7, 'pending'),
(8, 'فن صناعة الشمع', '2025-08-27', '2025-08-27', '2025-08-27 10:00:00', '2025-08-27 10:00:00', 90, 90, 1000.00, 1000.00, 90, 65, NULL, NULL, NULL, NULL, 11, 8, 'pending'),
(9, 'بطولة الالعاب الالكترونية (1)', '2025-10-01', '2025-10-01', '2025-10-01 17:00:00', '2025-10-01 17:00:00', 60, 90, 2500.00, 2500.00, 90, 76, NULL, NULL, NULL, NULL, 7, 7, 'pending'),
(10, 'لقاء البحث العلمي (1)', '2025-04-12', '2025-04-15', '2025-04-15 10:00:00', '2025-04-15 10:00:00', 90, 90, 20000.00, 20000.00, 90, 56, NULL, NULL, NULL, NULL, 2, 10, 'ended'),
(11, 'ديوانية وميض (2)', '2025-07-21', '2025-07-21', '2025-07-21 10:00:00', '2025-07-21 17:30:00', 90, 90, 1000.00, 1500.00, 40, 27, NULL, NULL, NULL, NULL, 23, 5, 'ended'),
(12, 'لقاء البحث العلمي (2)', '2025-05-26', '2025-05-30', '2025-05-30 10:00:00', '2025-05-30 10:00:00', 60, 60, 15000.00, 15000.00, 90, 35, NULL, NULL, NULL, NULL, 2, 10, 'ended'),
(13, 'اليوم العالمي لـ اللغة العربية', '2025-07-12', NULL, '2025-07-14 10:00:00', NULL, 120, NULL, 500.00, NULL, 70, NULL, NULL, NULL, NULL, NULL, 23, 5, 'active'),
(14, 'Introduction to cyber security', '2025-02-11', '2025-02-15', '2025-02-15 10:00:00', '2025-07-15 10:00:00', 60, 60, 2000.00, 2000.00, 50, 42, NULL, NULL, NULL, NULL, 1, 1, 'ended'),
(15, 'التفكير التصميمي', '2025-02-21', NULL, '2025-02-26 10:30:00', NULL, 60, NULL, 1000.00, NULL, 50, NULL, NULL, NULL, NULL, NULL, 1, 1, 'pending'),
(18, 'اليوم العالمي للكتب', '2025-08-20', NULL, '2025-08-20 10:30:00', NULL, 90, NULL, 500.00, NULL, 50, NULL, NULL, NULL, NULL, NULL, 23, 5, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `activity_kpi`
--

CREATE TABLE `activity_kpi` (
  `kpi_id` int(11) NOT NULL,
  `kpi_name` varchar(100) DEFAULT NULL,
  `measurement` varchar(50) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_kpi`
--

INSERT INTO `activity_kpi` (`kpi_id`, `kpi_name`, `measurement`, `activity_id`, `note`) VALUES
(29, 'التكلفة', 'cost', NULL, NULL),
(30, 'التاريخ', 'date', NULL, NULL),
(34, 'الرضا', 'feedback', NULL, NULL),
(35, 'الحضور', 'audience', NULL, NULL),
(45, 'خدمة الطالب', 'serves_student', NULL, 'boolean'),
(46, 'خدمة خطة الطالب', 'serves_student_plan', NULL, 'boolean'),
(47, 'خدمة ذوي الاحتياجات الخاصة', 'serves_special_needs', NULL, 'boolean'),
(48, 'هدف الفعالية', 'purpose_of_activity', NULL, 'text');

-- --------------------------------------------------------

--
-- Table structure for table `activity_measurement`
--

CREATE TABLE `activity_measurement` (
  `activity_id` int(11) NOT NULL,
  `kpi_id` int(11) NOT NULL,
  `measure_date` date NOT NULL,
  `result` decimal(10,2) DEFAULT NULL,
  `note` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_measurement`
--

INSERT INTO `activity_measurement` (`activity_id`, `kpi_id`, `measure_date`, `result`, `note`) VALUES
(1, 29, '2025-07-20', 0.00, NULL),
(1, 29, '2025-08-05', 0.00, NULL),
(1, 30, '2025-07-21', 3.00, NULL),
(1, 30, '2025-08-05', 3.00, NULL),
(1, 34, '2025-07-21', 0.00, NULL),
(1, 34, '2025-08-05', 5.00, NULL),
(1, 35, '2025-07-21', -23.00, NULL),
(1, 35, '2025-08-05', -23.00, NULL),
(1, 45, '2025-08-05', NULL, '1'),
(1, 46, '2025-08-05', NULL, '1'),
(1, 47, '2025-08-05', NULL, '0'),
(1, 48, '2025-08-05', NULL, '‏أمن تطبيقات الويب'),
(5, 29, '2025-07-18', 100.00, NULL),
(5, 30, '2025-07-19', 1.00, NULL),
(5, 34, '2025-07-21', 3.25, NULL),
(5, 35, '2025-07-18', -38.00, NULL),
(5, 45, '2025-08-05', NULL, '1'),
(5, 46, '2025-08-05', NULL, '0'),
(5, 47, '2025-08-05', NULL, '1'),
(5, 48, '2025-08-05', NULL, 'جلسة تثقيفية'),
(11, 29, '2025-07-21', 500.00, NULL),
(11, 30, '2025-07-21', 0.00, NULL),
(11, 34, '2025-07-21', 0.00, NULL),
(11, 35, '2025-07-21', -13.00, NULL),
(14, 29, '2025-08-05', 0.00, NULL),
(14, 30, '2025-08-05', 4.00, NULL),
(14, 34, '2025-08-05', 4.00, NULL),
(14, 35, '2025-08-05', -8.00, NULL),
(14, 45, '2025-08-05', NULL, '1'),
(14, 46, '2025-08-05', NULL, '1'),
(14, 47, '2025-08-05', NULL, '1'),
(14, 48, '2025-08-05', NULL, 'تقديم للسايبر سكيورت');

-- --------------------------------------------------------

--
-- Table structure for table `activity_registration`
--

CREATE TABLE `activity_registration` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_registration`
--

INSERT INTO `activity_registration` (`id`, `member_id`, `activity_id`, `status`, `request_date`) VALUES
(1, 24, 5, 'approved', '2025-07-22 02:04:05'),
(2, 24, 15, 'approved', '2025-07-22 02:04:06'),
(3, 24, 6, 'pending', '2025-07-22 02:04:06'),
(4, 24, 4, 'approved', '2025-07-22 02:04:07'),
(9, 1, 1, 'approved', '2025-07-22 02:14:25'),
(10, 1, 14, 'approved', '2025-07-22 02:14:25'),
(11, 1, 15, 'approved', '2025-07-22 02:14:25'),
(12, 2, 2, 'approved', '2025-07-22 02:14:25'),
(13, 3, 3, 'approved', '2025-07-22 02:14:25'),
(14, 4, 4, 'approved', '2025-07-22 02:14:25'),
(15, 5, 5, 'approved', '2025-07-22 02:14:25'),
(16, 5, 11, 'approved', '2025-07-22 02:14:25'),
(17, 5, 13, 'approved', '2025-07-22 02:14:25'),
(18, 6, 6, 'approved', '2025-07-22 02:14:25'),
(19, 7, 7, 'approved', '2025-07-22 02:14:25'),
(20, 7, 9, 'approved', '2025-07-22 02:14:25'),
(21, 10, 3, 'approved', '2025-07-22 02:14:25'),
(22, 11, 8, 'approved', '2025-07-22 02:14:25'),
(23, 12, 10, 'approved', '2025-07-22 02:14:25'),
(24, 12, 12, 'approved', '2025-07-22 02:14:25'),
(25, 14, 10, 'approved', '2025-07-22 02:14:25'),
(26, 14, 12, 'approved', '2025-07-22 02:14:25'),
(27, 16, 1, 'approved', '2025-07-22 02:14:25'),
(28, 16, 14, 'approved', '2025-07-22 02:14:25'),
(29, 16, 15, 'approved', '2025-07-22 02:14:25'),
(30, 17, 5, 'approved', '2025-07-22 02:14:25'),
(31, 17, 11, 'approved', '2025-07-22 02:14:25'),
(32, 17, 13, 'approved', '2025-07-22 02:14:25'),
(33, 18, 1, 'approved', '2025-07-22 02:14:25'),
(34, 18, 14, 'approved', '2025-07-22 02:14:25'),
(35, 18, 15, 'approved', '2025-07-22 02:14:25'),
(36, 19, 7, 'approved', '2025-07-22 02:14:25'),
(37, 19, 9, 'approved', '2025-07-22 02:14:25'),
(38, 20, 3, 'approved', '2025-07-22 02:14:25'),
(39, 21, 10, 'approved', '2025-07-22 02:14:25'),
(40, 21, 12, 'approved', '2025-07-22 02:14:25'),
(41, 22, 6, 'approved', '2025-07-22 02:14:25'),
(42, 23, 5, 'approved', '2025-07-22 02:14:25'),
(43, 23, 11, 'approved', '2025-07-22 02:14:25'),
(44, 23, 13, 'approved', '2025-07-22 02:14:25'),
(45, 24, 11, 'approved', '2025-07-22 02:14:25'),
(46, 26, 5, 'approved', '2025-07-22 02:14:25'),
(47, 26, 11, 'approved', '2025-07-22 02:14:25'),
(48, 26, 13, 'approved', '2025-07-22 02:14:25'),
(49, 27, 3, 'approved', '2025-07-22 02:14:25'),
(50, 24, 13, 'approved', '2025-07-24 17:51:33'),
(55, 32, 5, 'pending', '2025-08-05 12:16:54'),
(56, 32, 15, 'approved', '2025-08-05 12:16:55'),
(57, 32, 6, 'pending', '2025-08-05 12:16:55'),
(58, 32, 4, 'pending', '2025-08-05 12:16:56'),
(59, 32, 13, 'approved', '2025-08-05 12:16:56'),
(60, 32, 8, 'pending', '2025-08-05 12:16:56'),
(61, 32, 9, 'pending', '2025-08-05 12:16:57'),
(62, 32, 7, 'pending', '2025-08-05 12:16:57'),
(63, 41, 15, 'approved', '2025-08-05 12:19:55'),
(64, 41, 14, 'approved', '2025-08-05 12:21:06'),
(65, 24, 18, 'rejected', '2025-08-08 01:23:43'),
(66, 24, 8, 'pending', '2025-08-08 01:23:46'),
(67, 24, 9, 'pending', '2025-08-08 01:23:46'),
(70, 24, 18, 'rejected', '2025-08-08 01:47:24'),
(71, 57, 13, 'approved', '2025-08-08 14:58:39'),
(72, 57, 15, 'rejected', '2025-08-08 14:58:44'),
(73, 57, 4, 'pending', '2025-08-08 14:58:46'),
(74, 57, 8, 'pending', '2025-08-08 14:58:47'),
(75, 57, 18, 'pending', '2025-08-08 14:58:47'),
(76, 57, 14, 'rejected', '2025-08-08 15:00:01'),
(77, 57, 1, 'approved', '2025-08-08 15:00:02'),
(78, 57, 11, 'approved', '2025-08-08 15:00:06'),
(79, 57, 5, 'approved', '2025-08-08 15:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `activity_task`
--

CREATE TABLE `activity_task` (
  `task_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `task_name` varchar(100) DEFAULT NULL,
  `task_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_task`
--

INSERT INTO `activity_task` (`task_id`, `activity_id`, `task_name`, `task_cost`) VALUES
(1, 11, 'توزيع المشروبات', 200.00),
(2, 5, 'تجربة2', 500.00),
(3, 13, 'تجربة', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `activity_team`
--

CREATE TABLE `activity_team` (
  `activity_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_team`
--

INSERT INTO `activity_team` (`activity_id`, `member_id`, `task_id`) VALUES
(1, 57, NULL),
(5, 57, NULL),
(11, 57, NULL),
(13, 32, NULL),
(13, 57, NULL),
(14, 41, NULL),
(15, 24, NULL),
(15, 32, NULL),
(15, 41, NULL),
(11, 26, 1);

-- --------------------------------------------------------

--
-- Table structure for table `activity_team_kpi`
--

CREATE TABLE `activity_team_kpi` (
  `team_kpi_id` int(11) NOT NULL,
  `kpi_name` varchar(100) DEFAULT NULL,
  `note` varchar(20) NOT NULL DEFAULT 'number'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_team_kpi`
--

INSERT INTO `activity_team_kpi` (`team_kpi_id`, `kpi_name`, `note`) VALUES
(4, 'التعاون', 'number'),
(6, 'الاداء العام', 'number'),
(9, 'الالتزام بالحضور', 'boolean'),
(10, 'الالتزام بالزي', 'boolean'),
(11, 'الالتزام بالتعليمات', 'boolean');

-- --------------------------------------------------------

--
-- Table structure for table `activity_team_measurement`
--

CREATE TABLE `activity_team_measurement` (
  `activity_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `team_kpi_id` int(11) NOT NULL,
  `result` decimal(10,2) DEFAULT NULL,
  `measure_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_team_measurement`
--

INSERT INTO `activity_team_measurement` (`activity_id`, `member_id`, `team_kpi_id`, `result`, `measure_date`) VALUES
(5, 5, 6, 4.00, '2025-08-05'),
(5, 5, 9, 1.00, '2025-07-19'),
(5, 5, 10, 1.00, '2025-08-05'),
(5, 17, 6, 4.00, '2025-08-05'),
(5, 17, 9, 1.00, '2025-07-19'),
(5, 17, 10, 0.00, '2025-07-19'),
(5, 17, 11, 0.00, '2025-07-19'),
(5, 23, 9, 1.00, '2025-07-19'),
(5, 23, 10, 1.00, '2025-08-05'),
(5, 24, 4, 4.00, '2025-07-19'),
(5, 24, 6, 4.00, '2025-07-19'),
(5, 24, 9, 1.00, '2025-07-19'),
(5, 24, 10, 1.00, '2025-08-05'),
(5, 26, 6, 4.00, '2025-08-05'),
(5, 26, 9, 1.00, '2025-07-19'),
(5, 26, 10, 1.00, '2025-08-05'),
(11, 5, 4, 5.00, '2025-08-05'),
(11, 5, 6, 5.00, '2025-08-05'),
(11, 5, 9, 1.00, '2025-08-05'),
(11, 5, 10, 0.00, '2025-08-05'),
(11, 5, 11, 1.00, '2025-08-05'),
(11, 17, 4, 4.00, '2025-08-05'),
(11, 17, 6, 5.00, '2025-08-05'),
(11, 17, 9, 1.00, '2025-08-05'),
(11, 17, 10, 1.00, '2025-08-05'),
(11, 17, 11, 1.00, '2025-08-05'),
(11, 24, 4, 4.00, '2025-08-05'),
(11, 24, 6, 5.00, '2025-08-05'),
(11, 24, 9, 1.00, '2025-08-05'),
(11, 24, 10, 1.00, '2025-08-05'),
(11, 24, 11, 1.00, '2025-08-05'),
(11, 26, 4, 4.00, '2025-08-05'),
(11, 26, 6, 4.00, '2025-08-05'),
(11, 26, 9, 1.00, '2025-08-05'),
(11, 26, 10, 1.00, '2025-08-05'),
(11, 26, 11, 1.00, '2025-08-05'),
(14, 1, 4, 5.00, '2025-08-05'),
(14, 1, 6, 5.00, '2025-08-05'),
(14, 1, 9, 1.00, '2025-08-05'),
(14, 1, 10, 1.00, '2025-08-05'),
(14, 1, 11, 1.00, '2025-08-05'),
(14, 32, 4, 5.00, '2025-08-05'),
(14, 32, 6, 4.00, '2025-08-05'),
(14, 32, 9, 1.00, '2025-08-05'),
(14, 32, 10, 1.00, '2025-08-05'),
(14, 32, 11, 1.00, '2025-08-05'),
(14, 35, 4, 4.00, '2025-08-05'),
(14, 35, 6, 4.00, '2025-08-05'),
(14, 35, 9, 1.00, '2025-08-05'),
(14, 35, 10, 1.00, '2025-08-05'),
(14, 35, 11, 1.00, '2025-08-05'),
(14, 41, 4, 4.00, '2025-08-05'),
(14, 41, 6, 4.00, '2025-08-05'),
(14, 41, 9, 1.00, '2025-08-05'),
(14, 41, 10, 0.00, '2025-08-05'),
(14, 41, 11, 1.00, '2025-08-05'),
(14, 42, 4, 2.00, '2025-08-05'),
(14, 42, 6, 3.00, '2025-08-05'),
(14, 42, 9, 1.00, '2025-08-05'),
(14, 42, 10, 1.00, '2025-08-05'),
(14, 42, 11, 1.00, '2025-08-05'),
(15, 1, 4, 3.00, '2025-08-05'),
(15, 1, 6, 4.00, '2025-08-05'),
(15, 1, 9, 1.00, '2025-08-05'),
(15, 1, 10, 1.00, '2025-08-05'),
(15, 1, 11, 0.00, '2025-08-05'),
(15, 32, 4, 5.00, '2025-08-05'),
(15, 32, 6, 5.00, '2025-08-05'),
(15, 32, 9, 1.00, '2025-08-05'),
(15, 32, 10, 0.00, '2025-08-05'),
(15, 32, 11, 1.00, '2025-08-05'),
(15, 35, 4, 4.00, '2025-08-05'),
(15, 35, 6, 4.00, '2025-08-05'),
(15, 35, 9, 1.00, '2025-08-05'),
(15, 35, 10, 0.00, '2025-08-05'),
(15, 35, 11, 1.00, '2025-08-05'),
(15, 41, 4, 4.00, '2025-08-05'),
(15, 41, 6, 4.00, '2025-08-05'),
(15, 41, 9, 1.00, '2025-08-05'),
(15, 41, 10, 1.00, '2025-08-05'),
(15, 41, 11, 1.00, '2025-08-05'),
(15, 42, 4, 5.00, '2025-08-05'),
(15, 42, 6, 5.00, '2025-08-05'),
(15, 42, 9, 1.00, '2025-08-05'),
(15, 42, 10, 1.00, '2025-08-05'),
(15, 42, 11, 1.00, '2025-08-05');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `account` varchar(50) NOT NULL,
  `pwd_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `account`, `pwd_hash`) VALUES
(1, 'admin', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3'),
(2, 'omar', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `initial_date` date DEFAULT NULL,
  `club_manager_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`club_id`, `club_name`, `initial_date`, `club_manager_id`, `note`) VALUES
(1, 'نادي كلية الحاسبات', '2025-07-12', 1, ''),
(2, 'نادي العمل التطوعي', '2025-02-14', 2, ''),
(3, 'نادي الكلية التطبيقية', '2025-03-05', 10, ''),
(4, 'نادي فنار للموهوبين', '2025-04-20', 4, ''),
(5, 'نادي وميض القرائي', '2025-05-25', 23, ''),
(6, 'قسم تمكين الأفكار', '2025-06-18', 6, ''),
(7, 'نادي الألعاب الإلكترونية', '2025-07-01', 7, ''),
(8, 'نادي ارتقاء', '2025-07-19', 11, NULL),
(10, 'نادي البحث العلمي (SRC)', '2025-08-10', 12, ''),
(12, 'نادي الرياضة (SCD)', '2025-07-11', 13, '');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `feedback_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `activity_id`, `member_id`, `content`, `created_at`, `feedback_score`) VALUES
(1, 5, 5, 'جميل', '2025-07-13 01:11:04', 5),
(2, 5, 17, 'غير مفيدة', '2025-07-13 01:12:07', 1),
(3, 5, 7, '..', '2025-07-13 01:13:07', 2),
(5, 11, 24, '..', '2025-07-26 07:59:27', 5),
(6, 1, 1, 'جميل جدا', '2025-08-05 09:18:06', 5),
(7, 14, 1, 'جميل', '2025-08-05 09:18:28', 4),
(8, 14, 41, 'جيد', '2025-08-05 09:21:40', 4),
(9, 15, 32, 'فعالية جيدة', '2025-08-05 09:26:37', 5),
(10, 15, 41, 'استفدت من الفعالية', '2025-08-05 09:26:55', 5),
(11, 11, 17, 'ممتازة', '2025-08-05 09:32:57', 5),
(12, 5, 17, 'جيدة', '2025-08-05 09:33:49', 4),
(13, 5, 5, 'جيد', '2025-08-05 09:34:37', 5),
(14, 11, 5, 'تحتاج بعض التحسينات', '2025-08-05 09:35:02', 2),
(16, 5, 24, '...', '2025-08-07 22:58:14', 4);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `third_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `member_role` enum('Student','ActivityManager') DEFAULT 'Student',
  `faculty` varchar(10) DEFAULT NULL,
  `department` varchar(10) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `account_name` varchar(50) NOT NULL,
  `pwd_hash` varchar(255) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `first_name`, `middle_name`, `third_name`, `last_name`, `member_role`, `faculty`, `department`, `phone_number`, `email`, `club_id`, `account_name`, `pwd_hash`, `registration_date`) VALUES
(1, 'Fahad', 'Ali', 'Saleh', 'Alotaibi', 'ActivityManager', 'FCIT', 'CS', '0511577803', 'fahad1@stu.ut.edu.sa', 1, 'fahad1', '123', '2025-07-11 22:14:06'),
(2, 'Mona', 'Saleh', 'Fahad', 'Alkhaldi', 'ActivityManager', 'FCIT', 'IT', '0541371403', 'mona2@stu.ut.edu.sa', 2, 'mona2', '123', '2025-07-11 22:14:06'),
(3, 'Yousef', 'Hassan', 'Naser', 'Alghamdi', 'Student', 'SCI', 'Biology', '0572123612', 'yousef3@stu.ut.edu.sa', 3, 'yousef3', '123', '2025-07-11 22:14:06'),
(4, 'Abeer', 'Ibrahim', 'Yasir', 'Alshehri', 'ActivityManager', 'EDU', 'Psychology', '0546503856', 'abeer5@stu.ut.edu.sa', 4, 'abeer5', '123', '2025-07-11 22:14:06'),
(5, 'Khaled', 'Abdullah', 'Mohammad', 'Alzahrani', 'Student', 'ENG', 'Mechanical', '0596148445', 'khaled5@stu.ut.edu.sa', 5, 'khaled5', '123', '2025-07-11 22:14:06'),
(6, 'Nouf', 'Omar', 'Faisal', 'Almutairi', 'Student', 'PHARM', 'Pharmacy', '0561230666', 'nouf6@stu.ut.edu.sa', 6, 'nouf6', '123', '2025-07-11 22:14:06'),
(7, 'Sultan', 'Majed', 'Khaled', 'Alsubaie', 'ActivityManager', 'LAW', 'Law', '0597707994', 'sultan7@stu.ut.edu.sa', 7, 'sultan7', '123', '2025-07-11 22:14:06'),
(10, 'Ahmed', 'Mohammed', 'Ali', 'Alshammari', 'ActivityManager', 'FCIT', 'CS', '0524847980', 'ahmed@ut.edu.sa', 3, 'dr_ahmed', '123', '2025-07-11 22:32:58'),
(11, 'Reem', 'Abdullah', 'Nasser', 'Alharbi', 'ActivityManager', 'SCI', 'Chemistry', '0591115919', 'reem@ut.edu.sa', 8, 'dr_reem', '123', '2025-07-11 22:32:58'),
(12, 'Nasser', 'Yahya', 'Salem', 'Alsulami', 'ActivityManager', 'ENG', 'Civil', '0511035659', 'nasser@ut.edu.sa', 10, 'dr_nasser', '123', '2025-07-11 22:32:58'),
(13, 'Noura', 'Fahad', 'Ali', 'Alharbi', 'ActivityManager', 'CS', 'IT', '0541830541', 'noura@stu.ut.edu.sa', 12, 'noura10', '123', '2025-07-11 22:58:15'),
(14, 'Salem', 'Ibrahim', 'Mohammed', 'Aldossari', 'Student', 'ENG', 'CE', '0576045733', 'salem@stu.ut.edu.sa', 10, 'salem11', '123', '2025-07-11 22:58:15'),
(15, 'Lama', 'Abdullah', 'Saad', 'Alqahtani', 'Student', 'MED', 'MBBS', '0564737046', 'lama@stu.ut.edu.sa', 12, 'lama12', '123', '2025-07-11 22:58:15'),
(16, 'Rakan', 'Yousef', 'Hassan', 'Alomari', 'Student', 'SCI', 'BIO', '0585548036', 'rakan@stu.ut.edu.sa', NULL, 'rakan13', '123', '2025-07-11 22:58:15'),
(17, 'Hanin', 'Ali', 'Faisal', 'Alshahrani', 'Student', 'CS', 'CS', '0543529045', 'hanin@stu.ut.edu.sa', 5, 'hanin14', '123', '2025-07-11 22:58:15'),
(18, 'Faisal', 'Majid', 'Nasser', 'Alshehri', 'Student', 'SCI', 'CHM', '0541001121', 'faisal@stu.ut.edu.sa', NULL, 'faisal15', '123', '2025-07-11 22:58:15'),
(19, 'Reem', 'Khalid', 'Mohammed', 'Alotaibi', 'ActivityManager', 'BUS', 'ACCT', '0564418473', 'reem@stu.ut.edu.sa', 7, 'reem16', '123', '2025-07-11 22:58:15'),
(20, 'Saad', 'Abdulaziz', 'Sultan', 'Alenezi', 'Student', 'ENG', 'EE', '0599089006', 'saad@stu.ut.edu.sa', 3, 'saad17', '123', '2025-07-11 22:58:15'),
(21, 'Maha', 'Turki', 'Ibrahim', 'Almutairi', 'Student', 'MED', 'NUR', '0522189618', 'maha@stu.ut.edu.sa', 10, 'maha18', '123', '2025-07-11 22:58:15'),
(22, 'Badr', 'Omar', 'Khalid', 'Alzahrani', 'Student', 'CS', 'SE', '0573681073', 'badr@stu.ut.edu.sa', 6, 'badr19', '123', '2025-07-11 22:58:15'),
(23, 'Activity', 'Manager', '', 'Activity', 'ActivityManager', 'FCIT', 'CS', '0521836518', 'act@stu.ut.edu.sa', 5, 'act', '123', '2025-07-12 00:08:56'),
(24, 'Omar', 'Mohammed', 'Ahmed', 'Saleem', 'Student', 'FCIT', 'CS', '0533266418', '431000594@stu.ut.edu.sa', 5, '431000594', '123', '2025-07-12 00:09:55'),
(25, 'Mahmud', 'Aljohani', '', '', 'Student', 'FCIT', 'CS', '0535187298', '431004394@stu.ut.edu.sa', 12, '431004394', '123', '2025-07-12 00:10:22'),
(26, 'Badr', 'Almalki', '', '', 'Student', 'FCIT', 'CS', '0581518386', '421007112@stu.ut.edu.sa', 5, '421007112', '123', '2025-07-12 00:10:32'),
(27, 'Sattam', 'Albalawi', '', '', 'Student', 'FCIT', 'CS', '0561230636', '421008397@stu.ut.edu.sa', 3, '421008397', '123', '2025-07-12 03:34:32'),
(28, 'Sara', 'Ibrahim', 'Waleed', 'Alshammari', 'Student', 'LAW', 'Law', '0510872248', 'sara.alshammari0@stu.ut.edu.sa', 8, 'sara0', '123', '2024-08-03 00:00:00'),
(29, 'Turki', 'Yahia', 'Abdullah', 'Alharbi', 'Student', 'FCIT', 'CS', '0538898923', 'turki.alharbi1@stu.ut.edu.sa', NULL, 'turki1', '123', '2025-06-04 00:00:00'),
(30, 'Salma', 'Omar', 'Ahmed', 'Alotaibi', 'Student', 'LAW', 'Law', '0556164955', 'salma.alotaibi2@stu.ut.edu.sa', NULL, 'salma2', '123', '2025-03-13 00:00:00'),
(31, 'Noura', 'Ibrahim', 'Yousef', 'Alqahtani', 'Student', 'ENG', 'EE', '0560806024', 'noura.alqahtani3@stu.ut.edu.sa', 8, 'noura3', '123', '2024-10-17 00:00:00'),
(32, 'Mona', 'Hassan', 'Mohammed', 'Almalki', 'Student', 'FCIT', 'IT', '0535808537', 'mona.almalki4@stu.ut.edu.sa', 1, 'mona4', '123', '2025-06-21 00:00:00'),
(33, 'Noura', 'Abdulrahman', 'Abdullah', 'Alqahtani', 'Student', 'PHARM', 'Pharmacy', '0541244663', 'noura.alqahtani5@stu.ut.edu.sa', NULL, 'noura5', '123', '2025-01-13 00:00:00'),
(34, 'Huda', 'Ibrahim', 'Mohammed', 'Alharbi', 'Student', 'FCIT', 'CS', '0559684848', 'huda.alharbi6@stu.ut.edu.sa', 2, 'huda6', '123', '2025-04-10 00:00:00'),
(35, 'Lama', 'Hamad', 'Waleed', 'Alharbi', 'Student', 'FCIT', 'CS', '0581691040', 'lama.alharbi7@stu.ut.edu.sa', 1, 'lama7', '123', '2025-03-23 00:00:00'),
(36, 'Reem', 'Ibrahim', 'Saad', 'Alshammari', 'Student', 'FCIT', 'CS', '0595899313', 'reem.alshammari8@stu.ut.edu.sa', 4, 'reem8', '123', '2024-10-14 00:00:00'),
(37, 'Sara', 'Yahia', 'Hamad', 'Almutairi', 'Student', 'EDU', 'Psychology', '0514308421', 'sara.almutairi9@stu.ut.edu.sa', NULL, 'sara9', '123', '2025-01-02 00:00:00'),
(38, 'Emtenan', 'Hamad', 'Omar', 'Almalki', 'Student', 'FCIT', 'CS', '0552235350', 'emtenan.almalki10@stu.ut.edu.sa', 4, 'emtenan10', '123', '2024-08-25 00:00:00'),
(39, 'Khalid', 'Omar', 'Nasser', 'Alharbi', 'Student', 'PHARM', 'Pharmacy', '0545551614', 'khalid.alharbi11@stu.ut.edu.sa', NULL, 'khalid11', '123', '2025-03-22 00:00:00'),
(40, 'Waleed', 'Ali', 'Abdullah', 'Almalki', 'Student', 'SCI', 'Biology', '0567503414', 'waleed.almalki12@stu.ut.edu.sa', NULL, 'waleed12', '123', '2025-01-03 00:00:00'),
(41, 'Nasser', 'Abdulrahman', 'Khaled', 'Alshehri', 'Student', 'FCIT', 'CS', '0576238574', 'nasser.alshehri13@stu.ut.edu.sa', 1, 'nasser13', '123', '2025-07-02 00:00:00'),
(42, 'Lina', 'Saeed', 'Khaled', 'Alzahrani', 'Student', 'FCIT', 'IT', '0590048665', 'lina.alzahrani14@stu.ut.edu.sa', 1, 'lina14', '123', '2025-01-10 00:00:00'),
(43, 'Saad', 'Hassan', 'Nasser', 'Alshehri', 'Student', 'SCI', 'Biology', '0543744231', 'saad.alshehri15@stu.ut.edu.sa', NULL, 'saad15', '123', '2025-07-21 00:00:00'),
(44, 'Lina', 'Ali', 'Abdullah', 'Alotaibi', 'Student', 'SCI', 'Biology', '0524972279', 'lina.alotaibi16@stu.ut.edu.sa', 4, 'lina16', '123', '2024-12-16 00:00:00'),
(45, 'Reem', 'Ibrahim', 'Hamad', 'Alshammari', 'Student', 'PHARM', 'Pharmacy', '0577187530', 'reem.alshammari17@stu.ut.edu.sa', NULL, 'reem17', '123', '2024-11-09 00:00:00'),
(46, 'Lina', 'Mohsen', 'Yousef', 'Almalki', 'Student', 'FCIT', 'CS', '0536697396', 'lina.almalki18@stu.ut.edu.sa', 4, 'lina18', '123', '2025-01-16 00:00:00'),
(47, 'Reem', 'Ali', 'Yousef', 'Alsuhaim', 'Student', 'FCIT', 'CS', '0590388981', 'reem.alsuhaim19@stu.ut.edu.sa', NULL, 'reem19', '123', '2024-11-18 00:00:00'),
(48, 'Ghada', 'Hamad', 'Fahad', 'Alshammari', 'Student', 'ENG', 'Mechanical', '0542138745', 'ghada.alshammari20@stu.ut.edu.sa', NULL, 'ghada20', '123', '2025-03-25 00:00:00'),
(49, 'Yousef', 'Hamad', 'Ahmed', 'Alghamdi', 'Student', 'EDU', 'Psychology', '0519289546', 'yousef.alghamdi21@stu.ut.edu.sa', NULL, 'yousef21', '123', '2025-05-23 00:00:00'),
(50, 'Dana', 'Ibrahim', 'Yousef', 'Alharbi', 'Student', 'SCI', 'Biology', '0545575298', 'dana.alharbi22@stu.ut.edu.sa', 2, 'dana22', '123', '2024-09-19 00:00:00'),
(51, 'Turki', 'Abdulrahman', 'Yousef', 'Almutairi', 'Student', 'FCIT', 'IT', '0551837852', 'turki.almutairi23@stu.ut.edu.sa', 7, 'turki23', '123', '2024-08-17 00:00:00'),
(52, 'Nasser', 'Ibrahim', 'Yousef', 'Alghamdi', 'Student', 'ENG', 'Mechanical', '0526240908', 'nasser.alghamdi24@stu.ut.edu.sa', 2, 'nasser24', '123', '2025-04-02 00:00:00'),
(53, 'Salma', 'Yahia', 'Hamad', 'Almalki', 'Student', 'ENG', 'CE', '0584346088', 'salma.almalki25@stu.ut.edu.sa', 8, 'salma25', '123', '2024-09-28 00:00:00'),
(54, 'Sara', 'Fahad', 'Ahmed', 'Alsuhaim', 'Student', 'EDU', 'Psychology', '0540728046', 'sara.alsuhaim26@stu.ut.edu.sa', NULL, 'sara26', '123', '2025-07-10 00:00:00'),
(55, 'Fahad', 'Hamad', 'Yousef', 'Almutairi', 'Student', 'EDU', 'Psychology', '0547376585', 'fahad.almutairi27@stu.ut.edu.sa', 7, 'fahad27', '123', '2024-11-20 00:00:00'),
(56, 'Layla', 'Ali', 'Khaled', 'Almalki', 'Student', 'ENG', 'CE', '0587337818', 'layla.almalki28@stu.ut.edu.sa', 7, 'layla28', '123', '2025-03-24 00:00:00'),
(57, 'Member', 'Member', 'Member', 'Member', 'Student', 'FCIT', 'IT', '0598715803', 'member@stu.ut.edu.sa', 1, 'mmbr', '$2y$10$2J.wFRzHnkIjDz3XE5ZE7OiyteKW/K/snCaYVFgaDxQ483L4DOJRS', '2025-08-08 14:58:30');

-- --------------------------------------------------------

--
-- Table structure for table `partnerships`
--

CREATE TABLE `partnerships` (
  `partnership_id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `partner_name` varchar(100) DEFAULT NULL,
  `partnership_type` enum('داخلية','خارجية') DEFAULT 'خارجية',
  `category` enum('صحية','رياضية','تجارية','تطوعية','مجتمعية','إعلامية','تدريبية','أخرى') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `status` enum('pending','ended') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partnerships`
--

INSERT INTO `partnerships` (`partnership_id`, `club_id`, `partner_name`, `partnership_type`, `category`, `description`, `contact_email`, `status`, `created_at`, `start_date`, `end_date`) VALUES
(3, 2, 'مستشفى الملك فهد التخصصي', 'خارجية', 'صحية', 'حملات توعوية', 'Tbhc-info@moh.gov.sa', 'pending', '2025-07-27 15:51:30', '2025-07-16', '2025-07-16'),
(4, 5, 'جامعة فهد بن سلطان', 'خارجية', 'مجتمعية', 'شراكة تسعى لبناء علاقة إيجابية بين الجامعة والمجتمع المحلي من خلال تبادل الخدمات والدعم المتبادل', 'info@fbsu.edu.sa', 'pending', '2025-07-27 16:08:52', '2025-07-31', '2025-07-31'),
(5, 3, 'مديرية الدفاع المدني', 'خارجية', 'تطوعية', 'شراكة تطوعية تهدف إلى إشراك طلاب الجامعة في حملات الدفاع المدني التوعوية', '998@998.gov.sa', 'pending', '2025-07-27 16:12:00', '2025-07-28', '2025-07-29'),
(6, 1, 'المباحث العامة', 'خارجية', 'تدريبية', 'حملة تدريبية', '990@pss.gov.sa', 'pending', '2025-08-05 09:55:04', '2025-08-05', '2025-08-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `activity_manager_id` (`activity_manager_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `activity_kpi`
--
ALTER TABLE `activity_kpi`
  ADD PRIMARY KEY (`kpi_id`),
  ADD KEY `fk_activity` (`activity_id`);

--
-- Indexes for table `activity_measurement`
--
ALTER TABLE `activity_measurement`
  ADD PRIMARY KEY (`activity_id`,`kpi_id`,`measure_date`),
  ADD KEY `kpi_id` (`kpi_id`);

--
-- Indexes for table `activity_registration`
--
ALTER TABLE `activity_registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_member` (`member_id`);

--
-- Indexes for table `activity_task`
--
ALTER TABLE `activity_task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `activity_team`
--
ALTER TABLE `activity_team`
  ADD PRIMARY KEY (`activity_id`,`member_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `activity_team_kpi`
--
ALTER TABLE `activity_team_kpi`
  ADD PRIMARY KEY (`team_kpi_id`);

--
-- Indexes for table `activity_team_measurement`
--
ALTER TABLE `activity_team_measurement`
  ADD PRIMARY KEY (`activity_id`,`member_id`,`team_kpi_id`,`measure_date`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `team_kpi_id` (`team_kpi_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `account` (`account`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`club_id`),
  ADD KEY `club_manager_id` (`club_manager_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `account_name` (`account_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `partnerships`
--
ALTER TABLE `partnerships`
  ADD PRIMARY KEY (`partnership_id`),
  ADD KEY `club_id` (`club_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `activity_kpi`
--
ALTER TABLE `activity_kpi`
  MODIFY `kpi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `activity_registration`
--
ALTER TABLE `activity_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `activity_task`
--
ALTER TABLE `activity_task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `activity_team_kpi`
--
ALTER TABLE `activity_team_kpi`
  MODIFY `team_kpi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `partnerships`
--
ALTER TABLE `partnerships`
  MODIFY `partnership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`activity_manager_id`) REFERENCES `members` (`member_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activity_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_kpi`
--
ALTER TABLE `activity_kpi`
  ADD CONSTRAINT `fk_activity` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_measurement`
--
ALTER TABLE `activity_measurement`
  ADD CONSTRAINT `activity_measurement_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_measurement_ibfk_2` FOREIGN KEY (`kpi_id`) REFERENCES `activity_kpi` (`kpi_id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_registration`
--
ALTER TABLE `activity_registration`
  ADD CONSTRAINT `fk_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_task`
--
ALTER TABLE `activity_task`
  ADD CONSTRAINT `activity_task_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_team`
--
ALTER TABLE `activity_team`
  ADD CONSTRAINT `activity_team_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_team_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_team_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `activity_task` (`task_id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_team_measurement`
--
ALTER TABLE `activity_team_measurement`
  ADD CONSTRAINT `activity_team_measurement_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_team_measurement_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_team_measurement_ibfk_3` FOREIGN KEY (`team_kpi_id`) REFERENCES `activity_team_kpi` (`team_kpi_id`) ON DELETE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`club_manager_id`) REFERENCES `members` (`member_id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `partnerships`
--
ALTER TABLE `partnerships`
  ADD CONSTRAINT `partnerships_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
