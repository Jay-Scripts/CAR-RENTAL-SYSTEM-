-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost: 3307
-- Generation Time: Oct 02, 2025 at 05:46 PM
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
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_payment_details`
--

CREATE TABLE `booking_payment_details` (
  `PAYMENT_DETAILS_ID` int(11) NOT NULL,
  `RECEIPT_PATH` varchar(255) NOT NULL,
  `BOOKING_ID` int(11) NOT NULL,
  `PAYMENT_TYPE` enum('BOOKING FEE','PENALTY') DEFAULT 'BOOKING FEE',
  `AMOUNT` decimal(10,2) NOT NULL DEFAULT 0.00,
  `STATUS` enum('UNPAID','PAID') DEFAULT 'UNPAID',
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_payment_details`
--

INSERT INTO `booking_payment_details` (`PAYMENT_DETAILS_ID`, `RECEIPT_PATH`, `BOOKING_ID`, `PAYMENT_TYPE`, `AMOUNT`, `STATUS`, `CREATED_AT`) VALUES
(1, '../src/images/e-receiptsFolder/receipt_1_1759416980.png', 1, 'BOOKING FEE', 0.00, 'UNPAID', '2025-10-02 22:56:20'),
(2, '../src/images/e-receiptsFolder/receipt_2_1759417146.png', 2, 'BOOKING FEE', 0.00, 'UNPAID', '2025-10-02 22:59:06'),
(3, '../src/images/e-receiptsFolder/receipt_3_1759417191.png', 3, 'BOOKING FEE', 0.00, 'PAID', '2025-10-02 22:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `booking_vehicle_inspection`
--

CREATE TABLE `booking_vehicle_inspection` (
  `INSPECTION_ID` int(11) NOT NULL,
  `BOOKING_ID` int(11) NOT NULL,
  `IMAGE_PATH` varchar(255) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NOTES` text DEFAULT NULL,
  `PENALTY` decimal(10,2) DEFAULT 0.00,
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_vehicle_inspection`
--

INSERT INTO `booking_vehicle_inspection` (`INSPECTION_ID`, `BOOKING_ID`, `IMAGE_PATH`, `USER_ID`, `NOTES`, `PENALTY`, `CREATED_AT`) VALUES
(3, 3, '../src/images/inspection_proof/inspection_3_1759419411_1.png', 3, 'no issue', 0.00, '2025-10-02 23:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `car_details`
--

CREATE TABLE `car_details` (
  `CAR_ID` int(11) NOT NULL,
  `CAR_NAME` varchar(50) NOT NULL,
  `COLOR` varchar(50) NOT NULL,
  `THUMBNAIL_PATH` varchar(255) DEFAULT NULL,
  `CAPACITY` tinyint(4) NOT NULL,
  `STATUS` enum('AVAILABLE','RESERVED','RENTED','MAINTENANCE','SALVAGE') DEFAULT 'AVAILABLE',
  `PRICE` decimal(10,2) NOT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_details`
--

INSERT INTO `car_details` (`CAR_ID`, `CAR_NAME`, `COLOR`, `THUMBNAIL_PATH`, `CAPACITY`, `STATUS`, `PRICE`, `CREATED_AT`) VALUES
(1, 'Mitsubishi XPANDER GLS 1.5 AT 2022', 'Sterling Silver Metallic', '../../src/images/carsImage/Mitsubishi XPANDER GLS 1.5 AT 2022.png', 7, 'AVAILABLE', 3000.00, '2025-10-02 22:51:08'),
(2, 'Mitsubishi XPANDER GLS 1.5 AT 2023', 'Red Metallic', '../../src/images/carsImage/Mitsubishi XPANDER GLS 1.5 AT 2023.png', 7, 'AVAILABLE', 3000.00, '2025-10-02 22:51:08'),
(3, 'INNOVA 2.8 XE 2022', 'BLACK', '../../src/images/carsImage/INNOVA 2.8 XE 2022.png', 7, 'AVAILABLE', 3000.00, '2025-10-02 22:51:08'),
(4, 'Toyota Vios XLE 2022', 'Blue Mica Metallic', '../../src/images/carsImage/Toyota Vios XLE 2022.png', 5, 'AVAILABLE', 2000.00, '2025-10-02 22:51:08'),
(5, 'Toyota Vios XE 2019', 'Alumina Jade Green', '../../src/images/carsImage/Toyota Vios XE 2019.png', 5, 'AVAILABLE', 2000.00, '2025-10-02 22:51:08'),
(6, 'Toyota Vios XE 2022', 'Pearl White', '../../src/images/carsImage/Toyota Vios XE 2022.png', 5, 'AVAILABLE', 2000.00, '2025-10-02 22:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `customer_booking_details`
--

CREATE TABLE `customer_booking_details` (
  `BOOKING_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `CAR_ID` int(11) NOT NULL,
  `PICKUP_DATE` datetime NOT NULL,
  `DROP_OFF_DATE` datetime NOT NULL,
  `TRIP_DETAILS` varchar(255) DEFAULT NULL,
  `STATUS` enum('PENDING','CANCELED','FOR VERIFICATION','FOR PICKUP','ONGOING','EXTENDED','CHECKING','COMPLETED') DEFAULT 'PENDING',
  `TOTAL_COST` decimal(10,2) NOT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_booking_details`
--

INSERT INTO `customer_booking_details` (`BOOKING_ID`, `USER_ID`, `CAR_ID`, `PICKUP_DATE`, `DROP_OFF_DATE`, `TRIP_DETAILS`, `STATUS`, `TOTAL_COST`, `CREATED_AT`) VALUES
(1, 3, 1, '2025-10-01 00:00:00', '2025-10-04 00:00:00', 'trip lang', 'CANCELED', 9000.00, '2025-10-02 22:55:34'),
(2, 3, 1, '2025-09-06 00:00:00', '2025-09-29 00:00:00', 'trip lang', 'CANCELED', 69000.00, '2025-10-02 22:58:53'),
(3, 3, 1, '2025-09-04 00:00:00', '2025-09-30 00:00:00', 'trip lang', 'COMPLETED', 78000.00, '2025-10-02 22:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `ACCOUNT_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `STATUS` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`ACCOUNT_ID`, `USER_ID`, `PASSWORD`, `STATUS`, `CREATED_AT`) VALUES
(1, 1, '$argon2id$v=19$m=65536,t=4,p=1$U0RpaUREUGxUZnFvdm43ZA$iBmbg2QfLxxMkdWYPboRyV46BgcD3iQfXrmmcsm6o48', 'ACTIVE', '2025-10-02 22:52:10'),
(2, 2, '$argon2id$v=19$m=65536,t=4,p=1$UThaZ0N2VDUuVHRzSmhwRg$/Hsf/4nbv8WZvrvQ5Abxqjlby+EylBz7KIkROj36omo', 'ACTIVE', '2025-10-02 22:52:41'),
(3, 3, '$argon2id$v=19$m=65536,t=4,p=1$VDRHQVg0TzBJT2VGa3FMOA$HlhRm8RZ9Fym/bZ2kYKM4vM66KfzOC+ITYmCZZ29pMg', 'ACTIVE', '2025-10-02 22:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `USER_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(50) NOT NULL,
  `LAST_NAME` varchar(50) NOT NULL,
  `GENDER` enum('MALE','FEMALE') NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PHONE` varchar(20) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL,
  `ROLE` enum('customer','admin','rental-agent') DEFAULT 'customer',
  `CREATED_AT` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`USER_ID`, `FIRST_NAME`, `LAST_NAME`, `GENDER`, `EMAIL`, `PHONE`, `ADDRESS`, `ROLE`, `CREATED_AT`) VALUES
(1, 'CornelioCust', 'Gatbonton', 'MALE', 'cutomer@gmail.com', '1111-111-1111', 'dito lang', 'customer', '2025-10-02 22:52:10'),
(2, 'CornelioADMIN', 'Gatbonton', 'MALE', 'admin@gmail.com', '3333-333-3333', 'janlang', 'admin', '2025-10-02 22:52:40'),
(3, 'CornelioAGENT', 'Gatbonton', 'MALE', 'rentalagent@gmail.com', '6666-666-6666', 'doon', 'rental-agent', '2025-10-02 22:53:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_payment_details`
--
ALTER TABLE `booking_payment_details`
  ADD PRIMARY KEY (`PAYMENT_DETAILS_ID`),
  ADD KEY `BOOKING_ID` (`BOOKING_ID`);

--
-- Indexes for table `booking_vehicle_inspection`
--
ALTER TABLE `booking_vehicle_inspection`
  ADD PRIMARY KEY (`INSPECTION_ID`),
  ADD KEY `BOOKING_ID` (`BOOKING_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `car_details`
--
ALTER TABLE `car_details`
  ADD PRIMARY KEY (`CAR_ID`);

--
-- Indexes for table `customer_booking_details`
--
ALTER TABLE `customer_booking_details`
  ADD PRIMARY KEY (`BOOKING_ID`),
  ADD KEY `USER_ID` (`USER_ID`),
  ADD KEY `CAR_ID` (`CAR_ID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`ACCOUNT_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
  ADD UNIQUE KEY `PHONE` (`PHONE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_payment_details`
--
ALTER TABLE `booking_payment_details`
  MODIFY `PAYMENT_DETAILS_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `booking_vehicle_inspection`
--
ALTER TABLE `booking_vehicle_inspection`
  MODIFY `INSPECTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `car_details`
--
ALTER TABLE `car_details`
  MODIFY `CAR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_booking_details`
--
ALTER TABLE `customer_booking_details`
  MODIFY `BOOKING_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `ACCOUNT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_payment_details`
--
ALTER TABLE `booking_payment_details`
  ADD CONSTRAINT `booking_payment_details_ibfk_1` FOREIGN KEY (`BOOKING_ID`) REFERENCES `customer_booking_details` (`BOOKING_ID`);

--
-- Constraints for table `booking_vehicle_inspection`
--
ALTER TABLE `booking_vehicle_inspection`
  ADD CONSTRAINT `booking_vehicle_inspection_ibfk_1` FOREIGN KEY (`BOOKING_ID`) REFERENCES `customer_booking_details` (`BOOKING_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_vehicle_inspection_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `user_details` (`USER_ID`) ON DELETE CASCADE;

--
-- Constraints for table `customer_booking_details`
--
ALTER TABLE `customer_booking_details`
  ADD CONSTRAINT `customer_booking_details_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user_details` (`USER_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_booking_details_ibfk_2` FOREIGN KEY (`CAR_ID`) REFERENCES `car_details` (`CAR_ID`) ON DELETE CASCADE;

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user_details` (`USER_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
