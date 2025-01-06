-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 26, 2023 at 10:59 AM
-- Server version: 10.6.4-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sm17107_AS91902`
--

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `categories` text NOT NULL,
  `description` text NOT NULL,
  `leader_id` int(11) NOT NULL,
  `meetings` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`club_id`, `name`, `categories`, `description`, `leader_id`, `meetings`) VALUES
(50, 'Year 7&8 Minecraft Club', '[\'Year 7&8\',\'Gaming\',\'Junior School\']', 'Minecraft is a fun video game for all ages, come along and have a good time with friends!!!', 1, '[{\"date\": \"Thursday\", \"time\": \"Lunchtime\", \"location\": \"H2\", \"details\": \"\"}]'),
(54, 'Chess Club', '[\'Chess\',\'Whole School\',\'Board Games\']', 'Love Chess? Come to room A8 every Wednesday lunchtime to pit yourself against the school\'s best!', 2, '[{\"date\": \"Wednesday\", \"time\": \"Lunchtime\", \"location\": \"A8\", \"details\": \"\"}]'),
(55, 'Board Games', '[\'Board Games\',\'All Ages\']', 'Games are in the library every Thursday morning before school. Come in to join.', 5, '[{\"date\": \"Thursday\", \"time\": \"Before School\", \"location\": \"Library\", \"details\": \"\"}]'),
(60, 'First XI Football', '[\'Sports\',\'Football\',\'Senior School\',\'First Team\']', 'The First XI Football team of Katikati College, plays as the KKFC under-19 team in the baywide youth competition.', 4, '[{\"date\": \"Tuesday\", \"time\": \"5:30\", \"location\": \"Moore Park\", \"details\": \"\"}]'),
(63, 'Fortnite', '[\'Esports\']', 'Fornite for 2023 details:\r\nBuild games are Wesdnesday 5pm\r\nNo-Build games are Tuesday 5pm', 7, '[{\"date\": \"Tuesday\", \"time\": \"-99\", \"location\": \"No meeting\", \"details\": \"No Meeting\"},{\"date\": \"Monday\", \"time\": \"-9\", \"location\": \"-9\", \"details\": \"9\"}]'),
(64, 'Sam\'s club', '[\'Testing\']', 'This is a club to test Sam entering a club or is it??', 8, '[{\"date\": \"Monday\", \"time\": \"testing\", \"location\": \"testing\", \"details\": \"\"}]'),
(75, 'Tutoring / Study Group', '[\'All Ages\',\'Academic\',\'Student Led\',\'Before School\']', 'From 8 until 9 before school in the library on thursdays, anyone is welcome, come see Mikai or Michael for more details.', 12, '[{\"date\": \"Thursday\", \"time\": \"8:00am\", \"location\": \"Library\", \"details\": \"From 8 until 9 before school in the library, anyone is welcome, come see Mikai or Michael for more details.\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `leaders`
--

CREATE TABLE `leaders` (
  `leader_id` int(11) NOT NULL,
  `leader_type` int(1) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `leaders`
--

INSERT INTO `leaders` (`leader_id`, `leader_type`, `first_name`, `last_name`, `email`) VALUES
(1, 0, 'Melissa', 'Christophers', 'mchristophers@katikaticollege.school.nz'),
(2, 0, 'Patrick', 'McMahon', 'pmcmahon@katikaticollege.school.nz'),
(3, 1, 'Jack', 'Preston', 'pj17098@katikaticollege.school.nz'),
(4, 2, 'Avi', 'Arora', ''),
(5, 0, 'Colette', 'Lemon', 'clemon@katikaticollege.school.nz'),
(6, 0, 'Andy', 'Chuang', 'achuang@katikaticollege.school.nz'),
(7, 0, 'Andy\'s replacement', 'When he doesn’t leave KKC', 'TBD'),
(8, 1, 'Sam', 'test', 'Sam\'s email'),
(9, 0, 'club', 'leader', ''),
(11, 2, 'first', 'last', ''),
(12, 1, 'Mikai', 'Somerville', 'sm17107@katikaticollege.school.nz');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `username` text NOT NULL,
  `club_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `username`, `club_id`) VALUES
(78, 'Jacob', 'Turnbull', 'tj17122', 55),
(79, 'Jacob', 'Turnbull', 'tj17122', 54),
(80, 'Jacob', 'Turnbull', 'tj17122', 63),
(81, 'Jacob', 'Turnbull', 'tj17122', 64),
(83, 'Jacob', 'Turnbull', 'tj17122', 50),
(85, 'Mikai', 'Somerville', 'sm17107', 60),
(86, 'Mikai', 'Somerville', 'sm17107', 75),
(87, 'Jack', 'Preston', 'pj17098', 63);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `access_type` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `access_type`) VALUES
(1, 'teacher', '$2y$10$hrpdicrBwK0v806cuoWXbeZN4RH6zrvA6RUUl8qg42spk1GyJS/j.', 1),
(4, 'sm17107', '$2y$10$xfjlCbYLV8FzA0lkZeeY7.XizUf8rF3tmj3bZOMogSlNU.GErAp.C', 0),
(5, 'theotherteacher', '$2y$10$vkLaLz6W3riO8gYRA.L3h.D9A5dpi20n5ukSMMbD.NhJLFQjpXKi6', 1),
(6, 'Student Leader test', '$2y$10$YXnd./Dy1VPO0vvMBQbcE.z5cI0dzDZG.CZHje.TBiAw5f1WIcMSu', 2),
(7, 'Sam', '$2y$10$yUoE8GFrBCh42jgwkNC1Fu1SJr0u4NBOJeWFAtEhZFUdIQmBq8VyG', 2),
(10, 'admin', '$2y$10$x5Vi12ZhEhlYu/Ker12SKeL6J806tz6P6rYXJpJQjJGOWBgoYI042', 0),
(11, 'student', '$2y$10$.Qbz72hRhRjnaYBWDFov0evEyS6ObwTq9kqtgBXIA/3oXO.I3lS76', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`club_id`),
  ADD KEY `leader_id` (`leader_id`);

--
-- Indexes for table `leaders`
--
ALTER TABLE `leaders`
  ADD PRIMARY KEY (`leader_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `leaders`
--
ALTER TABLE `leaders`
  MODIFY `leader_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `leaders` (`leader_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
