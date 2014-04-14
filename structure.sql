SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `agreements`;
CREATE TABLE IF NOT EXISTS `agreements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `address` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `krs` char(10) COLLATE utf8_polish_ci NOT NULL,
  `nip` char(10) COLLATE utf8_polish_ci NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `subject` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `owner` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `alarmDate` int(10) unsigned NOT NULL,
  `alarmText` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `uploaded` tinyint(1) NOT NULL DEFAULT '0',
  `filename` varchar(250) COLLATE utf8_polish_ci NOT NULL,
  `filepath` varchar(250) COLLATE utf8_polish_ci NOT NULL,
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company_1` (`company`),
  KEY `address_1` (`address`),
  KEY `krs_1` (`krs`),
  KEY `nip_1` (`nip`),
  KEY `date_1` (`date`),
  KEY `subject_1` (`subject`),
  KEY `alarmDate_1` (`alarmDate`),
  KEY `alarmText_1` (`alarmText`),
  KEY `owner_1` (`owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `agreements_users`;
CREATE TABLE IF NOT EXISTS `agreements_users` (
  `agreement` int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`agreement`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `login` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `password` char(64) COLLATE utf8_polish_ci NOT NULL,
  `manage` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

ALTER TABLE `agreements_users`
  ADD CONSTRAINT `agreements_users_ibfk_1` FOREIGN KEY (`agreement`) REFERENCES `agreements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agreements_users_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;
