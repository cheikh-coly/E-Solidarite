-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 22 juin 2025 à 17:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `e-solidarite`
--

-- --------------------------------------------------------

--
-- Structure de la table `demandes_logement`
--

CREATE TABLE `demandes_logement` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `type_logement` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `justificatif` varchar(255) DEFAULT NULL,
  `statut` enum('en attente','validé','rejeté') DEFAULT 'en attente',
  `date_soumission` datetime DEFAULT current_timestamp(),
  `objectif_montant` int(11) DEFAULT 0,
  `montant_collecte` int(11) DEFAULT 0,
  `date_limite` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes_logement`
--

INSERT INTO `demandes_logement` (`id`, `utilisateur_id`, `type_logement`, `description`, `justificatif`, `statut`, `date_soumission`, `objectif_montant`, `montant_collecte`, `date_limite`) VALUES
(6, 1, 'ma maison a ete emporte par un ouragan', 'dljcvljvckldfkjl', '', 'en attente', '2025-06-22 15:00:13', 5000000, 0, '2025-08-24');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_sante`
--

CREATE TABLE `demandes_sante` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `type_soin` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `justificatif` varchar(255) DEFAULT NULL,
  `statut` enum('en attente','validé','rejeté') DEFAULT 'en attente',
  `date_soumission` datetime DEFAULT current_timestamp(),
  `montant_collecte` int(11) DEFAULT 0,
  `objectif` int(11) DEFAULT 0,
  `date_limite` date DEFAULT NULL,
  `objectif_montant` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes_sante`
--

INSERT INTO `demandes_sante` (`id`, `utilisateur_id`, `type_soin`, `description`, `justificatif`, `statut`, `date_soumission`, `montant_collecte`, `objectif`, `date_limite`, `objectif_montant`) VALUES
(4, 1, 'interventions chirurgicale', 'niddvhknkvdkhvkvdkjvdkn', '6852dfc119b25_receive.png', 'validé', '2025-06-18 15:48:17', 0, 0, NULL, 0),
(5, 1, 'interventions chirurgicale', 'niddvhknkvdkhvkvdkjvdkn', '6852e02c4a67e_receive.png', '', '2025-06-18 15:50:04', 0, 0, NULL, 0),
(6, 1, 'idfhdfhjifkfg', 'dfknfdnkdfkdvkjdvnkdv,dvjfv', '68538992328fc_ChatGPT Image 14 juin 2025, 00_44_36.png', 'rejeté', '2025-06-19 03:52:50', 0, 0, NULL, 0),
(7, 1, 'interventions chirurgicale', 'nofdidfjdfjofggf', '685389faa091c_partenaire1.jpg', 'rejeté', '2025-06-19 03:54:34', 0, 0, NULL, 0),
(8, 1, 'interventions chirurgicale', 'nofdidfjdfjofggf', '68538a235c2c1_partenaire1.jpg', 'validé', '2025-06-19 03:55:15', 10000, 0, NULL, 0),
(9, 1, ',lfgnkgfjfj', 'fnkfnfgjofgkoj', '68538a321f06d_partenaire1.jpg', 'validé', '2025-06-19 03:55:30', 5000, 0, NULL, 0),
(10, 5, 'Consultation ophtalmologique', 'J\'ai une vision floue depuis plusieurs semaines. J\'ai besoin de consulter un ophtalmologue pour un diagnostic et peut-être des lunettes.', '685813ef2aeb0_téléchargement.jpg', 'rejeté', '2025-06-22 14:32:15', 0, 250000, '2025-09-14', 0),
(11, 5, 'Consultation ophtalmologique', 'J\'ai une vision floue depuis plusieurs semaines. J\'ai besoin de consulter un ophtalmologue pour un diagnostic et peut-être des lunettes.', '68581530bd414_téléchargement.jpg', 'validé', '2025-06-22 14:37:36', 2000, 250000, '2025-09-14', 0),
(12, 0, 'fgfggnydt', 'rtjyjrtrtdtrj', '', 'en attente', '2025-06-22 14:56:28', 0, 365354, '2025-06-27', 0),
(13, 1, 'fgfggnydt', 'rtjyjrtrtdtrj', '', 'rejeté', '2025-06-22 14:58:17', 0, 365354, '2025-06-27', 0),
(14, 1, 'bn ,bnc,,cvb', 'fgxngn,fgcvxfg', '', 'rejeté', '2025-06-22 14:58:39', 0, 2332121, '2025-07-02', 0);

-- --------------------------------------------------------

--
-- Structure de la table `dons`
--

CREATE TABLE `dons` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `date_don` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dons`
--

