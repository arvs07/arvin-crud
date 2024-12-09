-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2024 at 09:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quintinita_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','employee') DEFAULT 'employee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `profile_picture`, `role`) VALUES
(1, 'admin', '123', '../profile-picture/WIN_20231212_09_20_35_Pro.jpg', 'admin'),
(2, 'staff', '123', '../profile-picture/images.jpg', 'employee');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `size` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `amount_of_money` decimal(10,2) DEFAULT 0.00,
  `province` varchar(80) NOT NULL,
  `city` varchar(80) NOT NULL,
  `barangay` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `image`, `amount_of_money`, `province`, `city`, `barangay`) VALUES
(1, 'Shana', 'Holland', 'test@gmail.com', '$2y$10$vAJ/VvGUpYg6FNUAUd2Wi.LdqVwVigWUTBImC4Zm8dkSN0LT8L2mC', '+1 (711) 767-7858', '../profile-picture/Tsunami_by_hokusai_19th_century.jpg', 1000.00, 'Sunt ea et quo est', 'Est ipsum dolorem v', 'Reiciendis quia et r'),
(2, 'May', 'Laroco', 'joviendulay@gmail.com', '$2y$10$Wf.ViSheFH/FnrY7NC1hw.NNEBDmjaxZNSiuEOK99sDvexa/CBpf.', '09565100520', '../profile-picture/437797545_398027593001158_4513645551808076095_n.jpg', 1234.00, 'Rosario', 'La Union', 'Concepcion'),
(3, 'Berk', 'Richardson', 'customer@gmail.com', '$2y$10$BUdf9OMeQQZfasFoG93WKuYG0EFV6zKDq0Znxt7BAahIZNudZeGs6', '+1 (382) 701-8232', NULL, 500.00, 'Minus magna quia dis', 'Sunt atque et vel pa', 'Modi autem reiciendi'),
(4, 'Sawyer', 'Guthrie', 'diva@mailinator.com', '$2y$10$B312Td4pRIMxY.QIgX3oy.lJ6GzFSTVf7rIRZadQj73c3ay6A9BMa', '+1 (172) 488-7208', NULL, 0.00, 'In consequatur Poss', 'Dolorem architecto u', 'Sit exercitation et');

-- --------------------------------------------------------

--
-- Table structure for table `customer_messages`
--

CREATE TABLE `customer_messages` (
  `message_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `message_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_messages`
--

INSERT INTO `customer_messages` (`message_id`, `customer_name`, `customer_email`, `message`, `message_date`) VALUES
(4, 'Brenda Hubbard', 'fonywys@mailinator.com', 'Sunt obcaecati rerum', '2024-04-19 14:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `admin_id`) VALUES
(1, 1, '2024-05-07 06:52:59', 1581.00, NULL),
(2, 1, '2024-05-07 06:58:20', 3162.00, NULL),
(3, 1, '2024-05-07 07:09:50', 527.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Uncategorized'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `image`, `category`) VALUES
(14, 'Hyatt Henson', 'Deserunt qui molesti', 212.00, '437774064_863685458850965_435382134148443132_n.jpg', 'T-Shirt'),
(15, 'Bryar Justice', 'Nisi distinctio Ips', 527.00, '437778129_972552750888894_1881910158280281780_n.jpg', 'T-Shirt'),
(16, 'Emi Dunlap', 'A distinctio Animi', 270.00, 'b029087b4f7150b45568cc25faea4d9a.jpg', 'Pants'),
(20, 'jhomer', 'uufsyuyyd', 234.00, 'sg-11134201-22100-si82j2pj16ivf9.jpg', 'Pants');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `product_id` int(11) NOT NULL,
  `size` varchar(5) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`product_id`, `size`, `quantity`) VALUES
(14, 'S', 0),
(15, 'L', 0),
(15, 'S', 10),
(16, 'S', 10),
(20, 'S', 10);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_quantity` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `cart_id` int(11) NOT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `customer_id`, `product_id`, `request_date`, `request_quantity`, `status`, `cart_id`, `size`) VALUES
(1, 1, 1, '0000-00-00 00:00:00', 1, 'declined', 0, 'S'),
(2, 1, 5, '0000-00-00 00:00:00', 5, 'pending', 0, 'S'),
(3, 1, 5, '0000-00-00 00:00:00', 5, 'pending', 0, 'S'),
(4, 1, 16, '0000-00-00 00:00:00', 10, 'pending', 0, 'S'),
(5, 1, 14, '0000-00-00 00:00:00', 5, 'pending', 0, 'S'),
(6, 1, 15, '0000-00-00 00:00:00', 3, 'approved', 0, 'L'),
(7, 1, 15, '0000-00-00 00:00:00', 6, 'approved', 0, 'L'),
(8, 1, 15, '0000-00-00 00:00:00', 1, 'approved', 0, 'L');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cart_ibfk_1` (`customer_id`),
  ADD KEY `cart_ibfk_2` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_messages`
--
ALTER TABLE `customer_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_ibfk_1` (`customer_id`),
  ADD KEY `orders_ibfk_2` (`admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`product_id`,`size`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `requests_ibfk_1` (`customer_id`),
  ADD KEY `requests_ibfk_2` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_messages`
--
ALTER TABLE `customer_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
