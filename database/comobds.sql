-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 04 juil. 2024 à 11:18
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `comob-db`
--

-- --------------------------------------------------------

--
-- Structure de la table `cox_capacites`
--

DROP TABLE IF EXISTS `cox_capacites`;
CREATE TABLE IF NOT EXISTS `cox_capacites` (
  `capacite` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `limitee` tinyint(1) DEFAULT NULL,
  `sort` tinyint(1) DEFAULT NULL,
  `type` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_capacites_voies`
--

DROP TABLE IF EXISTS `cox_capacites_voies`;
CREATE TABLE IF NOT EXISTS `cox_capacites_voies` (
  `voie` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `rang` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `capacite` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`voie`,`rang`),
  KEY `cox_capacites_voies_voie` (`voie`),
  KEY `cox_capacites_voies_capacite` (`capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_categories_equipement`
--

DROP TABLE IF EXISTS `cox_categories_equipement`;
CREATE TABLE IF NOT EXISTS `cox_categories_equipement` (
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `libelle` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `parent` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sequence` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `cox_fk_categorie_parente_idx` (`parent`),
  KEY `cox_categorie_sequence` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_categories_proprietes`
--

DROP TABLE IF EXISTS `cox_categories_proprietes`;
CREATE TABLE IF NOT EXISTS `cox_categories_proprietes` (
  `code_categorie` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `code_propriete` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`code_categorie`,`code_propriete`),
  KEY `cox_categories_proprietes_categorie` (`code_categorie`),
  KEY `cox_categories_proprietes_propriete` (`code_propriete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_equipement`
--

DROP TABLE IF EXISTS `cox_equipement`;
CREATE TABLE IF NOT EXISTS `cox_equipement` (
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `designation` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `categorie` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `sequence` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix` decimal(16,2) DEFAULT NULL,
  `notes` mediumtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`code`),
  KEY `cox_fk_categorie_equipement_idx` (`categorie`),
  KEY `cox_equipement_sequence` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_equipement_profils`
--

DROP TABLE IF EXISTS `cox_equipement_profils`;
CREATE TABLE IF NOT EXISTS `cox_equipement_profils` (
  `profil` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `sequence` tinyint NOT NULL,
  `equipement` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` tinyint(1) NOT NULL DEFAULT '1',
  `special` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`profil`,`sequence`),
  KEY `cox_equipement_profils_profil` (`profil`),
  KEY `cox_equipement_profils_equipement` (`equipement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_equipement_proprietes`
--

DROP TABLE IF EXISTS `cox_equipement_proprietes`;
CREATE TABLE IF NOT EXISTS `cox_equipement_proprietes` (
  `code_equipement` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `code_propriete` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `valeur` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`code_equipement`,`code_propriete`),
  KEY `cox_equipement_proprietes_equipement` (`code_equipement`),
  KEY `cox_equipement_proprietes_propriete` (`code_propriete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_familles`
--

DROP TABLE IF EXISTS `cox_familles`;
CREATE TABLE IF NOT EXISTS `cox_familles` (
  `famille` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`famille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_profils`
--

DROP TABLE IF EXISTS `cox_profils`;
CREATE TABLE IF NOT EXISTS `cox_profils` (
  `profil` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `famille` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `type` char(1) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `combat` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`profil`),
  KEY `cox_profils_famille` (`famille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_profils_maitrises`
--

DROP TABLE IF EXISTS `cox_profils_maitrises`;
CREATE TABLE IF NOT EXISTS `cox_profils_maitrises` (
  `profil` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `equipement` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`profil`,`equipement`),
  KEY `cox_profils_maitrises_profil` (`profil`),
  KEY `cox_profils_maitrises_equipement` (`equipement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_profils_traits`
--

DROP TABLE IF EXISTS `cox_profils_traits`;
CREATE TABLE IF NOT EXISTS `cox_profils_traits` (
  `profil` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `sequence` tinyint NOT NULL,
  `intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`profil`,`sequence`),
  KEY `cox_profils_traits_profil` (`profil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_proprietes_equipement`
--

DROP TABLE IF EXISTS `cox_proprietes_equipement`;
CREATE TABLE IF NOT EXISTS `cox_proprietes_equipement` (
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `defaut` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_races`
--

DROP TABLE IF EXISTS `cox_races`;
CREATE TABLE IF NOT EXISTS `cox_races` (
  `race` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mod_for` tinyint DEFAULT NULL,
  `mod_dex` tinyint DEFAULT NULL,
  `mod_con` tinyint DEFAULT NULL,
  `mod_int` tinyint DEFAULT NULL,
  `mod_sag` tinyint DEFAULT NULL,
  `mod_cha` tinyint DEFAULT NULL,
  `age_base` smallint DEFAULT NULL,
  `esperance_vie` smallint DEFAULT NULL,
  `taille_min` decimal(3,2) DEFAULT NULL,
  `taille_max` decimal(3,2) DEFAULT NULL,
  `poids_min` smallint DEFAULT NULL,
  `poids_max` smallint DEFAULT NULL,
  `type_race` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`race`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_races_capacites`
--

DROP TABLE IF EXISTS `cox_races_capacites`;
CREATE TABLE IF NOT EXISTS `cox_races_capacites` (
  `race` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `capacite` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`race`,`capacite`),
  KEY `cox_races_capacites_race` (`race`),
  KEY `cox_races_capacites_capacite` (`capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_races_traits`
--

DROP TABLE IF EXISTS `cox_races_traits`;
CREATE TABLE IF NOT EXISTS `cox_races_traits` (
  `race` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `sequence` tinyint NOT NULL,
  `intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`race`,`sequence`),
  KEY `cox_races_traits_race` (`race`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_types_capacite`
--

DROP TABLE IF EXISTS `cox_types_capacite`;
CREATE TABLE IF NOT EXISTS `cox_types_capacite` (
  `type_capacite` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `type_capacite_intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_capacite_config` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`type_capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_types_races`
--

DROP TABLE IF EXISTS `cox_types_races`;
CREATE TABLE IF NOT EXISTS `cox_types_races` (
  `type_race` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `type_race_intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_race_config` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`type_race`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_types_voie`
--

DROP TABLE IF EXISTS `cox_types_voie`;
CREATE TABLE IF NOT EXISTS `cox_types_voie` (
  `type_voie` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `type_voie_intitule` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_voie_config` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`type_voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_voies`
--

DROP TABLE IF EXISTS `cox_voies`;
CREATE TABLE IF NOT EXISTS `cox_voies` (
  `voie` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` varchar(1024) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pfx_deladu` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `equipement` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cox_voies_profils`
--

DROP TABLE IF EXISTS `cox_voies_profils`;
CREATE TABLE IF NOT EXISTS `cox_voies_profils` (
  `profil` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `voie` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`profil`,`voie`),
  KEY `cox_voies_profils_profil` (`profil`),
  KEY `cox_voies_profils_voie` (`voie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cox_capacites_voies`
--
ALTER TABLE `cox_capacites_voies`
  ADD CONSTRAINT `cox_fk_capacites_voies_capacite` FOREIGN KEY (`capacite`) REFERENCES `cox_capacites` (`capacite`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_capacites_voies_voie` FOREIGN KEY (`voie`) REFERENCES `cox_voies` (`voie`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_categories_equipement`
--
ALTER TABLE `cox_categories_equipement`
  ADD CONSTRAINT `cox_fk_categorie_parente` FOREIGN KEY (`parent`) REFERENCES `cox_categories_equipement` (`code`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_categories_proprietes`
--
ALTER TABLE `cox_categories_proprietes`
  ADD CONSTRAINT `cox_fk_categories_proprietes_categorie` FOREIGN KEY (`code_categorie`) REFERENCES `cox_categories_equipement` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_categories_proprietes_propriete` FOREIGN KEY (`code_propriete`) REFERENCES `cox_proprietes_equipement` (`code`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_equipement`
--
ALTER TABLE `cox_equipement`
  ADD CONSTRAINT `cox_fk_categorie_equipement` FOREIGN KEY (`categorie`) REFERENCES `cox_categories_equipement` (`code`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_equipement_profils`
--
ALTER TABLE `cox_equipement_profils`
  ADD CONSTRAINT `cox_fk_equipement_profils_equipement` FOREIGN KEY (`equipement`) REFERENCES `cox_equipement` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_equipement_profils_profil` FOREIGN KEY (`profil`) REFERENCES `cox_profils` (`profil`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_equipement_proprietes`
--
ALTER TABLE `cox_equipement_proprietes`
  ADD CONSTRAINT `cox_fk_equipement_proprietes_equipement` FOREIGN KEY (`code_equipement`) REFERENCES `cox_equipement` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_equipement_proprietes_propriete` FOREIGN KEY (`code_propriete`) REFERENCES `cox_proprietes_equipement` (`code`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_profils`
--
ALTER TABLE `cox_profils`
  ADD CONSTRAINT `cox_fk_profils_famille` FOREIGN KEY (`famille`) REFERENCES `cox_familles` (`famille`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_profils_maitrises`
--
ALTER TABLE `cox_profils_maitrises`
  ADD CONSTRAINT `cox_fk_profils_maitrises_equipement` FOREIGN KEY (`equipement`) REFERENCES `cox_equipement` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_profils_maitrises_profil` FOREIGN KEY (`profil`) REFERENCES `cox_profils` (`profil`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_profils_traits`
--
ALTER TABLE `cox_profils_traits`
  ADD CONSTRAINT `cox_fk_profils_traits_profil` FOREIGN KEY (`profil`) REFERENCES `cox_profils` (`profil`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_races_capacites`
--
ALTER TABLE `cox_races_capacites`
  ADD CONSTRAINT `cox_fk_races_capacites_capacite` FOREIGN KEY (`capacite`) REFERENCES `cox_capacites` (`capacite`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_races_capacites_race` FOREIGN KEY (`race`) REFERENCES `cox_races` (`race`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_races_traits`
--
ALTER TABLE `cox_races_traits`
  ADD CONSTRAINT `cox_fk_races_traits_race` FOREIGN KEY (`race`) REFERENCES `cox_races` (`race`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `cox_voies_profils`
--
ALTER TABLE `cox_voies_profils`
  ADD CONSTRAINT `cox_fk_voies_profils_profil` FOREIGN KEY (`profil`) REFERENCES `cox_profils` (`profil`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cox_fk_voies_profils_voie` FOREIGN KEY (`voie`) REFERENCES `cox_voies` (`voie`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
