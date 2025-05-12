-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 11:46 PM
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
-- Database: `navicare`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_date`, `title`, `event_time`, `created_at`, `updated_at`) VALUES
(1, '2025-05-09', 'Testing', '', '2025-05-09 13:34:40', '2025-05-09 13:34:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'Primary key, auto-incrementing user ID',
  `fullname` varchar(255) NOT NULL COMMENT 'User''s full name',
  `username` varchar(100) NOT NULL COMMENT 'Unique username for login',
  `id_number` varchar(100) NOT NULL COMMENT 'User''s ID number, kept unique',
  `email` varchar(100) NOT NULL COMMENT 'User''s email address, must be unique',
  `dob` date NOT NULL COMMENT 'Date of Birth',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `gender` enum('male','female','other','prefer_not_to_say','none') DEFAULT 'none' COMMENT 'User''s gender',
  `user_type` enum('student','faculty','admin','not_specified') DEFAULT 'not_specified' COMMENT 'Type of user (e.g., student, faculty)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp of record creation',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Timestamp of last record update'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `id_number`, `email`, `dob`, `password`, `gender`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'kiko', 'kiko', '1111', 'kiko@gmail.com', '2011-11-11', '$2y$10$dkjNQjNDKK0CAHtQn7zxJ.QO7ow0CQ6.AGh4T6GRnvUWRniiSotkS', 'male', 'student', '2025-05-12 20:21:36', '2025-05-12 21:37:03'),
(2, 'juan', 'juan', '123', 'juan@gmail.com', '2001-01-01', '$2y$10$rd/.b3OErjUzGix8TBhru.6W0qrsTf6zd5da6.WEcv2eUFIp3luAy', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(3, 'name', 'name', '123123', 'name@gmail.com', '2012-12-12', '$2y$10$DHtvtMqRRQWSldikESYX3Ounot55iWUUhBddLKYwdd3FMZd/nSR9y', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(4, 'try', 'try', '1234', 'try@gmail.com', '2012-12-12', '$2y$10$/wPBqdcKgheJ3Wfihwx2HeiTuqJhH2.JqWtFhVcTd.js1SSx6n4vK', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(5, 'test', 'test', '12341234', 'lee@gmail.com', '2006-06-06', '$2y$10$b8EyVFP/nFjutcBMR.WMA.1TxEwljU7BTLSz3RxPybbmIzcQMSgAG', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(6, 'regi', 'regi', '12344321', 'regi@gmail.com', '2012-12-12', '$2y$10$Y2cWMpulE5Vw63fDADDa5OyO.KU09rCQDRoGKxKVYTyjw6J87VvEi', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(7, 'sel', 'sel', '4321', 'sel@gmail.com', '2001-01-01', '$2y$10$P6eDNoGsN6uf8M40gF01iOsh/pNiU2XNyVpPFj/9NLbQqXeen9RQS', 'none', 'not_specified', '2025-05-12 20:21:36', '2025-05-12 20:21:36'),
(9, 'Practice', 'practice', '80808080', 'practice@wvsu.edu.ph', '2005-01-27', '$2y$10$LiQ11UjkDEHDmqs/LrXJ6er.028TGY5JhaEGPdnTVdvQW5xfudiea', 'male', 'student', '2025-05-12 21:31:10', '2025-05-12 21:31:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_date_idx` (`event_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key, auto-incrementing user ID', AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
