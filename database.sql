-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 21 nov. 2017 à 20:48
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `oxford`
--
CREATE DATABASE IF NOT EXISTS `oxford` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `oxford`;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL,
  `password` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `password`, `date`) VALUES
(2, 'eb56a4e76903092c46948857852aceab8efbe4ac92f8b913ef7cde14f1f2bc63', 1509631100),
(5, '0e937c1d640493f30fded4a46b756d839f33feb445f520f66631a227c1dbfc36', 1511107437);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `value`) VALUES
(1, 'Erreur'),
(2, 'Info'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_fk_events_users` (`user`),
  KEY `i_fk_events_categories` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `user`, `category`, `date`, `value`) VALUES
(1, 4, 2, 1509576328, 'Connexion'),
(2, 4, 1, 1509576354, 'Connexion'),
(3, 4, 3, 1509576358, 'Suppression de l\'utilisateur #2');

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `value` varchar(55) NOT NULL,
  `faceid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `photos`
--

INSERT INTO `photos` (`id`, `date`, `value`, `faceid`) VALUES
(1, 1509570698, '1509570698.jpg', NULL),
(2, 1509571265, '1509571265.jpg', NULL),
(3, 1509571832, '1509571832.jpg', NULL),
(4, 1509571840, '1509571840.jpg', NULL),
(5, 1509571851, '1509571851.jpg', NULL),
(6, 1509571881, '1509571881.jpg', NULL),
(7, 1509572034, '1509572034.jpg', NULL),
(8, 1509572059, '1509572059.jpg', NULL),
(9, 1509572079, '1509572079.jpg', NULL),
(10, 1509572082, '1509572082.jpg', NULL),
(11, 1509572083, '1509572083.jpg', NULL),
(12, 1509572085, '1509572085.jpg', NULL),
(13, 1509572137, '1509572137.jpg', NULL),
(14, 1509572169, '1509572169.jpg', NULL),
(15, 1509572203, '1509572203.jpg', NULL),
(16, 1509572275, '1509572275.jpg', NULL),
(17, 1509572350, '1509572350.jpg', NULL),
(18, 1509572458, '1509572458.jpg', NULL),
(19, 1509572460, '1509572460.jpg', NULL),
(20, 1509573032, '1509573032.jpg', NULL),
(21, 1509573050, '1509573050.jpg', NULL),
(22, 1509573063, '1509573063.jpg', NULL),
(23, 1509573098, '1509573098.jpg', NULL),
(24, 1509573210, '1509573210.jpg', NULL),
(25, 1509573265, '1509573265.jpg', NULL),
(35, 1509631462, '1509631462.png', NULL),
(36, 1511107349, '1511107349.jpg', NULL),
(37, 1511107379, '1511107379.jpg', NULL),
(38, 1511107386, '1511107386.jpg', NULL),
(39, 1511107392, '1511107392.jpg', NULL),
(40, 1511107414, '1511107414.jpg', NULL),
(41, 1511107424, '1511107424.jpg', NULL),
(42, 1511107437, '1511107437.jpg', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `subjects`
--

INSERT INTO `subjects` (`id`, `value`) VALUES
(1, 'Connexion impossible'),
(2, 'Ma photo n\'est pas reconnue'),
(3, 'Mon code d\'accès ne marche pas');

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` int(11) DEFAULT NULL,
  `subject` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `statut` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_fk_tickets_events` (`event`),
  KEY `i_fk_tickets_subjects` (`subject`),
  KEY `i_fk_tickets_users` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `event`, `subject`, `user`, `date`, `statut`, `token`) VALUES
(3, NULL, 1, 4, 1511296193, 3, 'ad2bbd3ebb8edfdg7e4f13aac8673d8b87'),
(4, NULL, 2, 6, 1511294892, 2, 'ad2bbd3ebb8ed37e4f13aargrgc8673d8b87'),
(5, NULL, 3, 4, 1511293908, 2, 'ad2bbd3ebb8ed37e4f1azad3aac8673d8b87'),
(6, NULL, 2, 6, 1506020789, 3, 'ad2bbazaazd3ebb8ed37e4f13aac8673d8b87'),
(7, NULL, 1, 4, 1511294927, 1, 'ad2bbd3ebb8edfdg7e4f13aac8673d8b87');

-- --------------------------------------------------------

--
-- Structure de la table `tickets_replies`
--

DROP TABLE IF EXISTS `tickets_replies`;
CREATE TABLE IF NOT EXISTS `tickets_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `ticket` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_fk_tickets_replies_users` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tickets_replies`
--

INSERT INTO `tickets_replies` (`id`, `user`, `value`, `date`, `ticket`) VALUES
(34, 5, 'thomas le pd', 1511296193, 3),
(33, 4, 'sa marsh pa :\'(', 1511293908, 3);

-- --------------------------------------------------------

--
-- Structure de la table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(55) NOT NULL,
  `filter` varchar(20) DEFAULT NULL,
  `icon` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `types`
--

INSERT INTO `types` (`id`, `value`, `filter`, `icon`) VALUES
(1, 'RSSI', 'rssi', 'user-secret'),
(2, 'Admin', 'admin', 'id-badge'),
(3, 'Vigile', 'vigile', 'lock'),
(4, 'Agent d\'entretien', 'cleaner', 'shower'),
(5, 'Technicien', 'technicien', 'wrench'),
(6, 'Visiteur', 'visiteur', 'user-o');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `birth` int(11) NOT NULL,
  `sexe` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `photo` int(11) NOT NULL,
  `code` int(4) NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_fk_users_types` (`type`),
  KEY `i_fk_users_photos` (`photo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `birth`, `sexe`, `email`, `photo`, `code`, `type`, `status`) VALUES
(4, 'RAVIA', 'Julien', 882144000, 'M', 'jrgfawkes@gmail.com', 35, 6523, 3, 1),
(5, 'Jean', 'Kevin', 882144000, 'M', 'xylis1337@protonmail.com', 42, 8061, 1, 1),
(6, 'JAMY', 'Leplaofray', 882144000, 'M', 'xylis1337@protonmail.com', 42, 8061, 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
