-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 09:00 AM
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
-- Database: `raflora_enterprises`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts_tbl`
--

CREATE TABLE `accounts_tbl` (
  `user_id` bigint(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_number` bigint(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `status` enum('active','pending_deletion','deactivated') DEFAULT 'active',
  `deletion_requested_at` datetime DEFAULT NULL,
  `deactivation_date` datetime DEFAULT NULL,
  `recovery_token` varchar(100) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts_tbl`
--

INSERT INTO `accounts_tbl` (`user_id`, `first_name`, `last_name`, `user_name`, `password`, `address`, `email`, `mobile_number`, `role`, `profile_picture`, `status`, `deletion_requested_at`, `deactivation_date`, `recovery_token`, `token_expires_at`) VALUES
(4, '', '', 'admin_user', '$2y$10$rplwzyOazn8xVHdnyKQZUO.p79ZPzAnxEr.jbv.c2QxEtairNlzuS', '', 'rafloraenterprises14@gmail.com', 0, 'admin_type', '', 'active', NULL, NULL, NULL, NULL),
(5, 'Justine', 'Salido', 'justinesalido22', '$2y$10$zHJ9FzHTkjJi2u5KaLpVM.ENgJWB6lDUXmPJSoX3SDgrMyCaiYXRO', 'blk 11 lot 6 kawal caloocan', 'justinemedice17@gmail.com', 9668663989, 'client_type', 'uploads/profile_pictures/profile_5_1760734232.jpg', 'active', NULL, NULL, NULL, NULL),
(7, 'Lismer', 'Palce', 'lismernadonza24', '$2y$10$5wLE1nWUlQn3KLGfrCuZq.CyZ9XaKEVzIq2CbHJJpoj.Q8Lr1xKuS', '4224 Blk 69  Caloocan City', 'lismerjohnnadonza@gmail.com', 9773436195, 'client_type', 'uploads/profile_pictures/profile_7_1761043833.jpg', 'active', NULL, NULL, NULL, NULL),
(10, 'John Michael', 'Java', 'jmjava19', '$2y$10$Z9RWgF2vxHtt8ZUX9N4gJOBT7hqpDeBMoiDB38f/Vp7a.2RTSk4Ca', '4224 blk Caloocan City', 'jmjava@gmail.com', 9232323232, 'client_type', 'uploads/profile_pictures/profile_10_1760705438.png', 'active', NULL, NULL, NULL, NULL),
(11, 'Lismer', 'Palce', 'lismerpalce24', '$2y$10$h8Bhl1OoI5JFxJsy2Y56zuRGHUN00ml7a81B0t5WKBAcnJn.NwgH.', '4224 Blk 69 Caloocan City', 'lismerpalce09@gmail.com', 9952796654, 'client_type', 'uploads/profile_pictures/profile_11_1760734572.jpg', 'active', NULL, NULL, NULL, NULL),
(12, 'Lenard', 'Palce', 'lenardpalce09', '$2y$10$uQ7zB0iO352XoclrIdJ3seqDqliRK2z/GseUE4Ec6dEaAzSDc2SYC', 'Bagong silang Novaliches', 'lenardpalce@gmail.com', 9881113377, 'client_type', '', 'active', NULL, NULL, NULL, NULL),
(13, 'Gerald', 'Palce', 'geraldpalce19', '$2y$10$GfZPdpcKQT/yWokyJTOyiO6c4Jfn6zrbfZZOrhx0CaH04jQtID9na', 'Caloocan City', 'geraldpalce19@gmail.com', 9778919384, 'client_type', '', 'active', NULL, NULL, NULL, NULL),
(14, 'Justine', 'Salido', 'Justine Salido', '$2y$10$LjhZOViJPYBaVpYbMVsqv.ZmO0DTC5r3w5JhlzCzBftZY5HLT7XpC', 'blk 11 lot 6 kawal caloocan', 'justinemedice18@gmail.com', 9668662989, 'client_type', '', 'active', NULL, NULL, NULL, NULL),
(15, 'Test', 'account', 'test_account', '$2y$10$pqpVxV4mZGp0QIPwqyQh..uhVA9wIBV9wbBuxwTys22xBB9VFTGKu', 'Test st. test Test', 'lismer69@gmail.com', 9773436195, 'client_type', 'uploads/profile_pictures/profile_15_1761114641.jpg', 'active', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_tbl`
--
ALTER TABLE `accounts_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts_tbl`
--
ALTER TABLE `accounts_tbl`
  MODIFY `user_id` bigint(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