INSERT INTO `dons` (`id`, `utilisateur_id`, `projet_id`, `montant`, `date_don`) VALUES
(1, 3, 1, 5000.00, '2025-06-18 13:51:28'),
(2, 3, 1, 5000.00, '2025-06-18 13:56:35'),
(4, 3, 1, 5000.00, '2025-06-18 19:44:30'),
(5, 3, 1, 5000.00, '2025-06-19 03:50:30'),
(6, 3, 2, 500000.00, '2025-06-20 18:46:05'),
(7, 3, 3, 20000.00, '2025-06-20 18:47:57'),
(8, 4, 2, 2000.00, '2025-06-21 04:15:51'),
(9, 4, 6, 2000.00, '2025-06-22 12:00:22'),
(10, 4, 1, 2000.00, '2025-06-22 12:17:18'),
(11, 4, 1, 2000.00, '2025-06-22 12:35:54'),
(12, 4, 1, 5000.00, '2025-06-22 12:39:18'),
(13, 4, 1, 5000.00, '2025-06-22 12:47:24'),
(14, 4, 6, 5000.00, '2025-06-22 13:15:18');

-- --------------------------------------------------------

--
-- Structure de la table `dons_logement`
--

CREATE TABLE `dons_logement` (
  `id` int(11) NOT NULL,
  `demande_logement_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_don` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dons_materiels`
--

CREATE TABLE `dons_materiels` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `nom_objet` varchar(100) DEFAULT NULL,
  `etat_objet` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_objet` varchar(255) DEFAULT NULL,
  `statut` enum('en attente','accepté','refusé') DEFAULT 'en attente',
  `date_soumission` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dons_materiels`
--

INSERT INTO `dons_materiels` (`id`, `utilisateur_id`, `nom_objet`, `etat_objet`, `description`, `image_objet`, `statut`, `date_soumission`) VALUES
(1, NULL, 'fourchette', 'bon etat', 'fkfdkkfdj dfjjdfodg dfjdfjdf ', '6852cbffd987d_hands.png', 'en attente', '2025-06-18 14:23:59'),
(2, NULL, 'fourchette', 'bon etat', 'fkfdkkfdj dfjjdfodg dfjdfjdf ', '6852cca3571a3_hands.png', 'en attente', '2025-06-18 14:26:43'),
(3, NULL, 'paka', 'gjgfutfuy', 'khuhuijhh', '6852d1ba97d69_heart.png', 'en attente', '2025-06-18 14:48:26'),
(4, 3, 'paka', 'gjgfutfuy', 'khuhuijhh', '6852d1e1eb820_heart.png', '', '2025-06-18 14:49:05'),
(5, 3, 'mklklklkl', ' kk,,lk,', 'cm;vkcvkl', '6852db13d148d_heart.png', '', '2025-06-18 15:28:19'),
(6, 3, 'kjdfkfgjk', 'bknvbkjfgkj', 'dfnkfdnkfdkn', '6852dc1586283_hands.png', 'accepté', '2025-06-18 15:32:37');

-- --------------------------------------------------------

--
-- Structure de la table `dons_sante`
--

CREATE TABLE `dons_sante` (
  `id` int(11) NOT NULL,
  `demande_sante_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_don` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `dons_sante`
--

INSERT INTO `dons_sante` (`id`, `demande_sante_id`, `utilisateur_id`, `montant`, `date_don`) VALUES
(1, 4, 3, 5000.00, '2025-06-18 17:19:11'),
(2, 4, 3, 5000.00, '2025-06-18 19:53:24'),
(3, 8, 4, 5000.00, '2025-06-22 12:58:48'),
(4, 9, 4, 5000.00, '2025-06-22 13:16:16'),
(5, 8, 4, 5000.00, '2025-06-22 13:17:03'),
(7, 11, 4, 2000.00, '2025-06-22 14:49:51');

-- --------------------------------------------------------

--
-- Structure de la table `messages_contact`
--

CREATE TABLE `messages_contact` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages_contact`
--

INSERT INTO `messages_contact` (`id`, `nom`, `email`, `sujet`, `message`, `date_envoi`) VALUES
(1, 'momo', 'momokarathiam@gmail.com', 'je suis interresse', 'votr projet me facine je veux apporter mon soutien', '2025-06-20 18:34:32');

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `objectif` decimal(10,2) DEFAULT NULL,
  `montant_collecte` decimal(10,2) DEFAULT 0.00,
  `statut` enum('en attente','validé','rejeté') DEFAULT 'en attente',
  `date_creation` datetime DEFAULT current_timestamp(),
  `utilisateur_id` int(11) DEFAULT NULL,
  `date_limite` date DEFAULT NULL,
  `cloture` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `titre`, `description`, `objectif`, `montant_collecte`, `statut`, `date_creation`, `utilisateur_id`, `date_limite`, `cloture`) VALUES
