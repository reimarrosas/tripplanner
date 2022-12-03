-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2022 at 04:59 PM
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
(1, 1, 'Old Port', '0.00', 0, 1, '333 de la Commune W', '25.00'),
(2, 2, 'CN Tower', '50.00', 1, 1, '290 Bremner Blvd', '75.00'),
(3, 4, 'Parliament ', '0.00', 1, 0, 'Wellington Street', '25.00'),
(4, 3, 'Museum of Vancouver', '25.00', 0, 0, '1100 Chestnut Street', '50.00'),
(5, 5, 'Chutes Montmorency', '75.00', 0, 1, '2490 Ave Royale', '100.00'),
(6, 1, 'Saint Joseph\'s Oratory', '35.00', 1, 0, '3800 Queen Mary Rd', '65.00'),
(7, 12, 'Western Development Museum', '0.00', 0, 1, '2610 Lorne Avenue', '10.00'),
(8, 5, 'Fairmont Le Château Frontenac', '25.00', 1, 0, '1 Rue des Carrières', '55.00'),
(9, 11, 'Stonehall castle', '5.00', 1, 0, '2210 College Avenue', '20.00'),
(10, 6, 'Calgary Stampede', '15.00', 1, 0, '1410 Olympic Way SE', '75.00'),
(11, 9, 'Haunted Manor Victoria', '10.00', 1, 0, '711 Yates Street', '25.00'),
(12, 2, 'Royal Ontario Museum', '45.00', 0, 1, '100 Queens Park', '70.00'),
(13, 2, 'Ripley\'s Aquarium of Canada', '15.00', 0, 0, '288 Bremner Blvd', '25.00'),
(14, 2, 'Casa Loma', '0.00', 1, 0, '1 Austin Terrace', '5.00'),
(15, 2, 'St. Lawrence Market', '35.00', 1, 0, '93 Front St E', '65.00'),
(16, 1, 'Domaine Saint-Bernard', '50.00', 1, 0, '539 CH St Bernard', '90.00');

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
(5, 5, '150.00', '2023-10-23', '3967 Sherbrooke Street', '200.00'),
(6, 6, '40.00', '2022-12-02', '1036 9 Avenue SW', '90.00'),
(7, 8, '75.00', '2023-01-12', '8640 Yellowhead Train NW', '180.00'),
(8, 11, '80.00', '2023-04-22', '1100 Scarth Street', '150.00'),
(9, 10, '140.00', '2023-12-02', '1755 Carlton Street', '200.00'),
(10, 15, '100.00', '2023-02-15', '100 World Parkway', '175.00'),
(11, 12, '80.00', '2023-03-10', '4411 Clayton Street', '140.00'),
(12, 14, '120.00', '2022-12-14', '1284 Bath Road', '220.00'),
(13, 5, '150.00', '2023-08-02', 'Grace Street', '230.00'),
(14, 9, '80.00', '2022-12-28', '2507 Government Street', '150.00'),
(15, 13, '90.00', '2023-04-19', '800 Sunshine Street', '130.00');

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
(1, 'The Ritz-Carlton ', 1, '1228 Sherbrooke St W', 1, '250.00', 'car', '400.00'),
(2, 'Hotel Bonaventure', 0, '900 rue De La Gauchetiere West', 1, '100.00', 'car', '250.00'),
(3, 'Four Seasons', 0, '1440 Rue de la Montagne', 1, '400.00', 'car', '550.00'),
(4, 'Sofitel', 0, '1155 Sherbrooke St W', 1, '250.00', 'car', '400.00'),
(5, 'Delta Hotels by Marriott', 1, '475 Av. du Président-Kennedy', 1, '100.00', 'car', '250.00'),
(6, 'Hotel Le Cantlie Suites', 1, '1110 Sherbrooke Street West', 1, '80.00', 'walking', '140.00'),
(7, 'Le Square Phillips Hotel And Suites', 0, '1193 Place Phillips', 1, '150.00', 'public', '160.00'),
(8, 'DoubleTree by Hilton', 1, '705 Avenue Michel-Jasmin', 1, '110.00', 'walking', '180.00'),
(9, 'Fairmont Royal York', 0, '100 Front St West', 2, '150.00', 'walking', '400.00'),
(10, 'Chelsea Hotel', 1, '33 Gerrard St W', 2, '75.00', 'walking', '115.00'),
(11, 'DoubleTree by Hilton Hotel', 0, '108 Chestnut St', 2, '100.00', 'car', '250.00'),
(12, 'Courtyard by Marriott', 1, '475 Yonge St', 2, '150.00', 'walking', '230.00'),
(13, 'One King West Hotel & Residence', 0, '1 King Street West', 2, '200.00', 'public', '500.00'),
(14, 'The Anndore House', 1, '3366 Douglas Street', 2, '90.00', 'walking', '135.00'),
(15, 'Hyatt Regency', 0, '370 King Street W', 2, '70.00', 'walking', '250.00'),
(16, 'Château Beauvallon', 0, '6385 Mnt Ryan', 1, '100.00', 'car', '200.00');

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
(5, 'Canada', 'Quebec'),
(6, 'Canada', 'Calgary'),
(7, 'Canada', 'Winnipeg'),
(8, 'Canada', 'Edmonton'),
(9, 'Canada', 'Victoria'),
(10, 'Canada', 'Hamilton'),
(11, 'Canada', 'Regina'),
(12, 'Canada', 'Saskatoon'),
(13, 'Canada', 'Mississauga'),
(14, 'Canada', 'Kingston'),
(15, 'Canada', 'St. John\'s');

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
(2, 1, 'Wendy\'s', '45.00', 'public', 0, '7340 Decarie Blvd', '60.00'),
(3, 1, 'McDonald\'s', '1.00', 'walking', 0, '1021 F Rue du Marché Central', '15.00'),
(4, 1, 'Barranco', '4.00', 'car', 1, '4552 Rue Saint-Denis', '50.00'),
(5, 1, 'Harvey\'s', '3.00', 'public', 0, '255 Boul Cremazie Ouest', '22.00'),
(6, 6, 'Alloy', '31.00', 'car', 1, '220 42 Ave SE', '50.00'),
(7, 9, 'Bear & Joey', '10.00', 'public', 1, '1025 Cook Street', '50.00'),
(8, 2, 'Victoria\'s Restaurant', '31.00', 'car', 0, '37 King St E', '50.00'),
(9, 6, 'Cardinale', '31.00', 'public', 1, '401 12 Ave SE', '50.00'),
(10, 7, 'Maxime\'s Restaurant and lounge', '15.00', 'car', 1, '1131 Sainte-Marie\'s Road', '35.00'),
(11, 6, 'Gorilla Whale', '25.00', 'walking', 0, '1214 9 Ave SE', '75.00'),
(12, 14, 'Mio Gelato', '15.00', 'car', 1, '178 Ontario Street', '35.00'),
(13, 3, 'Gotham Steakhouse and Bar', '31.00', 'walking', 1, '615 Seymour Street', '50.00'),
(14, 8, 'Cafe Amore Bistro', '10.00', 'car', 1, '10807 106 Avenue NW', '25.00'),
(15, 2, 'JaBistro', '31.00', 'walking', 0, '222 Richmond Street', '50.00'),
(16, 1, 'Le Club Chasse et Pêche', '8.00', 'public', 1, '423 Rue Saint-Claude', '40.00');

