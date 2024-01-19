-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 19 jan. 2024 à 16:10
-- Version du serveur : 10.4.10-MariaDB
-- Version de PHP : 8.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `symtour`
--

-- --------------------------------------------------------

--
-- Structure de la table `band`
--

DROP TABLE IF EXISTS `band`;
CREATE TABLE IF NOT EXISTS `band` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `define_style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `music_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_48DFA2EBBC36F4F1` (`music_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `band`
--

INSERT INTO `band` (`id`, `name`, `logo`, `define_style`, `music_category_id`) VALUES
(3, 'Massive Laitue', 'https://www.graines-schletzer.com/146-large/laitue-dhiver-val-d-orge.jpg', 'brutal death salade', 13),
(10, 'GroupeTest', 'https://assets.turbologo.com/blog/en/2021/07/23102432/The-Rolling-Stones-logo.png', 'Progressif', 14),
(11, 'Massive Gluten', 'https://www.gravatar.com/avatar/d65a159d285f605ed6adcb615145694e?size=80&rating=g&default=retro', 'Trompette sur glace', 17),
(12, 'AC/WC', 'https://www.gem-equip.fr/530-large_default/pictogramme-wc.jpg', 'Old School', 2),
(13, 'Maiden Iron', 'https://www.amphitheatrecogeco.com/cache/1680x640/madeiniron_nouvelleweb--1-.jpg', 'heavy metal tribute', 13),
(14, 'Groupe4', 'https://dt2sdf0db8zob.cloudfront.net/wp-content/uploads/2019/09/Best-Band-Logos-and-How-to-Make-Your-Own-Music-Logo-image25.jpg', 'Glam', 2),
(15, 'LapinGarou', 'https://p1.storage.canalblog.com/19/34/344776/37814458.jpg', 'DiscoDance', 15);

-- --------------------------------------------------------

--
-- Structure de la table `band_info`
--

DROP TABLE IF EXISTS `band_info`;
CREATE TABLE IF NOT EXISTS `band_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `band_id_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_90BFD10C83A620CB` (`band_id_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `band_info`
--

