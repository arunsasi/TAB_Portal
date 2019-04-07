-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 07, 2019 at 09:23 PM
-- Server version: 5.7.25-0ubuntu0.18.10.2
-- PHP Version: 7.2.15-0ubuntu0.18.10.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `natana_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 1, '2019-03-30 00:00:00', '2019-03-30 16:20:45'),
(2, 'Coodinator', 1, '2019-03-30 00:00:00', '2019-03-30 16:20:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(250) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `organization` int(3) NOT NULL,
  `role_id` int(3) NOT NULL,
  `email_verification_code` varchar(100) DEFAULT NULL,
  `status` int(2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `phone_no`, `name`, `organization`, `role_id`, `email_verification_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'chinschips@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '35345436363', 'sgthtr', 1, 1, '3c51ee6a70878531ab7ae6040fc8f884', 2, '2019-03-24 18:40:09', '2019-03-24 13:10:09'),
(2, 'chinschips1@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '35345436363', 'sgthtr', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(3, 'rreyrty@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '35345436363', 'rtyttt', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(4, 'nnnnnn@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '35345436363', 'gnnnhgn222446558678', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(5, 'ngggggg@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '35345436363', 'tttt', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(6, 'gggggggg@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '35345436363', 'rreyrty@gmail.com', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(7, 'wwww@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '47567567', 'wwwwwwww', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(8, 'tttttt@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '85679898', 'tuyttu', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(9, 'jjjjjjjjjj@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '70980980980', 'jjjjjjjjj', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(10, 'yyyyyyyy@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '9678665464', 'uuuuuuuuuu', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(11, 'ghjhkjhkjh@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '797897897897', 'hjhjfggfgd', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(12, 'kkkk@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '867867979', 'kkkkk', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35'),
(13, 'sss@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '7586797', 'sssssss', 0, 1, NULL, 2, '2019-03-30 21:08:35', '2019-03-30 15:38:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
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
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
