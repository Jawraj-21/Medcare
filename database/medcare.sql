-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2024 at 05:11 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `doctor_id`, `department_id`, `date`, `time`) VALUES
(2, 20, 1, 1, '2024-02-15', '12:00:00'),
(3, 20, 1, 1, '2024-03-07', '15:00:00'),
(4, 20, 1, 1, '2024-02-16', '11:30:00'),
(7, 20, 1, 1, '2024-02-16', '11:00:00'),
(8, 20, 1, 1, '2024-02-17', '09:00:00'),
(9, 20, 1, 1, '2024-02-20', '09:00:00'),
(12, 20, 1, 1, '2024-02-22', '11:00:00'),
(13, 20, 1, 1, '2024-02-20', '09:00:00'),
(14, 20, 1, 1, '2024-02-20', '09:00:00'),
(15, 20, 1, 1, '2024-02-16', '11:00:00'),
(16, 20, 1, 1, '2024-02-16', '12:30:00'),
(17, 21, 1, 1, '2024-02-14', '14:00:00'),
(19, 16, 1, 1, '2024-02-22', '09:00:00'),
(21, 16, 1, 1, '2024-02-22', '12:00:00'),
(23, 16, 1, 1, '2024-02-23', '15:30:00'),
(24, 16, 1, 1, '2024-02-23', '11:30:00'),
(29, 16, 1, 1, '2024-03-05', '09:00:00'),
(30, 16, 1, 1, '2024-03-05', '09:30:00'),
(31, 16, 1, 1, '2024-03-06', '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` char(255) NOT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `opening_time`, `closing_time`) VALUES
(1, 'Cardiology', '09:00:00', '17:00:00'),
(2, 'Orthopedics', '09:00:00', '17:00:00'),
(3, 'Phlebotomy', '07:00:00', '17:00:00'),
(4, 'Consultation Services', '09:00:00', '16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `DOB` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `department_id`, `first_name`, `last_name`, `gender`, `DOB`, `address`, `phone_number`) VALUES
(1, 15, 1, 'doctor1', 'doctor', 'Female', '2001-01-11', 'doctor 23', 758584524);

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `grams` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `for` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`medicine_id`, `name`, `grams`, `description`, `for`) VALUES
(1, 'Paracetamol', 500, 'Painkiller for aches and pain.', 'Pain'),
(2, 'Ibuprofen', 400, 'Pain reliever and fever reducer.', 'Inflammation'),
(3, 'Aspirin', 300, 'Pain relief, fever reduction, and anti-inflammatory.', 'Clots'),
(4, 'Amoxicillin', 250, 'Antibiotic for bacterial infections.', 'Infections'),
(5, 'Omeprazole', 20, 'Reduces stomach acid for GERD and ulcers.', 'Acid'),
(6, 'Perindopril', 4, 'Lowers blood pressure and treats heart conditions.', 'Blood Pressure'),
(7, 'Lansoprazole', 30, 'Reduces stomach acid for heartburn and ulcers.', 'Acid'),
(8, 'Amlodipine', 5, 'Lowers blood pressure and treats angina.', 'Blood Pressure'),
(9, 'Atorvastatin', 100, 'Lowers cholesterol to reduce heart disease risk.', 'Cholesterol'),
(10, 'Metformin', 850, 'Lowers blood sugar levels for type 2 diabetes.', 'Diabetes');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `DOB` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `user_id`, `first_name`, `last_name`, `gender`, `DOB`, `address`, `phone_number`) VALUES
(1, 14, 'user1', 'user', 'Male', '2024-02-01', 'user 23', '07535542544'),
(2, 20, 'Jack', 'Jonson', 'Male', '2024-02-07', '21 GT road', '07535542542'),
(3, 21, 'Jawraj', 'Singh', 'Male', '2001-08-21', '47 Sussex Avenue', '02147483647'),
(4, 16, 'Alex', 'Smith', 'Male', '2001-11-27', '21 High Street, Birmingham', '07565822645'),
(5, 22, 'Stefano', 'Rossi', 'Male', '2024-03-02', '83 High Street', '0753554254');

-- --------------------------------------------------------

--
-- Table structure for table `patient_reports`
--

