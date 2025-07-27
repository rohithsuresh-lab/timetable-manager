-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 01:22 PM
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
-- Database: `user_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`) VALUES
(1, 'S1'),
(2, 'S2'),
(3, 'S3'),
(4, 'S4'),
(5, 'S5'),
(6, 'S6');

-- --------------------------------------------------------

--
-- Table structure for table `class_notes`
--

CREATE TABLE `class_notes` (
  `note_id` int(11) NOT NULL,
  `rep_id` int(11) NOT NULL,
  `timetable_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `is_rep` tinyint(1) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `class_id`, `is_rep`, `is_approved`) VALUES
(4, 2, 1, 1),
(5, 6, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `name`, `teacher_id`) VALUES
(2, 'Chem', 11),
(3, 'asd', 11),
(4, 'DSA', 12);

-- --------------------------------------------------------

--
-- Table structure for table `substitute_requests`
--

CREATE TABLE `substitute_requests` (
  `request_id` int(11) NOT NULL,
  `original_teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `original_subject_id` int(11) NOT NULL,
  `substitute_teacher_id` int(11) DEFAULT NULL,
  `substitute_subject_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `substitute_requests`
--

INSERT INTO `substitute_requests` (`request_id`, `original_teacher_id`, `class_id`, `date`, `hour`, `original_subject_id`, `substitute_teacher_id`, `substitute_subject_id`, `status`, `reason`) VALUES
(17, 12, 2, '2025-07-28', 1, 4, 11, 3, 'approved', 'No reason provided');

-- --------------------------------------------------------

--
-- Table structure for table `substitute_request_applicants`
--

CREATE TABLE `substitute_request_applicants` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `substitute_teacher_id` int(11) NOT NULL,
  `substitute_subject_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `applied_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `substitute_request_applicants`
--

INSERT INTO `substitute_request_applicants` (`id`, `request_id`, `substitute_teacher_id`, `substitute_subject_id`, `status`, `applied_on`) VALUES
(5, 17, 11, 3, 'approved', '2025-07-27 16:24:44');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`) VALUES
(11),
(12);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_leaves`
--

CREATE TABLE `teacher_leaves` (
  `leave_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_leaves`
--

INSERT INTO `teacher_leaves` (`leave_id`, `teacher_id`, `date`, `hour`, `reason`) VALUES
(25, 12, '2025-07-28', 1, 'No reason provided');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `timetable_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `day_of_week` enum('Mon','Tue','Wed','Thu','Fri') NOT NULL,
  `hour` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`timetable_id`, `class_id`, `subject_id`, `day_of_week`, `hour`) VALUES
(1, 2, 4, 'Mon', 1),
(2, 2, 3, 'Mon', 2),
(3, 2, 3, 'Mon', 3),
(4, 2, 4, 'Mon', 4),
(5, 2, 2, 'Mon', 5),
(6, 6, 2, 'Thu', 1),
(7, 1, 2, 'Thu', 1),
(8, 1, 3, 'Thu', 2);

-- --------------------------------------------------------

--
-- Table structure for table `timetable_overrides`
--

CREATE TABLE `timetable_overrides` (
  `override_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `original_teacher_id` int(11) NOT NULL,
  `substitute_teacher_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable_overrides`
--

INSERT INTO `timetable_overrides` (`override_id`, `class_id`, `date`, `hour`, `original_teacher_id`, `substitute_teacher_id`, `subject_id`) VALUES
(1, 2, '2025-07-28', 1, 12, 11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(2, 'admin', 'admin@gmail.com', '123', 'admin'),
(4, 'amal', 'amal@gmail.com', '123', 'student'),
(5, 'rohit', 'rohit@gmail.com', '123', 'student'),
(11, 'roshna', 'roshna@gmail.com', '123', 'teacher'),
(12, 'roshini', 'roshini@gmail.com', '123', 'teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_notes`
--
ALTER TABLE `class_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `notesRep` (`rep_id`),
  ADD KEY `notesTable` (`timetable_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `studenClass` (`class_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `subjectTeacher` (`teacher_id`);

--
-- Indexes for table `substitute_requests`
--
ALTER TABLE `substitute_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `OTeacher` (`original_teacher_id`),
  ADD KEY `OSubject` (`original_subject_id`),
  ADD KEY `STeacher` (`substitute_teacher_id`),
  ADD KEY `SSubject` (`substitute_subject_id`),
  ADD KEY `onClass` (`class_id`);

--
-- Indexes for table `substitute_request_applicants`
--
ALTER TABLE `substitute_request_applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subs` (`request_id`),
  ADD KEY `teacher` (`substitute_teacher_id`),
  ADD KEY `subSubjects` (`substitute_subject_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_leaves`
--
ALTER TABLE `teacher_leaves`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `leaveTeacher` (`teacher_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`timetable_id`),
  ADD KEY `tableSubject` (`subject_id`),
  ADD KEY `tableClass` (`class_id`);

--
-- Indexes for table `timetable_overrides`
--
ALTER TABLE `timetable_overrides`
  ADD PRIMARY KEY (`override_id`),
  ADD KEY `class` (`class_id`),
  ADD KEY `userid` (`original_teacher_id`),
  ADD KEY `subTeacher` (`substitute_teacher_id`),
  ADD KEY `subject` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `class_notes`
--
ALTER TABLE `class_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `substitute_requests`
--
ALTER TABLE `substitute_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `substitute_request_applicants`
--
ALTER TABLE `substitute_request_applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_leaves`
--
ALTER TABLE `teacher_leaves`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `timetable_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `timetable_overrides`
--
ALTER TABLE `timetable_overrides`
  MODIFY `override_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class_notes`
--
ALTER TABLE `class_notes`
  ADD CONSTRAINT `notesRep` FOREIGN KEY (`rep_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notesTable` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`timetable_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `studenClass` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentUser` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjectTeacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `substitute_requests`
--
ALTER TABLE `substitute_requests`
  ADD CONSTRAINT `OSubject` FOREIGN KEY (`original_subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `OTeacher` FOREIGN KEY (`original_teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `SSubject` FOREIGN KEY (`substitute_subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `STeacher` FOREIGN KEY (`substitute_teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `onClass` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `substitute_request_applicants`
--
ALTER TABLE `substitute_request_applicants`
  ADD CONSTRAINT `subSubjects` FOREIGN KEY (`substitute_subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subs` FOREIGN KEY (`request_id`) REFERENCES `substitute_requests` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teacher` FOREIGN KEY (`substitute_teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teacherUser` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_leaves`
--
ALTER TABLE `teacher_leaves`
  ADD CONSTRAINT `leaveTeacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `tableClass` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tableSubject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timetable_overrides`
--
ALTER TABLE `timetable_overrides`
  ADD CONSTRAINT `class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subTeacher` FOREIGN KEY (`substitute_teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userid` FOREIGN KEY (`original_teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
