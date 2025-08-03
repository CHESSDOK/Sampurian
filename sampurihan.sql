-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 01:38 PM
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
-- Database: `sampurihan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `animal_bite_investigation_report`
--

CREATE TABLE `animal_bite_investigation_report` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) DEFAULT NULL,
  `guardian` varchar(100) DEFAULT NULL,
  `place_where_bitten` varchar(100) DEFAULT NULL,
  `body_part_that_was_bitten` varchar(50) DEFAULT NULL,
  `wash` varchar(50) DEFAULT NULL,
  `date_bitten` date DEFAULT NULL,
  `kind_of_animal_that_bite` varchar(50) DEFAULT NULL,
  `pet_color` varchar(30) DEFAULT NULL,
  `pet_marks` varchar(50) DEFAULT NULL,
  `pet_situation` varchar(100) DEFAULT NULL,
  `registered_and_vaccinated` varchar(100) DEFAULT NULL,
  `with_other_animal` varchar(100) DEFAULT NULL,
  `health_of_the_animal` varchar(50) DEFAULT NULL,
  `name_of_the_owner` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `comment` text DEFAULT NULL,
  `gcash_ref_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangay_clearance`
--

CREATE TABLE `barangay_clearance` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) DEFAULT NULL,
  `years_stay_in_barangay` int(11) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `comment` text DEFAULT NULL,
  `gcash_ref_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_permit`
--

CREATE TABLE `business_permit` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) DEFAULT NULL,
  `kind_of_establishment` varchar(100) DEFAULT NULL,
  `nature_of_business` varchar(100) DEFAULT NULL,
  `business_registration` varchar(255) DEFAULT NULL,
  `cedula` varchar(255) DEFAULT NULL,
  `barangay_requirements` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `comment` text DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL,
  `gcash_ref_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_permit`
--

INSERT INTO `business_permit` (`id`, `permit_id`, `kind_of_establishment`, `nature_of_business`, `business_registration`, `cedula`, `barangay_requirements`, `user_id`, `status`, `comment`, `payment_type`, `gcash_ref_no`, `created_at`) VALUES
(1, 'BP-20250724-688202CE46482', 'Azure', 'Internet cafe', 'include/uploads/business_permits/business_reg_2_1753350862.pdf', 'include/uploads/business_permits/cedula_2_1753350862.pdf', 'include/uploads/business_permits/barangay_reqs_2_1753350862.pdf', 2, 'Pending', NULL, '', NULL, '2025-07-24 09:54:22'),
(2, 'BP-20250724-68821B8FBE1E5', 'Azure', 'Internet cafe', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Business_Registration_1753357199.pdf', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Cedula_1753357199.pdf', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Barangay_Requirements_1753357199.pdf', 2, 'Pending', NULL, '', NULL, '2025-07-24 11:39:59'),
(3, 'BP-20250730-6889FA0E8952E', '2342343', '43543645', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Business_Registration_1753872910.pdf', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Cedula_1753872910.pdf', 'include/uploads/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Barangay_Requirements_1753872910.pdf', 2, 'Pending', NULL, '', NULL, '2025-07-30 10:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `business_permit_renewal`
--

CREATE TABLE `business_permit_renewal` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) DEFAULT NULL,
  `name_kind_of_establishment` varchar(100) DEFAULT NULL,
  `nature_of_business` varchar(100) DEFAULT NULL,
  `business_registration` varchar(255) DEFAULT NULL,
  `cedula` varchar(255) DEFAULT NULL,
  `barangay_requirements` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `comment` text DEFAULT NULL,
  `gcash_ref_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `indigency`
--

CREATE TABLE `indigency` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) DEFAULT NULL,
  `nature_of_assistance` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `comment` text DEFAULT NULL,
  `gcash_ref_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry`
--

CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `request_id` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `f_name` varchar(50) DEFAULT NULL,
  `m_name` varchar(50) DEFAULT NULL,
  `l_name` varchar(50) DEFAULT NULL,
  `birthday` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `marriage_status` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `user_password`, `f_name`, `m_name`, `l_name`, `birthday`, `contact`, `marriage_status`, `gender`, `address`, `picture`) VALUES
(1, 'batbattmercado@gmail.com', NULL, 'Igor Knapp', 'Hayley Holmes', 'Hoopers', '2008-07-27', 'Irure alias tempora', 'Married', 'Female', 'In quaerat nihil rep', 'uploads/1752672752_474043763_2836446049861869_694324624727446876_n.jpg'),
(2, 'mercadomarklawrence55@gmail.com', '$2y$10$0N.28NWLibhODYD2IojIweU4S3b1tDZ8R483J7Dv92esDt9AiHD72', 'Phelan Woodward', 'Althea Rice', 'Claire Pollard', '2014-08-05', 'Nemo rerum quisquam', 'Married', 'Female', 'Sit voluptatem Lab', 'uploads/1752987261_400859154_1087458925605069_2717601233466235752_n.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `animal_bite_investigation_report`
--
ALTER TABLE `animal_bite_investigation_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangay_clearance`
--
ALTER TABLE `barangay_clearance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_permit`
--
ALTER TABLE `business_permit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_permit_renewal`
--
ALTER TABLE `business_permit_renewal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `indigency`
--
ALTER TABLE `indigency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal_bite_investigation_report`
--
ALTER TABLE `animal_bite_investigation_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangay_clearance`
--
ALTER TABLE `barangay_clearance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_permit`
--
ALTER TABLE `business_permit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `business_permit_renewal`
--
ALTER TABLE `business_permit_renewal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `indigency`
--
ALTER TABLE `indigency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