INSERT INTO `band_info` (`id`, `band_id_id`, `country`, `region`, `department`, `city`, `email`, `phone`, `website`) VALUES
(1, 3, 'France', NULL, NULL, 'Marseille', NULL, NULL, NULL),
(2, 10, 'France', NULL, NULL, 'Bordeaux', NULL, NULL, NULL),
(3, 3, 'France', NULL, NULL, 'Paris', NULL, NULL, NULL),
(4, 12, 'France', NULL, NULL, 'Lyon', NULL, NULL, NULL),
(5, 13, 'France', NULL, NULL, 'Lille', NULL, NULL, NULL),
(6, 14, 'France', NULL, NULL, 'Nantes', NULL, NULL, NULL),
(7, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `band_member`
--

DROP TABLE IF EXISTS `band_member`;
CREATE TABLE IF NOT EXISTS `band_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `band_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT 19,
  `profil_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_89A1C7A949ABEB17` (`band_id`),
  KEY `IDX_89A1C7A9D60322AC` (`role_id`),
  KEY `IDX_89A1C7A9275ED078` (`profil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `band_member`
--

INSERT INTO `band_member` (`id`, `band_id`, `role_id`, `profil_id`) VALUES
(3, 3, 2, 2),
(4, 3, 4, 6),
(6, 10, 1, 7),
(7, 11, 12, 7),
(8, 12, 1, 7),
(9, 13, 1, 7),
(10, 14, 6, 7),
(11, 3, 3, 7),
(12, 3, 2, 7),
(13, 15, 16, 2);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_id` int(11) DEFAULT NULL,
  `band_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int(11) DEFAULT 3,
  PRIMARY KEY (`id`),
  KEY `IDX_3BAE0AA752AFCFD6` (`hall_id`),
  KEY `IDX_3BAE0AA749ABEB17` (`band_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `hall_id`, `band_id`, `date`, `status`) VALUES
(1, 5, 3, '2023-01-30', 3),
(2, 3, 3, '2024-03-29', 1),
(3, 5, 13, '2023-05-05', 1),
(4, 6, 13, '2024-02-14', 3),
(5, 4, 11, '2024-04-11', 3),
(6, 4, 13, '2024-08-05', 3),
(7, 3, 15, '2024-06-12', 3),
(8, 6, 3, '2024-11-28', 3),
(9, 4, 15, '2024-05-24', 3),
(10, 6, 10, '2024-03-30', 3),
(11, 5, 12, '2024-01-19', 3);

-- --------------------------------------------------------

--
-- Structure de la table `gender`
--

DROP TABLE IF EXISTS `gender`;
CREATE TABLE IF NOT EXISTS `gender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `gender`
--

INSERT INTO `gender` (`id`, `name`) VALUES
(1, 'Masculin'),
(2, 'Féminin'),
(3, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `hall`
--

DROP TABLE IF EXISTS `hall`;
CREATE TABLE IF NOT EXISTS `hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hall`
--

INSERT INTO `hall` (`id`, `name`, `logo`, `structure`) VALUES
(1, 'La Bouillote', NULL, 'Bar Live'),
(2, 'Totiyi', NULL, 'Salle concert'),
(3, 'L\'Arrosoir', 'https://cdn.manomano.com/media/edison/9/9/5/4/9954185bd4ed.jpg', 'Bar Live'),
(4, 'L\'Ornithorynque', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBqoZgt3GQth9WTBZzqDkm4rYf12PBzuBcTQ&usqp=CAU', 'Bar Live'),
(5, 'Les Deux béquilles', 'https://img.freepik.com/photos-premium/gros-plan-jambe-homme-platre-aide-bequilles-marchant_522160-756.jpg', 'Salle de concert'),
(6, 'YeahMan', 'https://images.squarespace-cdn.com/content/v1/634e6c1bdb147e0d4da2e74b/788d4515-ac50-4447-9101-b09dea1dad0a/Yeah%2C+Man_Logo+Sheet-06.png', 'Coffee Shop'),
(7, 'Elephant Bar', 'https://cdn.worldvectorlogo.com/logos/elephant-bar.svg', 'Bar Restaurant');

-- --------------------------------------------------------

--
-- Structure de la table `hall_info`
--

DROP TABLE IF EXISTS `hall_info`;
CREATE TABLE IF NOT EXISTS `hall_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7E5DC8D652AFCFD6` (`hall_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hall_info`
--

INSERT INTO `hall_info` (`id`, `hall_id`, `country`, `region`, `department`, `city`, `zip_code`, `email`, `phone`, `website`) VALUES
(1, 1, 'France', NULL, NULL, 'Parise', NULL, NULL, NULL, NULL),
(2, 2, 'France', NULL, NULL, 'Lyon', NULL, NULL, NULL, NULL),
(3, 3, 'France', NULL, NULL, 'Lyon', NULL, NULL, NULL, NULL),
(4, 4, 'France', NULL, NULL, 'Nantes', NULL, NULL, NULL, NULL),
(5, 5, 'France', 'Hauts-de-France', 'Nord', 'Lille', 59000, 'prod@lesdeuxbequilles.com', '0145247000', 'www.lesdeuxbequilles.fr'),
(6, 6, 'France', 'Nouvelle-Aquitaine', NULL, 'Bordeaux', NULL, NULL, NULL, NULL),
(7, 7, 'France', 'Rhône Alpes', 'Rhône', 'Lyon', 69001, 'elephantbar@gmail.com', '0638656565', 'elephantbar.com');

-- --------------------------------------------------------

--
-- Structure de la table `hall_member`
--

DROP TABLE IF EXISTS `hall_member`;
CREATE TABLE IF NOT EXISTS `hall_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_961DAC1E52AFCFD6` (`hall_id`),
  KEY `IDX_961DAC1ED60322AC` (`role_id`),
  KEY `IDX_961DAC1ECCFA12B8` (`profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hall_member`
--

INSERT INTO `hall_member` (`id`, `hall_id`, `role_id`, `profile_id`) VALUES
(1, 4, 1, 2),
(2, 5, 1, 7),
(3, 6, 1, 7),
(4, 3, 3, 3),
(5, 3, 1, 2),
(6, 7, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `hall_music_category`
--

DROP TABLE IF EXISTS `hall_music_category`;
CREATE TABLE IF NOT EXISTS `hall_music_category` (
  `hall_id` int(11) NOT NULL,
  `music_category_id` int(11) NOT NULL,
  PRIMARY KEY (`hall_id`,`music_category_id`),
  KEY `IDX_2D74AF852AFCFD6` (`hall_id`),
  KEY `IDX_2D74AF8BC36F4F1` (`music_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hall_music_category`
--

INSERT INTO `hall_music_category` (`hall_id`, `music_category_id`) VALUES
(1, 2),
(1, 3),
(1, 34),
(2, 16),
(3, 24),
(4, 1),
(4, 8),
(4, 10),
(4, 12),
(4, 13),
(4, 15),
(4, 20),
(4, 22),
(5, 1),
(5, 2),
(5, 3),
(5, 13),
(5, 14),
(5, 15),
(5, 20),
(5, 22),
(5, 24),
(5, 34),
(6, 3),
(6, 4),
(6, 10),
(6, 11),
(6, 12),
(6, 22),
(6, 24),
(7, 4),
(7, 5),
(7, 6),
(7, 15),
(7, 16),
(7, 17),
(7, 22),
(7, 24);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `music_category`
--

DROP TABLE IF EXISTS `music_category`;
CREATE TABLE IF NOT EXISTS `music_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `music_category`
--

INSERT INTO `music_category` (`id`, `category`) VALUES
(1, 'Pop'),
(2, 'Rock'),
(3, 'Hip-hop / Rap'),
(4, 'Jazz'),
(5, 'Blues'),
(6, 'Country'),
(8, 'Classique'),
(9, 'Folk'),
(10, 'Reggae'),
(11, 'Soul'),
(12, 'R&B (Rhythm and Blues)'),
(13, 'Metal'),
(14, 'Punk'),
(15, 'Funk'),
(16, 'Disco'),
(17, 'Alternative'),
(20, 'Variété'),
(22, 'World music'),
(24, 'Techno'),
(34, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

DROP TABLE IF EXISTS `profil`;
CREATE TABLE IF NOT EXISTS `profil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pseudo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E6D6B29779F37AE5` (`id_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profil`
--

INSERT INTO `profil` (`id`, `id_user_id`, `country`, `city`, `zip_code`, `description`, `picture`, `pseudo`) VALUES
(2, 6, 'France', 'Lyon', 69001, 'pouetpouet', 'https://i.pinimg.com/736x/16/37/8e/16378ec42ec358824857e954a38bfd16.jpg', 'Timmy'),
(3, 8, 'France', 'Marseill', 13000, 'J\'aime le pastis et la pétanque lol', 'data:image/jpeg;base64,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', 'Riboulito'),
(4, 9, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 11, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 12, 'France', 'Grenoble', 74000, 'j\'aime la fondu et le pâté en croute', 'data:image/jpeg;base64,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', 'LuluLaFondu');

-- --------------------------------------------------------

--
-- Structure de la table `role_band`
--

DROP TABLE IF EXISTS `role_band`;
CREATE TABLE IF NOT EXISTS `role_band` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_band`
--

INSERT INTO `role_band` (`id`, `role_name`) VALUES
(1, 'Not Defined'),
(2, 'Chant'),
(3, 'Guitare'),
(4, 'Basse'),
(5, 'Batterie'),
(6, 'Piano/Clavier'),
(7, 'Violon'),
(8, 'Violoncelle'),
(9, 'Saxophone'),
(10, 'Trompette'),
(11, 'Trombone'),
(12, 'Flûte traversière'),
(13, 'Clarinette'),
(14, 'Harmonica'),
(15, 'Accordéon'),
(16, 'Banjo'),
(17, 'Mandoline'),
(18, 'Platines'),
(19, 'Manager'),
(20, 'Booker');

-- --------------------------------------------------------

--
-- Structure de la table `role_hall`
--

DROP TABLE IF EXISTS `role_hall`;
CREATE TABLE IF NOT EXISTS `role_hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_hall`
--

INSERT INTO `role_hall` (`id`, `role_name`) VALUES
(1, 'Directeur/trice'),
(2, 'Manager/geuse'),
(3, 'Technicien/ne Son'),
(4, 'Technicien/ne Lumières'),
(5, 'Programmateur/trice musical'),
(6, 'Not Defined');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `roles`) VALUES
(6, 'tim@legoat.com', '$2y$13$VclppSKvPgR6JgilHV0A9e74RHaRhlG9tE0GEkXb1uWeLOb/BCG2m', '[\"ROLE_USER\"]'),
(7, 'diego.mazenc@gmail.com', '$2y$13$UPWZN8HkEBy3zt3vYycwgOWnaIhbtky5hpc8mNqRei46BBgEiKya.', '[\"ROLE_ADMIN\"]'),
(8, 'jeanmarque@riboulet.com', '$2y$13$G.zQjSNQbV9q3O6/2FRoAOrfSsXXRUH2eWuK7YJvYKsyw8uOuD1K2', '[]'),
(9, 'pepouni@rufus.com', '$2y$13$vqsryUrPAy8rUqUeXKFmOuwaTyC/RnweDWOpiNoFHai63euS9G9QO', '[]'),
(10, 'user@user.com', '$2y$13$AcO6QkOgmt1x7B.1CHUxOOoc8soCSKYXlZcft1w5QLnCY5CB6Vtuu', '[]'),
(11, 'user2@user.com', '$2y$13$buLYODkllrvWzDPWjUqqZOor38rBUsTXqLFQIc6z2l0unvCFIPABy', '[]'),
(12, 'lucas@legoat.com', '$2y$13$pfPaJaaH.1oCl4sMWnlKl.69rmbqBsjy4iicbxjIY/mD6gml6avl2', '[]');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `band`
--
ALTER TABLE `band`
  ADD CONSTRAINT `FK_48DFA2EBBC36F4F1` FOREIGN KEY (`music_category_id`) REFERENCES `music_category` (`id`);

--
-- Contraintes pour la table `band_info`
--
ALTER TABLE `band_info`
  ADD CONSTRAINT `FK_90BFD10C83A620CB` FOREIGN KEY (`band_id_id`) REFERENCES `band` (`id`);

--
-- Contraintes pour la table `band_member`
--
ALTER TABLE `band_member`
  ADD CONSTRAINT `FK_89A1C7A9275ED078` FOREIGN KEY (`profil_id`) REFERENCES `profil` (`id`),
  ADD CONSTRAINT `FK_89A1C7A949ABEB17` FOREIGN KEY (`band_id`) REFERENCES `band` (`id`),
  ADD CONSTRAINT `FK_89A1C7A9D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role_band` (`id`);

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `FK_3BAE0AA749ABEB17` FOREIGN KEY (`band_id`) REFERENCES `band` (`id`),
  ADD CONSTRAINT `FK_3BAE0AA752AFCFD6` FOREIGN KEY (`hall_id`) REFERENCES `hall` (`id`);

--
-- Contraintes pour la table `hall_info`
--
ALTER TABLE `hall_info`
  ADD CONSTRAINT `FK_7E5DC8D652AFCFD6` FOREIGN KEY (`hall_id`) REFERENCES `hall` (`id`);

--
-- Contraintes pour la table `hall_member`
--
ALTER TABLE `hall_member`
  ADD CONSTRAINT `FK_961DAC1E52AFCFD6` FOREIGN KEY (`hall_id`) REFERENCES `hall` (`id`),
  ADD CONSTRAINT `FK_961DAC1ECCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `profil` (`id`),
  ADD CONSTRAINT `FK_961DAC1ED60322AC` FOREIGN KEY (`role_id`) REFERENCES `role_hall` (`id`);

--
-- Contraintes pour la table `hall_music_category`
--
ALTER TABLE `hall_music_category`
  ADD CONSTRAINT `FK_2D74AF852AFCFD6` FOREIGN KEY (`hall_id`) REFERENCES `hall` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2D74AF8BC36F4F1` FOREIGN KEY (`music_category_id`) REFERENCES `music_category` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `FK_E6D6B29779F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
