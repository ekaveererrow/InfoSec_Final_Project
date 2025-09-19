-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2025 at 07:46 AM
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
-- Database: `build_stock_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `material_name` varchar(50) NOT NULL,
  `buying_price` decimal(10,2) NOT NULL,
  `unit` enum('kg','pcs','liters') NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 10,
  `expiry_date` date NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `availability` enum('In-Stock','Low Stock','Out of Stock') DEFAULT 'In-Stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`material_id`, `material_name`, `buying_price`, `unit`, `stock_quantity`, `reorder_level`, `expiry_date`, `supplier_id`, `availability`) VALUES
(2, 'Red Paint', 45000.00, 'liters', 15, 8, '2025-02-23', 1, 'In-Stock'),
(3, 'Dry Cement', 100000.00, 'pcs', 10, 5, '2025-02-24', 2, 'In-Stock'),
(4, 'Kakaibang Rugby', 120.00, 'pcs', 3, 2, '2025-02-25', 3, 'In-Stock'),
(5, 'Green Paint', 30000.00, 'liters', 10, 5, '2025-03-08', 1, 'In-Stock'),
(7, 'Yellow Paint', 15000.00, 'liters', 5, 3, '2025-03-08', 1, 'In-Stock');

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `personnel_id` int(11) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(60) NOT NULL,
  `status` enum('present','absent') DEFAULT 'present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `first_name`, `last_name`, `position`, `contact`, `email`, `status`, `created_at`) VALUES
(1, 'John', 'Doe', 'Foreman', '09123456789', 'john_doe@gmail.com', 'present', '2025-02-21 17:06:37'),
(2, 'Jane', 'Smith', 'Pahinante', '09987456321', 'jane_smith@gmail.com', 'present', '2025-02-21 17:09:37'),
(3, 'Bryan', 'Pacuan', 'Kargador', '091234569', 'bryan_pacuan@gmail.com', 'present', '2025-02-21 17:13:04'),
(4, 'Christopher', 'Lim', 'CEO', '09158446699', 'master_chok@gmail.com', 'present', '2025-02-21 17:21:36'),
(5, 'John', 'Smith', 'Salingkitkit', '09358441234', 'john_smith@gmail.com', 'present', '2025-02-21 18:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(60) NOT NULL,
  `product` varchar(50) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(60) NOT NULL,
  `on_the_way` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `product`, `contact`, `email`, `on_the_way`) VALUES
(1, 'Boysen', 'Pintura', '09981234567 ', 'boysenpaint@gmail.com', 25),
(2, 'Allied Concrete', 'Hollow Blocks', '09172345678  ', 'allied_concrete@gmail.com', 90),
(3, 'Bostik', 'Rugby', '09283456789', 'bostik_ph@gmail.com', 45),
(4, 'Sample', 'Kahit Ano', '09229012345  ', 'sample@gmail.com', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','procurement') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Lebron', 'James', 'lebron23@gmail.com', 'password123', 'admin', '2025-02-21 15:07:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`personnel_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
