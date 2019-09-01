-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 01 sep. 2019 à 09:08
-- Version du serveur :  10.3.15-MariaDB
-- Version de PHP :  7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `comobdb`
--
CREATE DATABASE IF NOT EXISTS `comobdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `comobdb`;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_capacites`
--

DROP TABLE IF EXISTS `xxx_capacites`;
CREATE TABLE IF NOT EXISTS `xxx_capacites` (
  `capacite` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `limitee` tinyint(1) DEFAULT NULL,
  `sort` tinyint(1) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_capacites_voies`
--

DROP TABLE IF EXISTS `xxx_capacites_voies`;
CREATE TABLE IF NOT EXISTS `xxx_capacites_voies` (
  `voie` varchar(20) NOT NULL,
  `rang` varchar(1) NOT NULL,
  `capacite` varchar(20) NOT NULL,
  PRIMARY KEY (`voie`,`rang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_familles`
--

DROP TABLE IF EXISTS `xxx_familles`;
CREATE TABLE IF NOT EXISTS `xxx_familles` (
  `famille` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`famille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_profils`
--

DROP TABLE IF EXISTS `xxx_profils`;
CREATE TABLE IF NOT EXISTS `xxx_profils` (
  `profil` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `famille` varchar(20) NOT NULL,
  `type` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`profil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_types_capacite`
--

DROP TABLE IF EXISTS `xxx_types_capacite`;
CREATE TABLE IF NOT EXISTS `xxx_types_capacite` (
  `type_capacite` varchar(5) NOT NULL,
  `type_capacite_intitule` varchar(50) NOT NULL,
  PRIMARY KEY (`type_capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_types_voie`
--

DROP TABLE IF EXISTS `xxx_types_voie`;
CREATE TABLE IF NOT EXISTS `xxx_types_voie` (
  `type_voie` varchar(5) NOT NULL,
  `type_voie_intitule` varchar(50) NOT NULL,
  PRIMARY KEY (`type_voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_voies`
--

DROP TABLE IF EXISTS `xxx_voies`;
CREATE TABLE IF NOT EXISTS `xxx_voies` (
  `voie` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `notes` varchar(1024) NOT NULL,
  `type` varchar(5) DEFAULT NULL,
  `pfx_deladu` char(1) NOT NULL,
  PRIMARY KEY (`voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `xxx_voies_profils`
--

DROP TABLE IF EXISTS `xxx_voies_profils`;
CREATE TABLE IF NOT EXISTS `xxx_voies_profils` (
  `profil` varchar(20) NOT NULL,
  `voie` varchar(20) NOT NULL,
  PRIMARY KEY (`profil`,`voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
