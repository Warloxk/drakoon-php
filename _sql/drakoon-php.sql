-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.38-0+wheezy1 - (Debian)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table drakoon-php.log_login_attempt
CREATE TABLE IF NOT EXISTS `log_login_attempt` (
  `date` timestamp NULL DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `guid` varchar(32) DEFAULT NULL,
  `inp_user_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table drakoon-php.log_login_attempt: ~0 rows (approximately)
/*!40000 ALTER TABLE `log_login_attempt` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_login_attempt` ENABLE KEYS */;


-- Dumping structure for table drakoon-php.log_login_attempt_archive
CREATE TABLE IF NOT EXISTS `log_login_attempt_archive` (
  `date` timestamp NULL DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `guid` varchar(32) DEFAULT NULL,
  `inp_user_name` varchar(50) DEFAULT NULL
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- Dumping data for table drakoon-php.log_login_attempt_archive: 0 rows
/*!40000 ALTER TABLE `log_login_attempt_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_login_attempt_archive` ENABLE KEYS */;


-- Dumping structure for table drakoon-php.log_mysql_errors
CREATE TABLE IF NOT EXISTS `log_mysql_errors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(500) DEFAULT NULL,
  `error` varchar(500) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table drakoon-php.log_mysql_errors: ~0 rows (approximately)
/*!40000 ALTER TABLE `log_mysql_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_mysql_errors` ENABLE KEYS */;


-- Dumping structure for table drakoon-php.session
CREATE TABLE IF NOT EXISTS `session` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `guid` varchar(32) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `user_agent` varchar(300) NOT NULL,
  UNIQUE KEY `guid` (`guid`),
  KEY `time` (`time`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table drakoon-php.session: ~0 rows (approximately)
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;


-- Dumping structure for table drakoon-php.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '101',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language` varchar(2) DEFAULT 'hu',
  `login_date` timestamp NULL DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT NULL,
  `credit` int(10) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `password` (`password`),
  KEY `rank` (`rank`),
  KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table drakoon-php.users: ~2 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `password`, `rank`, `points`, `email`, `status`, `language`, `login_date`, `registration_date`, `credit`, `guid`) VALUES
	(1, 'admin', 'cc341f5d06d17de6512f53fcaaf6f67d', 255, 0, 'admin@drakoon-php', 1, 'hu', NULL, NULL, 0, NULL),
	(2, 'teszt', '6c90aa3760658846a86a263a4e92630e', 101, 0, 'teszt@drakoon-php', 1, 'hu', NULL, NULL, 0, NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
