-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 10:44 AM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`fullname`, `username`, `id_number`, `email`, `dob`, `password`) VALUES
('juan', 'juan', '123', 'juan@gmail.com', '2001-01-01', '$2y$10$rd/.b3OErjUzGix8TBhru.6W0qrsTf6zd5da6.WEcv2eUFIp3luAy'),
('try', 'try', '1234', 'try@gmail.com', '2012-12-12', '$2y$10$/wPBqdcKgheJ3Wfihwx2HeiTuqJhH2.JqWtFhVcTd.js1SSx6n4vK'),
('test', 'test', '12341234', 'lee@gmail.com', '2006-06-06', '$2y$10$b8EyVFP/nFjutcBMR.WMA.1TxEwljU7BTLSz3RxPybbmIzcQMSgAG'),
('regi', 'regi', '12344321', 'regi@gmail.com', '2012-12-12', '$2y$10$Y2cWMpulE5Vw63fDADDa5OyO.KU09rCQDRoGKxKVYTyjw6J87VvEi'),
('sel', 'sel', '4321', 'sel@gmail.com', '2001-01-01', '$2y$10$P6eDNoGsN6uf8M40gF01iOsh/pNiU2XNyVpPFj/9NLbQqXeen9RQS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