CREATE TABLE `patient_reports` (
  `report_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `report_type` varchar(255) NOT NULL,
  `blood_pressure` text DEFAULT NULL,
  `heart_rate` text DEFAULT NULL,
  `lipid_profile` text DEFAULT NULL,
  `liver_function_tests` text DEFAULT NULL,
  `serum_albumin` text DEFAULT NULL,
  `albium_low_range` text NOT NULL DEFAULT '35 g/L',
  `albium_high_range` text NOT NULL DEFAULT '50 g/L',
  `serum_bilirubin` text DEFAULT NULL,
  `bilirubin_low_range` text NOT NULL DEFAULT '0',
  `bilirubin_high_range` text NOT NULL DEFAULT '21 umol/L',
  `serum_alakaline_phosphatase` text NOT NULL,
  `alkaline_phosphatase_low_range` text NOT NULL DEFAULT '20.0 U/L',
  `alkaline_phosphatase_high_range` text NOT NULL DEFAULT '130.0 U/L',
  `serum_alanine` text DEFAULT NULL,
  `alanine_low_range` text NOT NULL DEFAULT '0 U/L',
  `alanine_high_range` text NOT NULL DEFAULT '41 U/L',
  `kidney_function_tests` text DEFAULT NULL,
  `thyroid_function_tests` text DEFAULT NULL,
  `diabetes_status` enum('Normal','Pre-Diabetic','Diabetic') DEFAULT NULL,
  `vitamin_d_level` text DEFAULT NULL,
  `vitamin_b12_level` text DEFAULT NULL,
  `b12_low_range` text NOT NULL DEFAULT '187.0 ng/L',
  `b12_high_range` text NOT NULL DEFAULT '883.0',
  `serum_cholesterol` text DEFAULT NULL,
  `cholesterol_low_range` text NOT NULL DEFAULT '2.5 mmol/L',
  `cholesterol_high_range` text NOT NULL DEFAULT '5.0 mmol/L',
  `serum_sodium` text DEFAULT NULL,
  `sodium_low_range` text NOT NULL DEFAULT '133.0 mmol/L',
  `sodium_high_range` text NOT NULL DEFAULT '146.0 mmol/L',
  `serum_potassium` text DEFAULT NULL,
  `potassium_low_range` text NOT NULL DEFAULT '3.5 mmol/L',
  `potassium_high_range` text NOT NULL DEFAULT '5.3 mmol/L'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient_reports`
--

INSERT INTO `patient_reports` (`report_id`, `patient_id`, `doctor_id`, `report_date`, `report_type`, `blood_pressure`, `heart_rate`, `lipid_profile`, `liver_function_tests`, `serum_albumin`, `albium_low_range`, `albium_high_range`, `serum_bilirubin`, `bilirubin_low_range`, `bilirubin_high_range`, `serum_alakaline_phosphatase`, `alkaline_phosphatase_low_range`, `alkaline_phosphatase_high_range`, `serum_alanine`, `alanine_low_range`, `alanine_high_range`, `kidney_function_tests`, `thyroid_function_tests`, `diabetes_status`, `vitamin_d_level`, `vitamin_b12_level`, `b12_low_range`, `b12_high_range`, `serum_cholesterol`, `cholesterol_low_range`, `cholesterol_high_range`, `serum_sodium`, `sodium_low_range`, `sodium_high_range`, `serum_potassium`, `potassium_low_range`, `potassium_high_range`) VALUES
(1, 4, 1, '2024-02-18', 'Blood Test', '110 mmHg', '90 bpm', 'Normal', '70 IU/L', '45 g/L', '35 g/L', '50 g/L', '8 umol/L', '0', '21 umol/L', '117 U/L', '20 U/L', '130.0 U/L', '35 U/L', '0 U/L', '41 U/L', '85', '2.6 mIU/L', 'Normal', '163 nmol/L', '352 ng/L', '187.0 ng/L', '883.0', '4 mmol/L', '2.5 mmol/L', '5.0 mmol/L', '140 mmol/L', '133.0 mmol/L', '146.0 mmol/L', '4 mmol/L', '3.5 mmol/L', '5.3 mmol/L');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` char(50) NOT NULL,
  `last_name` char(50) NOT NULL,
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL,
  `role` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `security_question`, `security_answer`, `role`) VALUES
(14, 'user1', '$2y$10$Kf8bmJ5XN0bGIp6r1xzVG./GMPanzAlYg0wUTeZAQCxLTAly1aVk2', 'user@gmail.com', 'user1', 'user', '', '', 'Patient'),
(15, 'doctor1', '$2y$10$X09PMBandezCPCdJeFwpB.M71pt3c90Py7p1R0bnli/g9GK87v4CW', 'doctor@gmail.com', 'doctor1', 'doctor', '', '', 'Doctor'),
(16, 'alex01', '$2y$10$5sTWRfRoncsg8rr.c8Ebm.HQm1cXYzOL7NtW9SxSTiWL53A09rV7a', 'alex01@gmail.com', 'Alex', 'Johnson', '', '', 'Patient'),
(20, 'jack', '$2y$10$2SUb/Ni/zd0XDwlBNpFeR.XFGxEe6qScdJUUUfotVfRhKSvfNf4b.', 'jack@gmail.com', 'Jack', 'Jonson', '', '', 'Patient'),
(21, 'jawraj21', '$2y$10$xss.lK7GK2/SXgJ9bLBM2.bAWGZVHtpJFGTXZWhmOkCiU452ATz4u', 'jawrajsingh2001@gmail.com', 'Jawraj', 'Singh', '', '', 'Patient'),
(22, 'stefano1', '$2y$10$M.qACTgFhSNwhw3yqZ08ReN9/B/VmoiNLQWL7TRc7Ri9RKwScbetC', 'stefano@gmail.com', 'Stefano', 'Rossi', '', '', 'Patient');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patient_reports`
--
ALTER TABLE `patient_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD CONSTRAINT `patient_reports_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `patient_reports_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
