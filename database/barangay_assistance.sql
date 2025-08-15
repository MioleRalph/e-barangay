-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 15, 2025 at 07:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay_assistance`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` varchar(200) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(200) NOT NULL,
  `contact_number` int(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` int(30) NOT NULL,
  `verification_token` varchar(255) NOT NULL,
  `verification_status` varchar(100) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `profile_pic`, `first_name`, `last_name`, `date_of_birth`, `address`, `contact_number`, `email`, `password`, `user_type`, `verification_token`, `verification_status`, `date_registered`) VALUES
('1', 'img_681625582ec592.27101077.jpg', 'Administrator', 'Account', '0000-00-00', 'Libas, Sogod, Southern Leyte', 0, 'admin@gmail.com', '$2y$10$ZbnrnMftz3MrI4tj9htW1OMxUczt5n7aHvmpl8uPCLsc9zRh/scUu', 1, '', 'verified', '2025-04-15 11:16:18'),
('12', 'profile_682db037c4d873.32387971.jpg', 'Ralph', 'Miole', '2003-07-04', 'Libas, Sogod, Southern Leyte', 0, 'ralphmiole2001@gmail.com', '$2y$10$qYeer5ckMIEQkc069wsP0.Pm5/WHDADqWfQ5V53kUHwITeiqwilcC', 2, 'eb258e70619963f0cae6f8e7a31e9971', 'verified', '2025-06-21 10:51:35'),
('13', 'profile_682db037c4d873.32387971.jpg', 'Ralph', 'Miole', '2003-07-04', 'Libas, Sogod, Southern Leyte', 0, 'ralphmiole2001@gmail.com', '$2y$10$qYeer5ckMIEQkc069wsP0.Pm5/WHDADqWfQ5V53kUHwITeiqwilcC', 2, 'eb258e70619963f0cae6f8e7a31e9971', 'verified', '2025-05-21 10:51:35'),
('2', 'img_6813ac355893b7.11210402.jpeg', 'resident', 'account', '2025-05-14', 'Libas, Sogod, Southern Leyte', 0, 'resident@gmail.com', '$2y$10$76YYpG2a6bG0ma5pCUV1qeLhGzvRe4SKlfChiuGF4BTl/q/4Xhjju', 2, '', 'verified', '2025-04-15 11:16:18'),
('3', 'img_6813ac355893b7.11210402.jpeg', 'resident', 'account 2', '0000-00-00', '', 0, 'resident2@gmail.com', '$2y$10$76YYpG2a6bG0ma5pCUV1qeLhGzvRe4SKlfChiuGF4BTl/q/4Xhjju', 2, '', 'not verified', '2025-04-15 11:16:18'),
('59f43cebe1a601e0', 'profile_682db037c4d873.32387971.jpg', 'Ralph', 'Miole', '2003-07-04', 'Libas, Sogod, Southern Leyte', 0, 'ralphmiole2001@gmail.com', '$2y$10$qYeer5ckMIEQkc069wsP0.Pm5/WHDADqWfQ5V53kUHwITeiqwilcC', 2, 'eb258e70619963f0cae6f8e7a31e9971', 'verified', '2025-05-21 10:51:35'),
('71d3263819c61654', 'img_6813ac5fc2fe77.25199207.jpeg', 'official', 'account', '0000-00-00', '', 0, 'official@gmail.com', '$2y$10$qOPdSaxJvFeGPmULBI/fD.uqHj5qN0OuXKn4EtcfPJ4eXVIkKnv0e', 3, '24ac44c77473fb8a3d5361361530f340', 'verified', '2025-04-25 06:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `aid_events`
--

CREATE TABLE `aid_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('Financial','Goods','Medical','Other') NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aid_events`
--

INSERT INTO `aid_events` (`id`, `title`, `description`, `type`, `total_amount`, `date_started`, `date_ended`, `created_at`) VALUES
(1, 'Typhoon Odette Relief', 'Relief for affected families', 'Goods', 15000.00, '2023-12-16', '2023-12-20', '2025-05-19 14:24:20'),
(2, 'Flood Aid', 'Cash assistance for flood victims', 'Financial', 8000.00, '2024-01-10', '2024-01-12', '2025-05-19 14:24:20'),
(3, 'Flood Aid', 'Cash assistance for flood victims', 'Other', 8000.00, '2024-01-10', '2024-01-12', '2025-05-19 14:24:20'),
(4, 'Flood Aid', 'Cash assistance for flood victims', 'Financial', 8000.00, '2024-01-10', '2024-01-12', '2025-05-19 14:24:20');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `posted_by` varchar(100) NOT NULL,
  `audience` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attachment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `category`, `posted_by`, `audience`, `status`, `created_at`, `updated_at`, `attachment`) VALUES
(2, 'a', 'test', 'Health', '71d3263819c61654', 'All', 'Active', '2025-05-18 13:26:42', '2025-05-10 10:47:09', 'announcement_681f82cdd82075.39200611.jpg'),
(5, 'operation tuli ', 'adsadasdasdasdasdasdasdasd', 'Financial Assistance', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:24', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(6, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Events', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(7, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Emergency', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(8, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Public Notice', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(9, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Lost & Found', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(10, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Job Postings', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg'),
(11, 'operation tuli ', 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quod ducimus alias doloremque, odio porro, quibusdam aperiam a officia deleniti ut ex quasi velit? Autem aut distinctio rerum ut sit quam?', 'Community Projects', '71d3263819c61654', 'Residents', 'Active', '2025-06-07 15:17:43', '2025-05-22 00:22:20', 'announcement_682ec29c4b3c68.14434280.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `file_request`
--

CREATE TABLE `file_request` (
  `id` int(11) NOT NULL,
  `user_id` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `amount` int(11) NOT NULL,
  `transaction_type` varchar(100) NOT NULL,
  `transaction_status` varchar(50) NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_request`
--

INSERT INTO `file_request` (`id`, `user_id`, `name`, `date_of_birth`, `email`, `amount`, `transaction_type`, `transaction_status`, `date_submitted`) VALUES
(1, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Barangay Clearance', 'Rejected', '2025-05-21 11:32:21'),
(2, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Certificate of Indigency', 'Pending', '2025-05-21 17:42:42'),
(10, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Barangay Clearance', 'approved', '2025-05-22 04:50:42'),
(11, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Blotter Request', 'Rejected', '2025-05-21 17:42:42'),
(12, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Blotter Request', 'Rejected', '2025-06-21 17:42:42'),
(13, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Blotter Request', 'Pending', '2025-06-21 17:42:42'),
(14, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Certificate of Residency', 'Rejected', '2025-05-21 17:42:42'),
(15, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Barangay Clearance', 'Pending', '2025-06-18 15:37:42'),
(16, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Barangay Clearance', 'Pending', '2025-07-24 08:51:19'),
(17, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 100, 'Barangay Clearance', 'Pending', '2025-08-12 09:43:38'),
(18, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 100, 'Barangay Clearance', 'approved', '2025-08-12 09:56:30'),
(19, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 100, 'Barangay Clearance', 'Pending', '2025-08-12 09:59:05'),
(20, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 0, 'Certificate of Indigency', 'Pending', '2025-08-12 10:01:29'),
(21, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 100, 'Certificate of Residency', 'Pending', '2025-08-12 10:01:47'),
(22, '71d3263819c61654', 'official account', '2025-08-12', 'official@gmail.com', 0, 'Blotter Request', 'Pending', '2025-08-12 10:02:05'),
(23, '2', 'resident account', '2025-05-14', 'resident@gmail.com', 100, 'Barangay Clearance', 'Pending', '2025-08-13 08:41:24');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(20) NOT NULL,
  `user_id` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `name`, `email`, `activity_type`, `user_type`, `timestamp`) VALUES
(101, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-04 17:57:06'),
(102, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-04 18:08:57'),
(104, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-10 14:34:55'),
(105, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-10 14:56:58'),
(107, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-10 15:01:53'),
(108, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-10 15:02:00'),
(109, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-10 15:11:06'),
(110, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-10 15:11:12'),
(111, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-10 15:15:57'),
(112, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-10 15:16:05'),
(113, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-10 15:16:28'),
(114, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-10 15:16:39'),
(115, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-10 18:25:22'),
(116, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-10 18:25:30'),
(117, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-10 18:35:33'),
(118, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-10 18:35:40'),
(119, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-16 15:44:01'),
(120, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-16 15:46:01'),
(121, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-16 16:00:00'),
(122, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-16 16:00:08'),
(123, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-18 09:18:00'),
(124, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-18 12:47:48'),
(125, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-18 12:48:09'),
(126, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-18 12:53:23'),
(127, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-18 12:53:31'),
(128, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-18 13:12:41'),
(129, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-18 13:12:48'),
(131, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-18 13:15:42'),
(132, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-18 14:39:32'),
(133, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-18 14:39:39'),
(134, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-18 14:46:27'),
(135, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-18 14:46:35'),
(136, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-19 14:25:02'),
(137, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-21 10:03:27'),
(138, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-21 10:08:28'),
(139, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-21 10:08:45'),
(140, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-21 10:09:18'),
(141, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 10:09:29'),
(142, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 10:26:01'),
(143, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-21 10:26:10'),
(144, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-21 10:29:11'),
(145, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-21 10:29:20'),
(146, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-21 10:33:39'),
(147, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 10:34:50'),
(148, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 10:38:57'),
(149, '59f43cebe1a601e0', 'Ralph Miole', 'ralphmiole2001@gmail.com', 'login', '2', '2025-05-21 10:53:41'),
(150, '59f43cebe1a601e0', 'Ralph Miole', 'ralphmiole2001@gmail.com', 'Logout', '2', '2025-05-21 10:54:15'),
(151, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 17:20:34'),
(152, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 17:51:59'),
(153, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 17:57:23'),
(154, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 17:59:12'),
(155, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-21 17:59:28'),
(156, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-21 18:10:56'),
(157, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 18:11:18'),
(158, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 18:19:20'),
(159, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-21 18:19:27'),
(160, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-21 19:06:23'),
(161, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 19:06:30'),
(162, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-21 19:16:51'),
(163, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-21 19:16:57'),
(164, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-21 19:36:34'),
(165, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-21 19:36:43'),
(166, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-22 02:05:42'),
(167, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-22 02:06:00'),
(168, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-22 02:06:13'),
(169, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-05-22 04:51:58'),
(170, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-22 04:52:11'),
(171, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-22 04:52:56'),
(172, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-22 04:53:02'),
(173, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-22 06:16:43'),
(174, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-22 06:19:39'),
(175, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-22 06:19:55'),
(176, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-05-22 06:35:47'),
(177, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-05-22 06:35:53'),
(178, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-06-07 14:27:47'),
(179, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-06-07 14:47:45'),
(180, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-06-07 14:47:54'),
(181, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-06-07 15:06:31'),
(182, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-06-07 15:06:42'),
(183, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-06-07 15:26:18'),
(184, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-06-13 14:42:34'),
(185, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-06-18 14:15:55'),
(186, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-06-18 14:29:08'),
(187, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-06-18 14:29:54'),
(188, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-06-18 15:08:33'),
(189, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-06-18 15:08:43'),
(190, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-06-18 15:42:46'),
(191, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-06-18 15:42:55'),
(192, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-06-18 15:49:09'),
(193, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-06-18 15:49:23'),
(194, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-06-30 05:11:26'),
(195, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-06-30 05:12:15'),
(196, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-07-24 08:36:42'),
(197, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-07-24 08:44:37'),
(198, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-07-24 08:44:49'),
(199, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-07-24 08:49:42'),
(200, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-07-24 08:50:19'),
(201, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-07-24 08:51:25'),
(202, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-07-24 08:51:41'),
(203, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-07-24 08:52:19'),
(204, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-07-24 08:52:39'),
(205, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-07-24 08:59:27'),
(206, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-12 09:10:38'),
(207, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-08-12 09:11:19'),
(208, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-12 09:11:28'),
(209, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-12 09:11:35'),
(210, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-08-12 09:11:45'),
(211, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-08-12 09:12:13'),
(212, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-12 09:14:56'),
(213, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-12 09:15:20'),
(214, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-08-12 09:15:31'),
(215, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-12 09:21:05'),
(216, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-12 13:11:33'),
(217, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-13 05:49:09'),
(218, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-08-13 05:54:21'),
(219, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 05:54:30'),
(220, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-13 06:06:19'),
(221, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-08-13 06:06:43'),
(222, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-08-13 06:09:56'),
(223, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 06:10:09'),
(224, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-13 06:16:25'),
(225, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-13 06:16:32'),
(226, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-08-13 06:40:22'),
(227, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 06:40:34'),
(228, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-13 06:45:51'),
(229, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-13 06:45:59'),
(230, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-08-13 06:47:23'),
(231, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 08:34:34'),
(232, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-13 08:35:46'),
(233, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-13 08:36:05'),
(234, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-08-13 08:40:24'),
(235, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 08:40:35'),
(236, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-13 08:41:03'),
(237, '2', 'resident account', 'resident@gmail.com', 'login', '2', '2025-08-13 08:41:16'),
(238, '2', 'resident account', 'resident@gmail.com', 'Logout', '2', '2025-08-13 08:41:56'),
(239, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-13 08:42:06'),
(240, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-08-15 05:37:50'),
(241, '71d3263819c61654', 'official account', 'official@gmail.com', 'Logout', '3', '2025-08-15 05:38:13'),
(242, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-08-15 05:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `resident_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` int(1) NOT NULL,
  `resident_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `resident_id`, `message`, `is_read`, `resident_type`, `created_at`) VALUES
(2, '2', 'Your blotter request has been approved.', 1, 'null', '2025-06-18 14:46:11'),
(4, '2', 'Your blotter request has been rejected.', 1, 'null', '2025-06-18 14:51:48'),
(5, '2', 'Your Barangay Clearance request has been approved.', 1, 'null', '2025-06-18 14:59:16'),
(6, '2', 'Your Barangay Clearance request has been rejected.', 1, 'null', '2025-06-18 14:59:28'),
(7, '2', 'Your Certificate of Residency request has been approved.', 1, 'null', '2025-06-18 15:02:05'),
(8, '2', 'Your Certificate of Residency request has been rejected.', 1, 'null', '2025-06-18 15:02:08'),
(9, '2', 'Your Certificate of Residency request has been approved.', 1, 'null', '2025-06-18 15:02:37'),
(10, '2', 'Your Certificate of Residency request has been rejected.', 1, 'null', '2025-06-18 15:02:39'),
(11, '2', 'Your Barangay Clearance request has been approved.', 1, 'null', '2025-07-24 08:52:05'),
(12, '71d3263819c61654', 'Your Barangay Clearance request has been approved.', 0, 'null', '2025-08-13 06:40:51'),
(13, '2', 'Your Barangay Clearance request has been rejected.', 0, 'null', '2025-08-13 08:42:33'),
(14, '2', 'Your Barangay Clearance request has been approved.', 0, 'null', '2025-08-13 08:42:37'),
(15, '2', 'Your Barangay Clearance request has been rejected.', 0, 'null', '2025-08-13 08:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `official_notifications`
--

CREATE TABLE `official_notifications` (
  `id` int(11) NOT NULL,
  `resident_name` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `official_notifications`
--

INSERT INTO `official_notifications` (`id`, `resident_name`, `message`, `is_read`, `created_at`) VALUES
(4, 'official account', 'New Barangay Clearance request submitted by OFFICIAL ACCOUNT Email: OFFICIAL@GMAIL.COM. Please review and process the request.', 1, '2025-08-12 09:59:05'),
(5, 'official account', 'New Certificate of Indigency request submitted by OFFICIAL ACCOUNT Email: OFFICIAL@GMAIL.COM. Please review and process the request.', 1, '2025-08-12 10:01:29'),
(6, 'official account', 'New Certificate of Residency request submitted by OFFICIAL ACCOUNT Email: OFFICIAL@GMAIL.COM. Please review and process the request.', 1, '2025-08-12 10:01:47'),
(7, 'official account', 'New Blotter request submitted by OFFICIAL ACCOUNT Email: OFFICIAL@GMAIL.COM. Please review and process the request.', 1, '2025-08-12 10:02:05'),
(8, 'resident account', 'New Barangay Clearance request submitted by RESIDENT ACCOUNT Email: RESIDENT@GMAIL.COM. Please review and process the request.', 0, '2025-08-13 08:41:24');

-- --------------------------------------------------------

--
-- Table structure for table `official_requests_logs`
--

CREATE TABLE `official_requests_logs` (
  `id` int(11) NOT NULL,
  `approved_id` varchar(100) NOT NULL,
  `resident_id` varchar(100) NOT NULL,
  `resident_name` varchar(100) NOT NULL,
  `approved_by` varchar(100) NOT NULL,
  `activity` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `official_requests_logs`
--

INSERT INTO `official_requests_logs` (`id`, `approved_id`, `resident_id`, `resident_name`, `approved_by`, `activity`, `timestamp`) VALUES
(1, '71d3263819c61654', '71d3263819c61654', 'official account', 'official account', 'Request Barangay Clearance Approved', '2025-08-13 06:40:51'),
(2, '71d3263819c61654', '2', 'resident account', 'official account', 'Barangay Clearance Rejected', '2025-08-13 08:42:33'),
(3, '71d3263819c61654', '2', 'resident account', 'official account', 'Request Barangay Clearance Approved', '2025-08-13 08:42:37'),
(4, '71d3263819c61654', '2', 'resident account', 'official account', 'Barangay Clearance Rejected', '2025-08-13 08:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `resident_request_logs`
--

CREATE TABLE `resident_request_logs` (
  `id` int(11) NOT NULL,
  `account_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `activity_type` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident_request_logs`
--

INSERT INTO `resident_request_logs` (`id`, `account_id`, `name`, `activity`, `activity_type`, `timestamp`) VALUES
(1, '2', 'resident account', 'Submitted a Certificate of Indigency request', 'Certificate of Indigency', '2025-05-21 17:42:42'),
(2, '2', 'resident account', 'Submitted a Certificate of Indigency request', 'Certificate of Indigency', '2025-05-21 17:45:14'),
(3, '2', 'resident account', 'Submitted a Barangay Clearance request', 'Barangay Clearance', '2025-05-21 17:48:13'),
(4, '2', 'resident account', 'Submitted a Certificate of Indigency request', 'Certificate of Indigency', '2025-05-21 17:48:24'),
(5, '2', 'resident account', 'Submitted a Certificate of Indigency request', 'Certificate of Indigency', '2025-05-21 17:48:39'),
(6, '2', 'resident account', 'Submitted a Certificate of Residency request', 'Certificate of Residency', '2025-05-21 17:49:44'),
(7, '2', 'resident account', 'Submitted a Financial Assistance request', 'Financial Assistance', '2025-05-21 17:50:20'),
(8, '2', 'resident account', 'Submitted a Barangay Clearance request', 'Barangay Clearance', '2025-05-21 18:14:15'),
(9, '2', 'resident account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-05-22 04:50:42'),
(10, '2', 'resident account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-06-18 15:37:42'),
(11, '2', 'resident account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-07-24 08:51:19'),
(12, '71d3263819c61654', 'official account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-08-12 09:43:38'),
(13, '71d3263819c61654', 'official account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-08-12 09:56:30'),
(14, '71d3263819c61654', 'official account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-08-12 09:59:05'),
(15, '71d3263819c61654', 'official account', 'Requested a Certificate of Indigency', 'Certificate of Indigency', '2025-08-12 10:01:29'),
(16, '71d3263819c61654', 'official account', 'Requested a Certificate of Residency', 'Certificate of Residency', '2025-08-12 10:01:47'),
(17, '71d3263819c61654', 'official account', 'Requested a Blotter', 'Blotter', '2025-08-12 10:02:05'),
(18, '2', 'resident account', 'Requested a Barangay Clearance', 'Barangay Clearance', '2025-08-13 08:41:24');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'administrator'),
(2, 'resident'),
(3, 'official');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `user_type` (`user_type`);

--
-- Indexes for table `aid_events`
--
ALTER TABLE `aid_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `file_request`
--
ALTER TABLE `file_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `official_notifications`
--
ALTER TABLE `official_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `official_requests_logs`
--
ALTER TABLE `official_requests_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_id` (`approved_id`);

--
-- Indexes for table `resident_request_logs`
--
ALTER TABLE `resident_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aid_events`
--
ALTER TABLE `aid_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `file_request`
--
ALTER TABLE `file_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `official_notifications`
--
ALTER TABLE `official_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `official_requests_logs`
--
ALTER TABLE `official_requests_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resident_request_logs`
--
ALTER TABLE `resident_request_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_type`) REFERENCES `roles` (`id`);

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `file_request`
--
ALTER TABLE `file_request`
  ADD CONSTRAINT `file_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `official_requests_logs`
--
ALTER TABLE `official_requests_logs`
  ADD CONSTRAINT `official_requests_logs_ibfk_1` FOREIGN KEY (`approved_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `resident_request_logs`
--
ALTER TABLE `resident_request_logs`
  ADD CONSTRAINT `resident_request_logs_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