(1, 'je suis malade', ',vkjdkhfdknvdnk cvnkvjhvdjbvjiv  vvfjfvjv j', 500000.00, 10000.00, 'validé', '2025-06-18 13:36:01', 1, NULL, 0),
(2, 'jfjfgjofgjofgogfko', 'b,bjogogkghk', 5000000.00, 0.00, 'validé', '2025-06-19 03:55:49', 1, NULL, 0),
(3, 'f,ggfgfogfjogfjofg', 'f,gfjogfjogfkfgkgfbo,bgkp', 5001554.00, 0.00, 'validé', '2025-06-19 03:56:05', 1, NULL, 0),
(4, 'f,ggfgfogfjogfjofg', 'f,gfjogfjogfkfgkgfbo,bgkp', 5001554.00, 0.00, 'validé', '2025-06-19 03:57:15', 1, NULL, 0),
(6, 'Soutien aux enfants atteints de maladies rares', 'je suis Soutien aux enfants atteints de maladies rares\r\nSoutien aux enfants atteints de maladies rares\r\nSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies raresSoutien aux enfants atteints de maladies rares', 50000.00, 5000.00, 'validé', '2025-06-20 19:39:56', 1, '2025-12-15', 0);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` enum('admin','donateur','beneficiaire') DEFAULT 'donateur',
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `date_inscription`) VALUES
(1, 'mamadou thiam', 'momokarathiam@gmail.com', '$2y$10$pv3/VdktQenuNH7vI9Xplex75NqpDOdjP8QdF38HoJH5Z4K73ZQVK', 'beneficiaire', '2025-06-18 13:19:10'),
(2, 'momo', 'mamadou.thiam16@unchk.edu.sn', '$2y$10$IfyJGhPK8Ssi5N2wmUpHFOHN1Lbqh55Y/UswRIoxnoimAuAR4ykK.', 'admin', '2025-06-18 13:37:22'),
(3, 'modou niokhoro', 'monsieur@gmail.com', '$2y$10$E1v1AK5muChtebU5QyBCOeZ.iChub15s7CkAGunBsKI2tHMJT6h3C', 'donateur', '2025-06-18 13:47:28'),
(4, 'momo', 'momothiam@gmail.com', '$2y$10$5ueGfWXJl1eVtSVzZT0SiuRDlbXq3soScLVlG/6Q7w68qL4Qr8wFC', 'donateur', '2025-06-21 03:58:43'),
(5, 'Ndèye Astou Fall Badji', 'ndeyeastoufall.badji@unchk.edu.sn', '$2y$10$iJQxiaUYBTce0Oct5zpwa.wpBl7PxhdcwhqT1GiNzLeYXesIL33Zm', 'beneficiaire', '2025-06-22 12:54:32'),
(6, 'Thiam', 'bimbo@gmail.com', '$2y$10$FfYD00GDaaPKF8rCjaaKZ.iwHF/AfYyDjN7RsTaw67IyfF3NR9sf6', 'admin', '2025-06-22 15:31:57');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `demandes_logement`
--
ALTER TABLE `demandes_logement`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes_sante`
--
ALTER TABLE `demandes_sante`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `dons`
--
ALTER TABLE `dons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Index pour la table `dons_logement`
--
ALTER TABLE `dons_logement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_logement_id` (`demande_logement_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `dons_materiels`
--
ALTER TABLE `dons_materiels`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `dons_sante`
--
ALTER TABLE `dons_sante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_sante_id` (`demande_sante_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `demandes_logement`
--
ALTER TABLE `demandes_logement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `demandes_sante`
--
ALTER TABLE `demandes_sante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `dons`
--
ALTER TABLE `dons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `dons_logement`
--
ALTER TABLE `dons_logement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `dons_materiels`
--
ALTER TABLE `dons_materiels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `dons_sante`
--
ALTER TABLE `dons_sante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `dons`
--
ALTER TABLE `dons`
  ADD CONSTRAINT `dons_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `dons_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`);

--
-- Contraintes pour la table `dons_logement`
--
ALTER TABLE `dons_logement`
  ADD CONSTRAINT `dons_logement_ibfk_1` FOREIGN KEY (`demande_logement_id`) REFERENCES `demandes_logement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dons_logement_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dons_sante`
--
ALTER TABLE `dons_sante`
  ADD CONSTRAINT `dons_sante_ibfk_1` FOREIGN KEY (`demande_sante_id`) REFERENCES `demandes_sante` (`id`),
  ADD CONSTRAINT `dons_sante_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `projets`
--
ALTER TABLE `projets`
  ADD CONSTRAINT `projets_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
