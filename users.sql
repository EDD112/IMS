-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 11:07 AM
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
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `student_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/default.png',
  `sidebar_profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`student_id`, `firstname`, `lastname`, `username`, `password`, `profile_picture`, `sidebar_profile_picture`) VALUES
(1234567, 'Edwin', 'Argallon', 'Edwin1', '$2y$10$sV81/ZB8T6J5d/5i4Qiqa.NTMA1FE3J93.6GKiBaKTtyM/u82fcNe', 'uploads/default.png', NULL),
(123456789, 'Argie', 'Apostol', 'argie1', '$2y$10$H0fI9A5GPBk8y2zpRVVqBuinWtEHtFzFgaX9lz.a0.Oeo4vdU70HW', 'uploads/1743181124_1743073452_1742785309_pfp.jpg', 'uploads/1743182679_1742647558_pfp.jpg'),
(2147483647, 'James', 'Amad', 'amad123', '$2y$10$v/DLIrFpMnzk.fFUs2/kVuN36KUi4MZj/eU4zktbwZqJ6l5Ikz7Uu', 'uploads/1743182697_1743061154_logo.png', 'uploads/1743182690_1742647702_cec.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
