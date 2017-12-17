-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `card_id` int(20) NOT NULL,
  `type_id` int(20) NOT NULL,
  `card_state_id` int(20) NOT NULL,
  `message_text` text NOT NULL,
  `added` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `edited` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `card_id` (`card_id`),
  CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `activities_ibfk_5` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cards`;
CREATE TABLE `cards` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `added` datetime NOT NULL,
  `name` varchar(140) NOT NULL,
  `description` text NOT NULL,
  `state_id` tinyint(1) NOT NULL,
  `seen` tinyint(1) NOT NULL,
  `archived` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `size` (`state_id`),
  CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cards_taxonomies`;
CREATE TABLE `cards_taxonomies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `card_id` int(20) NOT NULL,
  `taxonomy_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_id` (`card_id`),
  KEY `taxonomy_id` (`taxonomy_id`),
  CONSTRAINT `cards_taxonomies_ibfk_4` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `cards_taxonomies_ibfk_5` FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomies` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `type_id` int(1) NOT NULL,
  `author_id` int(1) NOT NULL,
  `card_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `card_id` (`card_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `taxonomies`;
CREATE TABLE `taxonomies` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `taxonomies` (`id`, `name`, `type`) VALUES
(1, 'Naléhavé', 1),
(2, 'Hardware', 2),
(3, 'Software', 2),
(4, 'Školní síť', 2),
(5, 'Jiné', 2);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `name_ldap` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `settings` text,
  `joined` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `group_id` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users_session`;
CREATE TABLE `users_session` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2017-02-27 21:17:52