-- --------------------------------------------------------

--
-- Table structure for table `tagged_attraction`
--

DROP TABLE IF EXISTS `tagged_attraction`;
CREATE TABLE `tagged_attraction` (
  `attraction_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tagged_attraction`
--

INSERT INTO `tagged_attraction` (`attraction_id`, `tag_id`) VALUES
(1, 2),
(2, 1),
(3, 1),
(4, 2),
(5, 2),
(6, 1),
(7, 2),
(8, 3),
(9, 3),
(12, 2),
(13, 2),
(14, 3),
(16, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tagged_hotel`
--

DROP TABLE IF EXISTS `tagged_hotel`;
CREATE TABLE `tagged_hotel` (
  `hotel_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tagged_hotel`
--

INSERT INTO `tagged_hotel` (`hotel_id`, `tag_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tagged_restaurant`
--

DROP TABLE IF EXISTS `tagged_restaurant`;
CREATE TABLE `tagged_restaurant` (
  `restaurant_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tagged_restaurant`
--

INSERT INTO `tagged_restaurant` (`restaurant_id`, `tag_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`) VALUES
(1, 'City'),
(2, 'Group Tour'),
(3, 'Historic Sites'),
(4, 'Camping');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permission` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `permission`) VALUES
(1, 'admin', '$2a$10$P2Qv.SWBkBvIvTd2v3MKW.jWicA4NRaEflrqEAQ5GlvanG97Hxkoa', 'admin');

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
  MODIFY `attraction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `car_rental`
--
ALTER TABLE `car_rental`
  MODIFY `car_rental_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
