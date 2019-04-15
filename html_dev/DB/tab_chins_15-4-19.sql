-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2019 at 10:04 AM
-- Server version: 5.7.25-0ubuntu0.18.10.2
-- PHP Version: 7.2.15-0ubuntu0.18.10.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tab`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_list`
--

CREATE TABLE `company_list` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company_list`
--

INSERT INTO `company_list` (`company_id`, `company_name`) VALUES
(1, 'ALLIANZ CORNHILL INFORMATION SERVICES PVT LTD'),
(2, 'Cycloides Technologies Pvt. Ltd.'),
(3, 'EPICA STUDIO PVT LTD'),
(4, 'EYME Technologies Pvt Ltd'),
(5, 'H&R Block'),
(6, 'IBS Software Services Pvt. Ltd.'),
(7, 'Infosys Ltd'),
(8, 'Oracle India'),
(9, 'QuEST Global Engineering Services Pvt Ltd'),
(10, 'RM Education Solutions India Pvt Ltd');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(120) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `prelims` int(11) NOT NULL COMMENT '0-not,1-yes,2-prelims completed',
  `event_date` varchar(20) DEFAULT NULL,
  `prelims_date` varchar(20) DEFAULT NULL,
  `prelims_venue` varchar(255) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '0-active,1-judgement open,2-judgement closed, 99-deleted',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `contact_id`, `prelims`, `event_date`, `prelims_date`, `prelims_venue`, `venue`, `status`, `created_date`) VALUES
(1, 'Dance', 0, 1, '0000-00-00 00:00:00', NULL, NULL, 'sdfsg', 99, '2019-04-14 02:13:03'),
(2, 'Song', 4, 0, '4/19/19 10:00 PM', NULL, NULL, 'jijloij', 1, '2019-04-14 02:16:46'),
(3, 'Drawing', 2, 0, '4/14/19 02:00 AM', NULL, NULL, 'fghfg', 1, '2019-04-14 02:17:56'),
(4, 'Painting', 3, 1, '4/16/19 03:00 AM', '4/26/19 09:00 AM', 'ertret55', 'dgret1', 0, '2019-04-14 02:55:48'),
(5, 'Cartoon', 3, 0, '4/19/19 03:00 AM', NULL, NULL, 'gddh', 0, '2019-04-14 03:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `event_judges`
--

CREATE TABLE `event_judges` (
  `id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `prilims` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_registration`
--

CREATE TABLE `event_registration` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `company_id` int(11) NOT NULL,
  `prelims_roll_no` int(11) NOT NULL,
  `roll_no` int(3) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_registration`
--

INSERT INTO `event_registration` (`id`, `event_id`, `name`, `email`, `contact_no`, `company_id`, `prelims_roll_no`, `roll_no`, `status`, `created_date`) VALUES
(1, 5, 'dgfg', 'sdfdsd@ghfh.ddt', '45345', 4, 0, 0, 99, '2019-04-14 12:34:58'),
(2, 5, 'fdgdfgdf', '', '', 0, 0, 0, 1, '2019-04-14 12:35:51'),
(3, 4, 'fgdfg', '', '', 0, 0, 0, 1, '2019-04-14 12:36:51'),
(4, 4, 'vnhfthf', '', '', 0, 0, 0, 1, '2019-04-14 12:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `judgement_criteria`
--

CREATE TABLE `judgement_criteria` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `criteria` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Co-ordinator'),
(3, 'Judge'),
(4, 'Contestant');

-- --------------------------------------------------------

--
-- Table structure for table `score_card`
--

CREATE TABLE `score_card` (
  `score_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `contestant_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `prelims` int(11) NOT NULL COMMENT '0-not,1-yes',
  `judge_id` int(11) NOT NULL,
  `total_score` double NOT NULL,
  `judgement` varchar(200) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tab_user`
--

CREATE TABLE `tab_user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  `about_user` varchar(255) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tab_user`
--

INSERT INTO `tab_user` (`user_id`, `name`, `email`, `password`, `about_user`, `company_id`, `role_id`, `contact_no`, `created_date`, `status`) VALUES
(1, 'Chinnu', 'chinschips@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 1, 1, '35345', '2019-04-13 00:00:00', 1),
(2, 'member1', 'member1@hfgh.dff', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 1, 2, '56464486546', '2019-04-13 21:46:50', 2),
(3, 'member2', 'member2@sdaas.dsdf', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 2, 2, '8798733333', '2019-04-13 23:19:52', 2),
(4, 'member3', 'member3@gfdg.fdsf', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 8, 2, '4234235', '2019-04-13 23:23:28', 2),
(5, 'Judge1', 'judge1@hi.sdf', '827ccb0eea8a706c4c34a16891f84e7b', 'dsakfpo sfksodkf sfpsdfk[psd fghfghfg', 0, 3, '798798797', '2019-04-14 01:14:45', 2),
(6, 'Judge2', 'Judge2@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'fyh i uhh iiuhiu iuhi', 0, 3, '464756756', '2019-04-14 01:25:54', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company_list`
--
ALTER TABLE `company_list`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_judges`
--
ALTER TABLE `event_judges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_registration`
--
ALTER TABLE `event_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `judgement_criteria`
--
ALTER TABLE `judgement_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `score_card`
--
ALTER TABLE `score_card`
  ADD PRIMARY KEY (`score_id`);

--
-- Indexes for table `tab_user`
--
ALTER TABLE `tab_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company_list`
--
ALTER TABLE `company_list`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `event_judges`
--
ALTER TABLE `event_judges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event_registration`
--
ALTER TABLE `event_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `judgement_criteria`
--
ALTER TABLE `judgement_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `score_card`
--
ALTER TABLE `score_card`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tab_user`
--
ALTER TABLE `tab_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
