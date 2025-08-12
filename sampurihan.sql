-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2025 at 03:17 PM
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

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `admin_password`, `first_name`, `middle_name`, `last_name`, `contact`) VALUES
(1, 'mark@gmail.com', '$2y$10$MIhlw9H7NdLgkz5V5l9Gw.ykBfUR3ogOxCOoJm1P8.4zCYfgH/a8q', 'Maile', 'Karen Dunn', 'Valdez', '09124645');

-- --------------------------------------------------------

--
-- Table structure for table `animal_bite_reports`
--

CREATE TABLE `animal_bite_reports` (
  `id` int(11) NOT NULL,
  `permit_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,
  `age` int(3) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `contact` varchar(20) NOT NULL,
  `guardian` varchar(100) DEFAULT NULL,
  `bite_location` text NOT NULL,
  `body_part` text NOT NULL,
  `washed` enum('Oo','Hindi') NOT NULL,
  `bite_date` date NOT NULL,
  `animal_description` text NOT NULL,
  `color` varchar(50) NOT NULL,
  `marks` text DEFAULT NULL,
  `animal_condition` text DEFAULT NULL COMMENT 'JSON array of conditions',
  `registered` enum('Oo','Hindi') NOT NULL,
  `other_animals` enum('Meron','Wala') NOT NULL,
  `dog_condition` enum('Malusog','Bagong panganak','May sakit') NOT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `payment_method` enum('Cash','GCash') NOT NULL,
  `gcash_ref_no` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animal_bite_reports`
--

INSERT INTO `animal_bite_reports` (`id`, `permit_id`, `user_id`, `last_name`, `first_name`, `middle_name`, `dob`, `age`, `gender`, `contact`, `guardian`, `bite_location`, `body_part`, `washed`, `bite_date`, `animal_description`, `color`, `marks`, `animal_condition`, `registered`, `other_animals`, `dog_condition`, `owner_name`, `payment_method`, `gcash_ref_no`, `payment_proof`, `status`, `created_at`, `updated_at`, `comment`) VALUES
(1, 'ABR-20250804-59274A708FC9', 2, 'Lester', 'Serena', 'Zephania Reyes', '1981-02-15', 35, 'Female', 'Debitis quo quidem e', 'Tempore rerum et te', 'Consequatur Nihil a', 'Pariatur Incididunt', 'Hindi', '2008-10-09', 'Rerum omnis magnam q', 'Rerum qui eos eu et ', 'Ipsum ratione moles', '[\"Nakakulong\",\"Nakatali\",\"Gala\"]', 'Hindi', 'Wala', 'Bagong panganak', 'Carl Monroe', 'Cash', '', NULL, 'Pending', '2025-08-04 19:58:07', NULL, NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_clearance`
--

INSERT INTO `barangay_clearance` (`id`, `permit_id`, `years_stay_in_barangay`, `purpose`, `attachment`, `user_id`, `status`, `comment`, `gcash_ref_no`, `created_at`, `payment_proof`, `payment_type`) VALUES
(2, 'BP-20250804-244A76ED1A4F', 23, 'Job application', 'documents/Claire Pollard, Phelan Woodward Althea Rice/clearance/2x2_1754304016.jpg', 2, 'Pending', NULL, '', '2025-08-04 10:40:16', '', 'Cash');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_permit`
--

INSERT INTO `business_permit` (`id`, `permit_id`, `kind_of_establishment`, `nature_of_business`, `business_registration`, `cedula`, `barangay_requirements`, `user_id`, `status`, `comment`, `payment_type`, `gcash_ref_no`, `created_at`, `payment_proof`) VALUES
(6, 'BP-20250803-633203CE016B', 'NextV', 'Internet cafe', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Business_Registration_1754196685.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Cedula_1754196685.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Business Permit/Barangay_Requirements_1754196685.pdf', 2, 'Pending', NULL, 'Cash', '', '2025-08-03 04:51:25', '');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `business_payment` varchar(255) NOT NULL,
  `business_permit` varchar(255) NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_permit_renewal`
--

INSERT INTO `business_permit_renewal` (`id`, `permit_id`, `name_kind_of_establishment`, `nature_of_business`, `business_registration`, `cedula`, `barangay_requirements`, `user_id`, `status`, `comment`, `gcash_ref_no`, `created_at`, `business_payment`, `business_permit`, `payment_proof`, `payment_type`) VALUES
(2, 'BP-20250804-5F280A97B408', '2342343', '43543645', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Registration_1754299435.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Cedula_1754299435.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Barangay_Requirements_1754299435.pdf', 2, 'Pending', NULL, '', '2025-08-04 09:23:55', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/business_Permit_payment_1754299435.jpg', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Permit_1754299435.pdf', '', 'GCash'),
(3, 'BP-20250804-C83D275A789A', '123', '345', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Registration_1754299891.jpg', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Cedula_1754299891.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Barangay_Requirements_1754299891.jpg', 2, 'Pending', NULL, '123456', '2025-08-04 09:31:31', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/business_Permit_payment_1754299891.jpg', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Permit_1754299891.jpg', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Payment_Proof_1754299891.pdf', 'GCash'),
(4, 'BP-20250804-2CC3877CC78A', 'Brady Livingston', 'Consequatur ut dolor', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Registration_1754300419.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Cedula_1754300419.jpg', '', 2, 'Pending', NULL, '', '2025-08-04 09:40:19', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/business_Permit_payment_1754300419.pdf', 'documents/Claire Pollard, Phelan Woodward Althea Rice/Renew Business Permit/Business_Permit_1754300419.pdf', '', 'Cash');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_type` varchar(255) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `indigency`
--

INSERT INTO `indigency` (`id`, `permit_id`, `nature_of_assistance`, `user_id`, `status`, `comment`, `gcash_ref_no`, `created_at`, `payment_type`, `payment_proof`) VALUES
(1, 'BP-20250804-5FDCAACE9F67', 'Job application', 2, 'Declined', 'wrong', '', '2025-08-04 11:27:13', 'Cash', ''),
(2, 'BP-20250812-32571EEC63A1', 'Job application', 2, 'Approved', NULL, '', '2025-08-12 11:41:46', 'Cash', ''),
(3, 'BP-20250812-F88DA04074ED', 'Job application', 2, 'Approved', NULL, '123456', '2025-08-12 11:59:53', 'GCash', 'documents/Claire Pollard, Phelan Woodward Althea Rice/clearance/Payment_Proof_1754999993.jpg');

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

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `request_id`, `message`, `is_read`, `created_at`) VALUES
(1, '2', 'BP-20250731-1CE832F96F46', 'Your business permit application (ID: BP-20250731-1CE832F96F46) has been submitted and is pending review.', 0, '2025-07-31 11:42:14'),
(2, '2', 'BP-20250731-3F07224176D3', 'Your business permit application (ID: BP-20250731-3F07224176D3) has been submitted and is pending review.', 0, '2025-07-31 11:44:40'),
(3, '2', 'BP-20250803-633203CE016B', 'Your business permit application (ID: BP-20250803-633203CE016B) has been submitted successfully.', 0, '2025-08-03 04:51:25'),
(4, '2', 'BP-20250803-B82981E3C6A0', 'Your business permit application (ID: BP-20250803-B82981E3C6A0) has been submitted successfully.', 0, '2025-08-03 12:07:55'),
(5, '2', 'BP-20250804-5F280A97B408', 'Your business permit application (ID: BP-20250804-5F280A97B408) has been submitted successfully.', 0, '2025-08-04 09:23:56'),
(6, '2', 'BP-20250804-C83D275A789A', 'Your business permit application (ID: BP-20250804-C83D275A789A) has been submitted successfully.', 0, '2025-08-04 09:31:31'),
(7, '2', 'BP-20250804-2CC3877CC78A', 'Your business permit application (ID: BP-20250804-2CC3877CC78A) has been submitted successfully.', 0, '2025-08-04 09:40:19'),
(8, '2', 'BP-20250804-360DEA664DDF', 'Your barangay clearance application (ID: BP-20250804-360DEA664DDF) has been submitted successfully.', 0, '2025-08-04 10:38:23'),
(9, '2', 'BP-20250804-244A76ED1A4F', 'Your barangay clearance application (ID: BP-20250804-244A76ED1A4F) has been submitted successfully.', 0, '2025-08-04 10:40:16'),
(10, '2', 'BP-20250804-5FDCAACE9F67', 'Your barangay clearance application (ID: BP-20250804-5FDCAACE9F67) has been submitted successfully.', 0, '2025-08-04 11:27:13'),
(11, '2', 'ABR-20250804-59274A708FC9', 'Your animal bite report (ID: ABR-20250804-59274A708FC9) has been submitted successfully.', 0, '2025-08-04 11:58:08'),
(12, '2', 'BP-20250812-32571EEC63A1', 'Your barangay indigency application (ID: BP-20250812-32571EEC63A1) has been submitted successfully.', 0, '2025-08-12 11:41:46'),
(13, '2', 'BP-20250812-F88DA04074ED', 'Your barangay indigency application (ID: BP-20250812-F88DA04074ED) has been submitted successfully.', 0, '2025-08-12 11:59:53');

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
(2, 'mercadomarklawrence55@gmail.com', '$2y$10$0N.28NWLibhODYD2IojIweU4S3b1tDZ8R483J7Dv92esDt9AiHD72', 'Phelan Woodward', 'Althea Rice', 'Claire Pollard', '2014-08-05', 'Nemo rerum quisquam', 'Married', 'Female', 'Sit voluptatem Lab', 'uploads/1752987261_400859154_1087458925605069_2717601233466235752_n.jpg'),
(3, 'azure@gmail.com', '$2y$10$/Bgm/xWZO7mYATRHzXoLzuzdjVru4t3iqB5fypFP5SbN7NwhXXB6i', 'Skyler Brooks', 'Jameson Joseph', 'Rudyard Shepherd', '2019-12-26', 'Totam nostrud natus', 'Single', 'Female', 'Cumque rerum accusam', 'uploads/1754998393_susan-q-yin-2JIvboGLeho-unsplash.jpg');

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
-- Indexes for table `animal_bite_reports`
--
ALTER TABLE `animal_bite_reports`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `animal_bite_reports`
--
ALTER TABLE `animal_bite_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `barangay_clearance`
--
ALTER TABLE `barangay_clearance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `business_permit`
--
ALTER TABLE `business_permit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `business_permit_renewal`
--
ALTER TABLE `business_permit_renewal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `indigency`
--
ALTER TABLE `indigency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
