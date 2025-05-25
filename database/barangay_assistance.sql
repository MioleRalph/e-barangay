-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2025 at 04:23 AM
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

INSERT INTO `accounts` (`account_id`, `profile_pic`, `first_name`, `last_name`, `email`, `password`, `user_type`, `verification_token`, `verification_status`, `date_registered`) VALUES
('1', 'img_681625582ec592.27101077.jpg', 'Administrator', 'Account', 'admin@gmail.com', '$2y$10$ZbnrnMftz3MrI4tj9htW1OMxUczt5n7aHvmpl8uPCLsc9zRh/scUu', 1, '', 'verified', '2025-04-15 11:16:18'),
('2', 'img_6813ac355893b7.11210402.jpeg', 'resident', 'account', 'resident@gmail.com', '$2y$10$76YYpG2a6bG0ma5pCUV1qeLhGzvRe4SKlfChiuGF4BTl/q/4Xhjju', 2, '', 'verified', '2025-04-15 11:16:18'),
('4e7a8c3e8258b11a', 'profile_6816537b6685e5.37694532.jpg', 'as', 'as', 'as@gmail.com', '$2y$10$gYpqyuGp1qOASnxsWpoSyexf0BJEKgPHRDVieF9l6w3lhMR/dTToi', 3, 'ea477051a911afad66f9fa1015cde240', 'not verified', '2025-05-03 17:33:47'),
('71d3263819c61654', 'img_6813ac5fc2fe77.25199207.jpeg', 'official', 'account', 'official@gmail.com', '$2y$10$qOPdSaxJvFeGPmULBI/fD.uqHj5qN0OuXKn4EtcfPJ4eXVIkKnv0e', 3, '24ac44c77473fb8a3d5361361530f340', 'verified', '2025-04-25 06:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `aid_distribution_logs`
--

CREATE TABLE `aid_distribution_logs` (
  `id` int(20) NOT NULL,
  `aid_request_id` varchar(100) NOT NULL,
  `official_id` varchar(100) NOT NULL,
  `date_distributed` date NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `aid_requests`
--

CREATE TABLE `aid_requests` (
  `id` int(11) NOT NULL,
  `beneficiary_id` varchar(100) NOT NULL,
  `beneficiary_name` varchar(100) NOT NULL,
  `aid_type` varchar(100) NOT NULL,
  `request_reason` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `amount_requested` int(30) NOT NULL,
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_approved` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aid_requests`
--

INSERT INTO `aid_requests` (`id`, `beneficiary_id`, `beneficiary_name`, `aid_type`, `request_reason`, `status`, `amount_requested`, `date_requested`, `date_approved`) VALUES
(2, 'a11', 'user account', 'asas', 'asas', 'approved', 1111, '2025-05-04 18:09:10', '2025-05-04 18:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `aid_requests_logs`
--

CREATE TABLE `aid_requests_logs` (
  `id` int(20) NOT NULL,
  `approved_id` varchar(100) NOT NULL,
  `beneficiary_id` varchar(100) NOT NULL,
  `beneficiary_name` varchar(100) NOT NULL,
  `approved_by` varchar(100) NOT NULL,
  `activity` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aid_requests_logs`
--

INSERT INTO `aid_requests_logs` (`id`, `approved_id`, `beneficiary_id`, `beneficiary_name`, `approved_by`, `activity`, `timestamp`) VALUES
(1, '1', 'a11', 'user account', 'Administrator Account', 'Approved aid request ID: 2', '2025-05-04 18:05:00'),
(2, '71d3263819c61654', 'a11', 'user account', 'official account', 'Approved aid request ID: 2', '2025-05-04 18:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(20) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `contact_number` int(20) NOT NULL,
  `valid_id_number` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(100, '1', 'Administrator', 'admin@gmail.com', 'Approved aid request ID: 2', 'admin', '2025-05-04 17:37:07'),
(101, '1', 'Administrator Account', 'admin@gmail.com', 'login', '1', '2025-05-04 17:57:06'),
(102, '1', 'Administrator Account', 'admin@gmail.com', 'Logout', '1', '2025-05-04 18:08:57'),
(103, '71d3263819c61654', 'official account', 'official@gmail.com', 'login', '3', '2025-05-04 18:09:06');

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
-- Indexes for table `aid_distribution_logs`
--
ALTER TABLE `aid_distribution_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aid_requests`
--
ALTER TABLE `aid_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aid_requests_logs`
--
ALTER TABLE `aid_requests_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_id` (`approved_id`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aid_distribution_logs`
--
ALTER TABLE `aid_distribution_logs`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `aid_requests`
--
ALTER TABLE `aid_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `aid_requests_logs`
--
ALTER TABLE `aid_requests_logs`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

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
-- Constraints for table `aid_requests_logs`
--
ALTER TABLE `aid_requests_logs`
  ADD CONSTRAINT `aid_requests_logs_ibfk_1` FOREIGN KEY (`approved_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
