-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2022 at 09:02 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking`
--
CREATE DATABASE IF NOT EXISTS `booking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `booking`;

-- --------------------------------------------------------

--
-- Table structure for table `attraction`
--

DROP TABLE IF EXISTS `attraction`;
CREATE TABLE `attraction` (
  `attraction_id` int(11) NOT NULL,
  `location_fk` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_min` decimal(5,2) NOT NULL,
  `parking` tinyint(1) NOT NULL,
  `charging_station` tinyint(1) NOT NULL,
  `street` varchar(255) NOT NULL,
  `price_max` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attraction`
--

INSERT INTO `attraction` (`attraction_id`, `location_fk`, `name`, `price_min`, `parking`, `charging_station`, `street`, `price_max`) VALUES
(1, 1, 'Old Port', '0.00', 0, 1, '333 De La Commune Street', '25.00'),
(2, 2, 'CN Tower', '50.00', 1, 1, '290 Bremner Street', '75.00'),
(3, 4, 'Parliament ', '0.00', 1, 0, '562 Wellington Street', '25.00'),
(4, 3, 'Museum of Vancouver', '25.00', 0, 0, '310 Granville Street', '50.00'),
(5, 5, 'Chutes Montmorency', '75.00', 0, 1, '5300 Sainte-Anne Street', '100.00');

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

DROP TABLE IF EXISTS `car`;
CREATE TABLE `car` (
  `car_id` int(11) NOT NULL,
  `make` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `passenger` int(255) NOT NULL,
  `year` int(255) NOT NULL,
  `type` enum('suv','luxury','sports','sedan') NOT NULL,
  `car_rental_fk` int(11) NOT NULL,
  `price` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`car_id`, `make`, `model`, `passenger`, `year`, `type`, `car_rental_fk`, `price`) VALUES
(1, 'Toyota', 'Yaris', 4, 2015, 'sedan', 1, '17.00'),
(2, 'Honda', 'Civic', 4, 2015, 'sedan', 2, '18.00'),
(3, 'Hyundai', 'Santa Fe', 5, 2016, 'suv', 1, '15.00'),
(4, 'BMW', 'i8', 2, 2022, 'sports', 2, '25.00'),
(5, 'Rolls-Royce', 'Ghost', 4, 2021, 'luxury', 1, '30.00');

-- --------------------------------------------------------

--
-- Table structure for table `car_rental`
--

DROP TABLE IF EXISTS `car_rental`;
CREATE TABLE `car_rental` (
  `car_rental_id` int(11) NOT NULL,
  `location_fk` int(11) NOT NULL,
  `price_min` decimal(5,2) NOT NULL,
  `rental_duration` date NOT NULL,
  `street` varchar(255) NOT NULL,
  `price_max` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `car_rental`
--

INSERT INTO `car_rental` (`car_rental_id`, `location_fk`, `price_min`, `rental_duration`, `street`, `price_max`) VALUES
(1, 1, '100.00', '2022-12-02', '2463 Saint-Kevin Street', '150.00'),
(2, 2, '150.00', '2023-01-16', '8264 St-Laurent Street', '200.00'),
(3, 3, '150.00', '2023-02-10', '2734 Saint-Marie Street', '200.00'),
(4, 4, '100.00', '2023-04-19', '3648 Victoria Street', '150.00'),
(5, 5, '150.00', '2023-10-23', '3967 Sherbrooke Street', '200.00');

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

DROP TABLE IF EXISTS `food`;
CREATE TABLE `food` (
  `food_id` int(11) NOT NULL,
  `restaurant_fk` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `food`
--

INSERT INTO `food` (`food_id`, `restaurant_fk`, `type`, `name`, `price`) VALUES
(1, 3, 'Meal', 'Quarter Pounder Cheese Burger', '6.00'),
(2, 5, 'Dessert', 'Chocolate Cake', '7.00'),
(3, 1, 'Meal', 'Steak ', '17.00'),
(4, 2, 'Dessert', 'Apple Pie', '7.00'),
(5, 4, 'Sides', 'Fries', '2.99');

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

DROP TABLE IF EXISTS `hotel`;
CREATE TABLE `hotel` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `charging_station` tinyint(1) NOT NULL,
  `Street` varchar(255) NOT NULL,
  `location_fk` int(11) NOT NULL,
  `price_min` decimal(5,2) NOT NULL,
  `accessibility` enum('car','public','walking','') NOT NULL,
  `price_max` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hotel`
--

INSERT INTO `hotel` (`hotel_id`, `name`, `charging_station`, `Street`, `location_fk`, `price_min`, `accessibility`, `price_max`) VALUES
(1, 'The Ritz-Carlton ', 1, '2193 Saint-Denise Street', 1, '250.00', 'car', '400.00'),
(2, 'Hilton', 0, '2236 Crescent Street', 2, '100.00', 'car', '250.00'),
(3, 'Four Seasons', 0, '038 Saint-Martin Street', 3, '400.00', 'car', '550.00'),
(4, 'Sofitel', 0, '1936 Saint-Marie Street', 4, '250.00', 'car', '400.00'),
(5, 'Delta', 1, '2502 Maine Street', 5, '100.00', 'car', '250.00');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `country`, `city`) VALUES
(1, 'Canada', 'Montreal'),
(2, 'Canada', 'Toronto'),
(3, 'Canada', 'Vancouver'),
(4, 'Canada', 'Ottawa'),
(5, 'Canada', 'Quebec');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

DROP TABLE IF EXISTS `restaurant`;
CREATE TABLE `restaurant` (
  `restaurant_id` int(11) NOT NULL,
  `location_fk` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_min` decimal(5,2) NOT NULL,
  `accessibility` enum('car','public','walking','') NOT NULL,
  `charging_station` tinyint(1) NOT NULL,
  `street` varchar(255) NOT NULL,
  `price_max` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`restaurant_id`, `location_fk`, `name`, `price_min`, `accessibility`, `charging_station`, `street`, `price_max`) VALUES
(1, 1, 'Baton-Rouge', '50.00', 'car', 1, '6373 Decarie Street', '75.00'),
(2, 2, 'Balthazar', '45.00', 'public', 0, '2946 Notre-Dame Street', '60.00'),
(3, 3, 'McDonald\'s', '1.00', 'walking', 0, '2749 Jarry Street', '15.00'),
(4, 4, 'KFC', '4.00', 'car', 1, '2047 Saint-Catherine Street', '50.00'),
(5, 5, 'Harvey\'s', '3.00', 'public', 0, '4065 Ontario Street', '22.00');

-- --------------------------------------------------------

--
-- Table structure for table `tagged_attraction`
--

DROP TABLE IF EXISTS `tagged_attraction`;
CREATE TABLE `tagged_attraction` (
  `attraction_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tagged_hotel`
--

DROP TABLE IF EXISTS `tagged_hotel`;
CREATE TABLE `tagged_hotel` (
  `hotel_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tagged_restaurant`
--

DROP TABLE IF EXISTS `tagged_restaurant`;
CREATE TABLE `tagged_restaurant` (
  `restaurant_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permission` enum('admin','','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `permission`) VALUES
(1, 'admin', 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attraction`
--
ALTER TABLE `attraction`
  ADD PRIMARY KEY (`attraction_id`),
  ADD KEY `location_fk4` (`location_fk`);

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `car_rental_fk` (`car_rental_fk`);

--
-- Indexes for table `car_rental`
--
ALTER TABLE `car_rental`
  ADD PRIMARY KEY (`car_rental_id`),
  ADD KEY `location_fk2` (`location_fk`);

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`food_id`),
  ADD KEY `restaurant_fk` (`restaurant_fk`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`hotel_id`),
  ADD KEY `location_fk` (`location_fk`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`restaurant_id`),
  ADD KEY `location_fk3` (`location_fk`);

--
-- Indexes for table `tagged_attraction`
--
ALTER TABLE `tagged_attraction`
  ADD PRIMARY KEY (`attraction_id`,`tag_id`),
  ADD KEY `tag_fk2` (`tag_id`);

--
-- Indexes for table `tagged_hotel`
--
ALTER TABLE `tagged_hotel`
  ADD PRIMARY KEY (`hotel_id`,`tag_id`),
  ADD KEY `tag_fk` (`tag_id`);

--
-- Indexes for table `tagged_restaurant`
--
ALTER TABLE `tagged_restaurant`
  ADD PRIMARY KEY (`restaurant_id`,`tag_id`),
  ADD KEY `tag_fk3` (`tag_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attraction`
--
ALTER TABLE `attraction`
  MODIFY `attraction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `car_rental`
--
ALTER TABLE `car_rental`
  MODIFY `car_rental_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attraction`
--
ALTER TABLE `attraction`
  ADD CONSTRAINT `location_fk4` FOREIGN KEY (`location_fk`) REFERENCES `location` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `car_rental_fk` FOREIGN KEY (`car_rental_fk`) REFERENCES `car_rental` (`car_rental_id`) ON DELETE CASCADE;

--
-- Constraints for table `car_rental`
--
ALTER TABLE `car_rental`
  ADD CONSTRAINT `location_fk2` FOREIGN KEY (`location_fk`) REFERENCES `location` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `food`
--
ALTER TABLE `food`
  ADD CONSTRAINT `restaurant_fk` FOREIGN KEY (`restaurant_fk`) REFERENCES `restaurant` (`restaurant_id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel`
--
ALTER TABLE `hotel`
  ADD CONSTRAINT `location_fk` FOREIGN KEY (`location_fk`) REFERENCES `location` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD CONSTRAINT `location_fk3` FOREIGN KEY (`location_fk`) REFERENCES `location` (`location_id`) ON DELETE CASCADE;

--
-- Constraints for table `tagged_attraction`
--
ALTER TABLE `tagged_attraction`
  ADD CONSTRAINT `attraction_fk` FOREIGN KEY (`attraction_id`) REFERENCES `attraction` (`attraction_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_fk2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `tagged_hotel`
--
ALTER TABLE `tagged_hotel`
  ADD CONSTRAINT `hotel_fk ` FOREIGN KEY (`hotel_id`) REFERENCES `hotel` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_fk` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `tagged_restaurant`
--
ALTER TABLE `tagged_restaurant`
  ADD CONSTRAINT `restaurant_fk2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant` (`restaurant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_fk3` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
