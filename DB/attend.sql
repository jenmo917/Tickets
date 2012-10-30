-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 30, 2012 at 04:56 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `attend`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_default_privileges`
--

CREATE TABLE IF NOT EXISTS `acl_default_privileges` (
  `default_privilege_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`default_privilege_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `acl_default_privileges`
--

INSERT INTO `acl_default_privileges` (`default_privilege_id`, `category`, `role_id`, `created`, `updated`) VALUES
(1, 'authenticated user', 2, '2012-08-15 20:13:38', NULL),
(2, 'liu-student', 12, '2012-08-16 06:50:07', NULL),
(3, 'event-creator', 13, '2012-08-18 09:43:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acl_permissions`
--

CREATE TABLE IF NOT EXISTS `acl_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  `permission` enum('allow','deny') NOT NULL DEFAULT 'deny',
  `assertion` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`role_id`,`resource_id`),
  KEY `role_id` (`role_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_permissions`
--

INSERT INTO `acl_permissions` (`role_id`, `resource_id`, `permission`, `assertion`, `created`, `updated`) VALUES
(1, 2, 'allow', NULL, '2012-08-05 10:44:19', '0000-00-00 00:00:00'),
(1, 3, 'allow', NULL, '2012-08-16 11:10:00', '0000-00-00 00:00:00'),
(1, 6, 'allow', 'Login_Model_Assertion_NoUser', '2012-08-17 12:20:19', '0000-00-00 00:00:00'),
(1, 9, 'allow', NULL, '2012-08-09 11:09:24', '0000-00-00 00:00:00'),
(1, 33, 'allow', NULL, '2012-08-10 08:18:26', '0000-00-00 00:00:00'),
(2, 14, 'allow', NULL, '2012-08-09 11:06:39', '0000-00-00 00:00:00'),
(2, 40, 'allow', NULL, '2012-08-18 08:54:03', '0000-00-00 00:00:00'),
(3, 9, 'allow', NULL, '2012-08-08 16:24:20', '0000-00-00 00:00:00'),
(3, 38, 'allow', NULL, '2012-08-13 11:28:30', '0000-00-00 00:00:00'),
(12, 8, 'allow', NULL, '2012-08-17 08:41:10', '0000-00-00 00:00:00'),
(12, 39, 'allow', NULL, '2012-08-17 08:41:10', '0000-00-00 00:00:00'),
(13, 20, 'allow', NULL, '2012-10-30 14:44:02', '0000-00-00 00:00:00'),
(13, 24, 'allow', NULL, '2012-08-19 17:58:37', '0000-00-00 00:00:00'),
(13, 25, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 17:56:47', '0000-00-00 00:00:00'),
(13, 26, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 17:58:14', '0000-00-00 00:00:00'),
(13, 27, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 17:57:14', '0000-00-00 00:00:00'),
(13, 28, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 17:56:47', '0000-00-00 00:00:00'),
(13, 29, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 17:57:14', '0000-00-00 00:00:00'),
(13, 41, 'allow', 'Admin_Model_Assertion_UsersEvent', '2012-08-19 07:10:19', '0000-00-00 00:00:00'),
(13, 42, 'allow', NULL, '2012-08-19 17:58:37', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `acl_privileges`
--

CREATE TABLE IF NOT EXISTS `acl_privileges` (
  `privilege_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `organisation_id` int(10) unsigned DEFAULT NULL,
  `event_id` int(10) unsigned DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`privilege_id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`),
  KEY `organisation_id` (`organisation_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

--
-- Dumping data for table `acl_privileges`
--

INSERT INTO `acl_privileges` (`privilege_id`, `user_id`, `organisation_id`, `event_id`, `role_id`, `start_time`, `end_time`, `created`, `updated`) VALUES
(1, NULL, NULL, NULL, 1, '2012-08-08 11:08:07', '0000-00-00 00:00:00', '2012-08-08 11:08:07', '2012-08-08 11:08:07'),
(79, 65, NULL, NULL, 2, NULL, NULL, '2012-10-30 15:30:18', NULL),
(80, 65, NULL, NULL, 12, NULL, NULL, '2012-10-30 15:30:19', NULL),
(81, 65, NULL, 42, 13, NULL, NULL, '2012-10-30 15:33:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource` varchar(128) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `acl_resources`
--

INSERT INTO `acl_resources` (`resource_id`, `resource`, `created`, `updated`) VALUES
(1, 'page::login::*::*', '2012-07-20 14:13:54', '0000-00-00 00:00:00'),
(2, 'page::temp::*::*', '2012-07-20 14:51:41', '0000-00-00 00:00:00'),
(3, 'page::login::error::*', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(4, 'page::login::error::error', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(5, 'page::login::index::*', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(6, 'page::login::index::index', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(8, 'page::login::index::liu-logout', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(9, 'page::login::index::test', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(10, 'page::temp::error::*', '2012-07-20 14:59:48', '0000-00-00 00:00:00'),
(11, 'page::temp::error::error', '2012-07-20 14:59:49', '0000-00-00 00:00:00'),
(12, 'page::temp::index::*', '2012-07-20 14:59:49', '0000-00-00 00:00:00'),
(13, 'page::temp::index::index', '2012-07-20 14:59:49', '0000-00-00 00:00:00'),
(14, 'page::login::index::logout', '2012-08-08 11:29:34', '0000-00-00 00:00:00'),
(18, 'page::admin::*::*', '2012-08-10 08:14:41', '0000-00-00 00:00:00'),
(19, 'page::admin::ajax::*', '2012-08-10 08:14:42', '0000-00-00 00:00:00'),
(20, 'page::admin::ajax::get-kobra-details', '2012-08-10 08:14:42', '0000-00-00 00:00:00'),
(21, 'page::admin::error::*', '2012-08-10 08:14:42', '0000-00-00 00:00:00'),
(22, 'page::admin::error::error', '2012-08-10 08:14:43', '0000-00-00 00:00:00'),
(23, 'page::admin::event::*', '2012-08-10 08:14:43', '0000-00-00 00:00:00'),
(24, 'page::admin::event::index', '2012-08-10 08:14:43', '0000-00-00 00:00:00'),
(25, 'page::admin::event::attendees', '2012-08-10 08:14:44', '0000-00-00 00:00:00'),
(26, 'page::admin::event::sell', '2012-08-10 08:14:44', '0000-00-00 00:00:00'),
(27, 'page::admin::event::edit', '2012-08-10 08:14:44', '0000-00-00 00:00:00'),
(28, 'page::admin::event::delete', '2012-08-10 08:14:44', '0000-00-00 00:00:00'),
(29, 'page::admin::event::publish', '2012-08-10 08:14:46', '0000-00-00 00:00:00'),
(30, 'page::admin::index::*', '2012-08-10 08:14:51', '0000-00-00 00:00:00'),
(31, 'page::admin::index::index', '2012-08-10 08:14:51', '0000-00-00 00:00:00'),
(32, 'page::admin::index::create-event', '2012-08-10 08:14:52', '0000-00-00 00:00:00'),
(33, 'page::default::*::*', '2012-08-10 08:14:54', '0000-00-00 00:00:00'),
(34, 'page::default::index::*', '2012-08-10 08:14:55', '0000-00-00 00:00:00'),
(35, 'page::default::index::index', '2012-08-10 08:14:55', '0000-00-00 00:00:00'),
(36, 'page::default::error::*', '2012-08-10 08:43:07', '0000-00-00 00:00:00'),
(37, 'page::default::error::error', '2012-08-10 08:43:07', '0000-00-00 00:00:00'),
(38, 'page::*::*::*', '2012-08-13 11:27:58', '0000-00-00 00:00:00'),
(39, 'page::login::index::liu-login', '2012-08-15 09:03:20', '0000-00-00 00:00:00'),
(40, 'page::admin::event::create-event', '2012-08-18 08:52:01', '0000-00-00 00:00:00'),
(41, 'page::admin::event::admin', '2012-08-19 07:09:27', '0000-00-00 00:00:00'),
(42, 'page::admin::event::my-events', '2012-08-19 17:46:13', '0000-00-00 00:00:00'),
(43, 'page::login::index::open-id-login', '2012-09-10 14:51:48', '0000-00-00 00:00:00'),
(44, 'page::default::index::overview', '2012-10-24 11:23:45', '0000-00-00 00:00:00'),
(45, 'page::default::index::validate-ticket-form', '2012-10-24 11:23:45', '0000-00-00 00:00:00'),
(46, 'page::default::index::test', '2012-10-30 12:45:38', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles`
--

CREATE TABLE IF NOT EXISTS `acl_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `acl_roles`
--

INSERT INTO `acl_roles` (`role_id`, `role_name`, `created`, `updated`) VALUES
(1, 'guest', '2012-07-22 16:13:30', '2012-07-22 16:13:30'),
(2, 'authenticated_user', '2012-07-22 16:17:28', '2012-07-22 16:17:28'),
(3, 'site_administrator', '2012-07-22 16:17:28', '2012-07-22 16:17:28'),
(4, 'test1', '2012-07-22 17:20:44', '2012-07-22 17:20:44'),
(6, 'test3', '2012-07-22 17:21:00', '2012-07-22 17:21:00'),
(7, 'test4', '2012-07-22 17:21:00', '2012-07-22 17:21:00'),
(8, 'test5', '2012-07-22 17:25:57', '2012-07-22 17:25:57'),
(10, 'admin', '2012-07-24 13:56:51', '2012-07-24 13:56:51'),
(11, 'student', '2012-08-16 06:48:00', '2012-08-16 06:48:00'),
(12, 'liu-student', '2012-08-16 06:48:11', '2012-08-16 06:48:11'),
(13, 'event_creator', '2012-08-18 09:42:56', '2012-08-18 09:42:56');

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles_inheritances`
--

CREATE TABLE IF NOT EXISTS `acl_roles_inheritances` (
  `role_id` int(10) unsigned NOT NULL,
  `parent_role_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`,`parent_role_id`),
  KEY `role_id` (`role_id`),
  KEY `parent_role_id` (`parent_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_roles_inheritances`
--

INSERT INTO `acl_roles_inheritances` (`role_id`, `parent_role_id`, `created`, `updated`) VALUES
(2, 1, '2012-07-24 14:00:13', '2012-07-24 14:00:13'),
(3, 7, '2012-07-31 14:45:01', '2012-07-31 14:45:01'),
(4, 3, '2012-07-24 14:01:20', '2012-07-24 14:01:20'),
(6, 3, '2012-07-24 14:01:20', '2012-07-24 14:01:20'),
(6, 7, '2012-07-31 14:44:06', '2012-07-31 14:44:06'),
(12, 11, '2012-08-16 06:49:32', '2012-08-16 06:49:32');

-- --------------------------------------------------------

--
-- Table structure for table `acl_user_liu_logins`
--

CREATE TABLE IF NOT EXISTS `acl_user_liu_logins` (
  `user_id` int(10) unsigned NOT NULL,
  `liu_id` varchar(8) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`liu_id`),
  UNIQUE KEY `liu_id` (`liu_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_user_liu_logins`
--

INSERT INTO `acl_user_liu_logins` (`user_id`, `liu_id`, `created`, `updated`) VALUES
(65, 'danjo140', '2012-10-30 15:30:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acl_user_open_ids`
--

CREATE TABLE IF NOT EXISTS `acl_user_open_ids` (
  `user_id` int(10) unsigned NOT NULL,
  `open_id` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`open_id`),
  UNIQUE KEY `liu_id` (`open_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_user_open_ids`
--

INSERT INTO `acl_user_open_ids` (`user_id`, `open_id`, `created`, `updated`) VALUES
(2, '3', '2012-09-10 19:14:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acl_user_roles`
--

CREATE TABLE IF NOT EXISTS `acl_user_roles` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `details` text NOT NULL,
  `location` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `details`, `location`, `public`, `published`, `start_time`, `end_time`, `created`, `updated`) VALUES
(37, 'Whiskies of the World', 'Whiskies of the World is a luxury whisky festival that gathers the worldâ€™s most fascinating distilled spirits along with their makers and ambassadors to give its guests an opportunity to sample, learn and experience a night of delicious spirited fun. The luxurious Hyatt Regency provides the perfect atmosphere for this unique tasting, with a spacious ballroom to showcase hundreds of whiskies, with world class chefs to provide food for pairing, and with a patio for cigar and whisky pairing. Come on November 2nd to enjoy hundreds of whiskies, a dinner buffet, masterclasses, live jazz, cigars and more. Learn, celebrate, experience!\r\nPlease note attendance at this event is limited to persons aged 21 or older. Photo I.D. showing age is required.\r\n\r\nTimes\r\nVIP hour 6.00pm - 7.00pm \r\nGeneral admission from 7.00pm\r\n\r\nEarly Bird tickets\r\nVIP ticket $130.00 (normal price $150.00)\r\nGeneral admission ticket $100.00 (normal price $120.00)\r\nEarly bird tickets are available until Aug 15, 2012 and are refundable until October 1, 2012.\r\n \r\nVIP tickets\r\nVIP ticket holders gain access to the event at 6.00pm (one hour before General Admission ticket holders) plus special VIP hour only pours.', 'Austin, Texas', 1, 1, '2012-11-02 18:00:00', '2012-11-02 22:00:00', '2012-10-30 14:46:09', '0000-00-00 00:00:00'),
(38, 'Halloween', 'Here the organiser can put some information about the event.', 'Trappan', 1, 1, '2012-11-03 18:00:00', '2012-11-03 03:00:00', '2012-10-30 15:14:48', '0000-00-00 00:00:00'),
(39, 'Two Cities Marathon & Half', 'What Makes Our Race\r\n- Personalized bib (Register by Oct 15) \r\n- Long-sleeve technical male & female event shirt \r\n- Custom finisher''s medallion \r\n- Finisher''s sweatshirt \r\n- Live music along the course \r\n- Post-race hot breakfast \r\n- Our signature ice cream sundae\r\n- Free massage and chiropractic \r\n- Michelob Ultra beer garden\r\n- Wine garden \r\n- Chrono-track disposable chip-timing \r\n- Host hotels to shuttle to race day events \r\n- Time Splits called out at each mile \r\n- 17 water/Gatorade stations \r\n- 8 first aid stations along the course \r\n- 2 energy stations along the course \r\n- Flat courses\r\n- Awards ceremony with live band \r\n- Online results immediately after race. \r\n- Online printable finisher''s certificate \r\n', 'Fresno', 1, 1, '2012-11-04 10:00:00', '2012-11-04 16:00:00', '2012-10-30 15:20:36', '0000-00-00 00:00:00'),
(40, 'Muddy Women''s Mud Run', 'Pretty Muddy is a 5k adventurous obstacle course mud run for any woman\r\nwho wants to get outside, spend time with friends and have fun.\r\n\r\n-- 5k Course (3.1 miles, run or walk)\r\n-- Women Only (sorry, guys)\r\n-- Mud (lots of it!)\r\n-- Architectural Obstacles (much better than shabby hay bales and shaky plywood)\r\n-- Pretty Epic Finish Line Party (Entertainment, music, drinks & celebration galore)\r\n \r\nWHAT TO WEAR\r\n\r\nWear whatever you feel comfortable exercising in, as long as you donâ€™t mind it getting dirty.\r\n\r\nWHAT TO BRING\r\n\r\nA valid ID and a signed copy of your waiver for check in.  Download a waiver here.\r\n\r\nChange of clothes. Bring a fresh set of comfortable clothes (including shoes) to change into after your wave. Donâ€™t forget to include a plastic bag (to stash your muddy gear), waterproof sunscreen (sunburn and mud are not a good look) and a towel to dry off. There will be plenty of bathrooms on site, showers to rinse off and private changing areas so you can get clean and enjoy the day.\r\n\r\nA camera. These are pretty awesome memories youâ€™ll want to remember and share! Weâ€™ll also have professional photographers throughout the course capturing all the dirty details. \r\n\r\nCash and credit cards. Weâ€™ll have a variety of food and beverages available for purchase throughout the day, along with some pretty awesome gear.\r\n\r\nFriends and family! Weâ€™ll have lots of places for them to cheer you on, take photos and add to the dayâ€™s excitement.\r\n\r\nAnd hereâ€™s what to leave at home:\r\n\r\nJewelry. Weâ€™d hate for your priceless family tiara to get lost in the mud!\r\n\r\nPets. Really, who wants to drive home with a wet, muddy dog?\r\n\r\nGot more questions? Check out our FAQs page for answers!\r\n\r\n \r\nRefund Policy:\r\nsorry, no refunds.', 'Tampa, Florida', 1, 1, '2012-11-16 10:00:00', '2012-11-16 09:39:00', '2012-10-30 15:22:00', '0000-00-00 00:00:00'),
(41, 'EDGB', 'The Electronic Design Program Venue Fair.', 'Grand Hotel', 1, 1, '2012-11-15 19:00:00', '2012-11-14 23:59:00', '2012-10-30 15:24:08', '0000-00-00 00:00:00'),
(42, 'BEAT2014', 'Some info. Blahalal.', 'Himmelstalund, NorrkÃ¶ping', 1, 1, '2014-05-15 16:00:00', '2014-05-17 13:00:00', '2012-10-30 15:33:09', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `organisations`
--

CREATE TABLE IF NOT EXISTS `organisations` (
  `org_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_number` varchar(20) DEFAULT NULL,
  `name` text NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned DEFAULT NULL,
  `ticket_type_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `liu_id` varchar(8) DEFAULT NULL,
  `payment` enum('cash','invoice') DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `event_id` (`event_id`),
  KEY `ticket_type_id` (`ticket_type_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `event_id`, `ticket_type_id`, `user_id`, `name`, `email`, `liu_id`, `payment`, `created`, `updated`) VALUES
(21, 38, 38, NULL, 'Daniel Josefsson', 'danjo140@student.liu.se', 'danjo140', 'cash', '2012-10-30 15:17:49', '0000-00-00 00:00:00'),
(22, 42, 41, NULL, 'Jens Moser', 'jenmo917@student.liu.se', '', 'cash', '2012-10-30 15:34:51', '0000-00-00 00:00:00'),
(23, 42, 40, NULL, 'Daniel Josefsson', 'danjo140@student.liu.se', 'danjo140', 'cash', '2012-10-30 15:35:32', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_types`
--

CREATE TABLE IF NOT EXISTS `ticket_types` (
  `ticket_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `details` text NOT NULL,
  `order` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`ticket_type_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `ticket_types`
--

INSERT INTO `ticket_types` (`ticket_type_id`, `event_id`, `name`, `price`, `quantity`, `details`, `order`, `created`, `updated`) VALUES
(37, 37, 'VIP', 150, 100, '', 1, '2012-10-30 14:46:09', NULL),
(38, 38, 'VIP', 200, 2, 'Only for VIP''s.', 1, '2012-10-30 15:14:48', NULL),
(39, 42, 'Thursday ticket', 200, 500, 'Thursday ticket.', 1, '2012-10-30 15:33:09', NULL),
(40, 42, 'Weekend', 350, 300, 'Yay!', 2, '2012-10-30 15:33:09', NULL),
(41, 42, 'Friday', 20, 20, '', 3, '2012-10-30 15:33:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `created`, `updated`) VALUES
(65, '2012-10-30 15:30:18', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acl_default_privileges`
--
ALTER TABLE `acl_default_privileges`
  ADD CONSTRAINT `acl_default_privileges_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_permissions`
--
ALTER TABLE `acl_permissions`
  ADD CONSTRAINT `acl_permissions_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `acl_resources` (`resource_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_permissions_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_privileges`
--
ALTER TABLE `acl_privileges`
  ADD CONSTRAINT `acl_privileges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_privileges_ibfk_4` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`org_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_privileges_ibfk_5` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_privileges_ibfk_6` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_roles_inheritances`
--
ALTER TABLE `acl_roles_inheritances`
  ADD CONSTRAINT `acl_roles_inheritances_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_roles_inheritances_ibfk_2` FOREIGN KEY (`parent_role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_user_liu_logins`
--
ALTER TABLE `acl_user_liu_logins`
  ADD CONSTRAINT `acl_user_liu_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `acl_user_roles`
--
ALTER TABLE `acl_user_roles`
  ADD CONSTRAINT `acl_user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_5` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `tickets_ibfk_6` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`ticket_type_id`),
  ADD CONSTRAINT `tickets_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ticket_types`
--
ALTER TABLE `ticket_types`
  ADD CONSTRAINT `ticket_types_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
