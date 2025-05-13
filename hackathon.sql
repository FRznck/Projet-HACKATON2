-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 13 mai 2025 à 12:07
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hackathon`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidats`
--

CREATE TABLE `candidats` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) COLLATE cp1250_bin NOT NULL,
  `email` varchar(100) COLLATE cp1250_bin NOT NULL,
  `mot_de_passe` varchar(200) COLLATE cp1250_bin NOT NULL,
  `date_inscription` datetime NOT NULL,
  `profil_linkedin` varchar(300) COLLATE cp1250_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1250 COLLATE=cp1250_bin;

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id_candidature` int(11) NOT NULL,
  `id_candidat` int(11) DEFAULT NULL,
  `id_offre` int(11) DEFAULT NULL,
  `lettre_motivation` mediumtext COLLATE utf8mb4_unicode_ci,
  `date_candidature` date DEFAULT NULL,
  `statut` enum('En attente','Acceptée','Refusée','Entretien planifié') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_recruteur` int(11) NOT NULL,
  `cv_fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lettre_fichier` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id_candidature`, `id_candidat`, `id_offre`, `lettre_motivation`, `date_candidature`, `statut`, `id_recruteur`, `cv_fichier`, `lettre_fichier`, `id_utilisateur`) VALUES
(1, 0, 1, 'ug?g', '2025-04-05', 'En attente', 0, '', '', NULL),
(2, 0, 1, 'g_yfy_pg', '2025-04-05', 'En attente', 0, '', '', NULL),
(3, 0, 1, 'jhugp', '2025-04-05', 'En attente', 0, '', '', NULL),
(4, 0, 2, 'coucou', '2025-04-05', 'En attente', 0, '', '', NULL),
(5, 0, 3, 'gt', '2025-04-05', 'En attente', 0, '', '', NULL),
(6, 0, 1, 'uh?o', '2025-04-05', 'En attente', 0, '', '', NULL),
(7, 0, 1, 'bo', '2025-04-05', 'En attente', 0, '', '', NULL),
(8, 0, 4, 'kplvjvjvo?vj', '2025-04-05', 'En attente', 0, '', '', NULL),
(9, 0, 5, 'i?u', '2025-04-05', 'En attente', 0, '', '', NULL),
(10, 0, 21, 'kj?kih', '2025-04-10', '', 0, 'uploads/cv_1744306386_Certificate_GESTION_DES_CONFLITS_ET_DE_L_AGRESSION_PHYSIQUE_CK.pdf', 'uploads/lettre_1744306386_Certificate_GESTION_DES_CONFLITS_ET_DE_L_AGRESSION_PHYSIQUE_CK.pdf', NULL),
(11, 0, 22, 'k\r\njjp?=^p?lkmpk', '2025-04-10', '', 0, 'uploads/cv_1744315677_Professional CV Resume (6) (1).pdf', 'uploads/lettre_1744315677_PARCOURS_EQUIPIER_EXPERT_DEBUTANT_EN_FEVRIER_MARS_AVRIL_MAI_2025.pdf', NULL),
(12, 0, 16, 'oih_?hkohçhhuf(gfsrugg', '2025-04-10', 'En attente', 0, 'uploads/cv_1744316239_d17ff621ae2dc75ca4e0997bc0c77499633c0e14faef05966d249a3145b9f171.pdf', 'uploads/lettre_1744316239_comprendre-ma-fiche-de-paie.pdf', NULL),
(13, 0, 23, 'Bonjours j me permet de vous envoyer ...', '2025-04-15', '', 0, 'uploads/cv_1744717873_Professional CV Resume (6) (1).pdf', 'uploads/lettre_1744717873_le projet hackathon.pdf', NULL),
(14, 0, 17, 'jkifygmbo', '2025-04-15', 'En attente', 0, '', '', NULL),
(15, 2, 22, 'fd?eyf(edhug?jkhjibddq-rkoi*)*$pl,uftfd  dyg ', '2025-04-16', '', 0, '', '', NULL),
(16, 2, 21, 'uyçt?ef i^=)$\r\nhjgyuf-(ieu v ', '2025-04-16', '', 0, 'uploads/cv_1744835700_d17ff621ae2dc75ca4e0997bc0c77499633c0e14faef05966d249a3145b9f171.pdf', 'uploads/lettre_1744835700_Professional CV Resume (6) (1).pdf', NULL),
(17, 1, 21, 'k^yh??l!b,jv:hcchkgghhvl:kivyxdwd: !nkm', '2025-04-17', 'En attente', 0, 'uploads/cv_1744927636_SPRINT DATA - PROJET FINAL.pdf', 'uploads/lettre_1744927636_CV_2025-03-10_RECHERCHE%20D\'ALTERNANCE_EN%20DATA%20ANALYST.pdf', NULL),
(18, 17, 21, ':,ÖN\r\nb?vmcytlumviobpn?l', '2025-04-18', 'En attente', 0, 'uploads/cv_1744964557_Professional CV Resume (6) (1).pdf', 'uploads/lettre_1744964557_Professional CV Resume (6) (1).pdf', NULL),
(19, 17, 23, 'h^rp_-tu)^gçokhk', '2025-04-18', '', 0, 'uploads/cv_1744964627_Professional CV Resume (6) (1).pdf', 'uploads/lettre_1744964627_Professional CV Resume (6) (1).pdf', NULL),
(20, 2, 25, 'bonjour j\'aimerais rejoindre votre entreprise !', '2025-04-18', 'Refusée', 3, 'uploads/cv_1745008082_CV_2025-02-04_Franck-Arsène_KOUASSI.pdf', 'uploads/lettre_1745008082_CV_2025-02-04_Franck-Arsène_KOUASSI.pdf', NULL),
(21, 2, 26, 'non', '2025-04-19', 'Entretien planifié', 3, 'uploads/cv_1745100553_alternance-admin_cv.pdf', 'uploads/lettre_1745100553_alternance-admin_cv.pdf', NULL),
(22, 10, 26, 'fnr', '2025-04-21', 'En attente', 3, 'uploads/cv_1745253690_alternance-admin_cv.pdf', 'uploads/lettre_1745253690_alternance-admin_cv.pdf', NULL),
(23, 2, 27, 'non', '2025-04-27', 'Refusée', 4, 'uploads/cv_1745772298_alternance-admin_cv.pdf', 'uploads/lettre_1745772298_alternance-admin_cv.pdf', NULL),
(24, 9, 27, 'j\'aime cette entreprise', '2025-04-27', 'En attente', 4, 'uploads/cv_1745780772_alternance-admin_cv.pdf', 'uploads/lettre_1745780772_alternance-admin_cv.pdf', 9),
(25, 8, 27, 'Accecpté moi svp!!', '2025-04-27', 'En attente', 4, 'uploads/cv_1745782593_alternance-admin_cv.pdf', 'uploads/lettre_1745782593_alternance-admin_cv.pdf', 8),
(26, 2, 30, 'prenez moi svp !!!!!!!!!!!!', '2025-04-28', 'En attente', 4, 'uploads/cv_1745855289_alternance-admin_cv.pdf', 'uploads/lettre_1745855289_alternance-admin_cv.pdf', 2),
(27, 14, 30, 'prenez moi !!! ', '2025-05-06', 'Entretien planifié', 4, 'uploads/cv_1746573004_Titre du projet.pdf', 'uploads/lettre_1746573004_Titre du projet.pdf', 14),
(28, 14, 31, 'Je suis déterminé à rejoindre votre entreprise', '2025-05-11', 'En attente', 4, 'uploads/cv_1746963847_alternance-admin_cv.pdf', 'uploads/lettre_1746963847_lettre de motivation admin.pdf', 14),
(29, 21, 31, 'Motivé', '2025-05-11', 'En attente', 4, 'uploads/cv_1746979304_alternance-admin_cv.pdf', 'uploads/lettre_1746979304_alternance-admin_cv.pdf', 21),
(30, NULL, 31, NULL, NULL, NULL, 4, 'Chef_de_projet_jr_candidat2_alternance-admin_cv.pdf', '', 2),
(31, NULL, 32, NULL, NULL, NULL, 4, 'Merveilleux_!_candidat2_alternance-admin_cv.pdf', '', 2),
(32, NULL, 32, NULL, NULL, NULL, 4, 'Merveilleux_!_candidat14_ECE_PARIS_MEP_CRENEAUX-09-MAI.pdf', '', 14),
(33, 14, 32, 'x', '2025-05-11', 'En attente', 4, 'uploads/cv_1746986600_ECE_PARIS_MEP_CRENEAUX-09-MAI.pdf', 'uploads/lettre_1746986600_ECE_PARIS_MEP_CRENEAUX-09-MAI.pdf', 14),
(34, 22, 26, 'motivé à vous rejoindre', '2025-05-11', 'En attente', 3, 'uploads/cv_1746988772_alternance-admin_cv.pdf', 'uploads/lettre_1746988772_alternance-admin_cv.pdf', 22),
(35, NULL, 34, NULL, NULL, 'Refusée', 6, 'Chef_de_projet_jr_candidat22_alternance-admin_cv.pdf', '', 22),
(36, 2, 35, 'de', '2025-05-11', 'Acceptée', 4, 'uploads/cv_1746989189_alternance-admin_cv.pdf', 'uploads/lettre_1746989189_alternance-admin_cv.pdf', 2),
(37, 14, 34, 'Bonjour', '2025-05-11', 'En attente', 6, 'uploads/cv_1746989432_alternance-admin_cv.pdf', 'uploads/lettre_1746989432_alternance-admin_cv.pdf', 14),
(38, 14, 35, 'motivé !', '2025-05-11', 'Entretien planifié', 4, 'uploads/cv_1746989725_alternance-admin_cv.pdf', 'uploads/lettre_1746989725_alternance-admin_cv.pdf', 14),
(39, NULL, 36, NULL, NULL, 'Acceptée', 4, 'Chef_de_projet_jr_candidat14_alternance-admin_cv.pdf', '', 14),
(40, 14, 36, 'Motivé !', '2025-05-11', 'Acceptée', 4, 'uploads/cv_1746991739_alternance-admin_cv.pdf', 'uploads/lettre_1746991739_alternance-admin_cv.pdf', 14);

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE `entreprise` (
  `id` int(11) NOT NULL,
  `nom_entreprise` varchar(255) COLLATE cp1250_bin NOT NULL,
  `contact_rh` varchar(200) COLLATE cp1250_bin NOT NULL,
  `email_professionnel` varchar(300) COLLATE cp1250_bin NOT NULL,
  `telephone` varchar(50) COLLATE cp1250_bin NOT NULL,
  `mot_de_passe` varchar(300) COLLATE cp1250_bin NOT NULL,
  `secteur_activite` varchar(300) COLLATE cp1250_bin NOT NULL,
  `taille_entreprise` varchar(300) COLLATE cp1250_bin NOT NULL,
  `adresse_siege` text COLLATE cp1250_bin NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=cp1250 COLLATE=cp1250_bin;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id`, `nom_entreprise`, `contact_rh`, `email_professionnel`, `telephone`, `mot_de_passe`, `secteur_activite`, `taille_entreprise`, `adresse_siege`, `date_creation`) VALUES
(1, 'Capgemini', 'Kanouté', 'kanoutecoumba00@gmail.com', '0615420046', '$2y$10$bui2eQ6fYwvLUKDpHirvxO4fUwnpBZPA4I0QswTCtTJ07F2nsp3w2', 'IT', '120 000', 'St.-Denis Île-de-France', '0000-00-00 00:00:00'),
(2, 'Orange', 'Mima', 'orange@gmailcom', '0615420046', '$2y$10$6U.C5eWnodRaBrnFgKVBvu9KG/YzNYJtABZHh.6hnziFDWodXF2y6', 'Communication', '2005', 'St.-Denis Île-de-France', '2025-04-15 10:39:06'),
(3, 'CocaCola', 'Francky', 'coca.franck@gmail.com', '489195', '$2y$10$fca0CjMqxrEETf1LpCVry.qMm9pcxsghIGXV4KDaNnyxvsUeQamS2', 'commerce', '10', '152 AVENUE EMILE COSSONNEAU', '2025-04-18 19:29:01'),
(4, 'Orange', 'Bétyy', 'orange@mail.com', '0775719913', '$2y$10$VgkkzdeVInotcGiqcCgKG.mdd7px3pY9uiVD.5hmuo7cdUjQsZHNy', 'Télecom', '100+', '152 AVENUE EMILE COSSONNEAU', '2025-04-27 16:43:09'),
(5, 'Skills3mond', 'Francky', 'skills3mond@gmail.com', '0775719913', '$2y$10$BoBnC0ilY.xXnlwmOpOgHOQc3s0.idP9Ltp/gTI1MSdbXwxBvsE2C', 'IT', '51-200 employés', 'Paris', '2025-05-11 18:26:26'),
(6, 'Skills8mond', 'Bétyy', 'Skills8mond@gmail.com', '189707', '$2y$10$04AgJ5a5e4GmUgBrCDG/X.wO8yq2JMgixCUgwCiY0LEstgShV4r8S', 'TECH', '11-50 employés', 'Paris,16', '2025-05-11 18:41:27');

-- --------------------------------------------------------

--
-- Structure de la table `entretiens`
--

CREATE TABLE `entretiens` (
  `id_entretien` int(11) NOT NULL,
  `id_candidature` int(11) DEFAULT NULL,
  `date_entretien` datetime DEFAULT NULL,
  `lieu` varchar(255) COLLATE cp1250_bin DEFAULT NULL,
  `remarques` text COLLATE cp1250_bin
) ENGINE=MyISAM DEFAULT CHARSET=cp1250 COLLATE=cp1250_bin;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `id_expediteur` int(11) DEFAULT NULL,
  `id_destinataire` int(11) DEFAULT NULL,
  `contenu` mediumtext COLLATE utf8mb4_unicode_ci,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_recruteur` int(11) NOT NULL,
  `reponse` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_reponse` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notification`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'Le statut de votre candidature pour «Alternance - RH assistant» est désormais : Acceptée', 1, '2025-04-19 00:11:22'),
(2, 2, 'Le statut de votre candidature pour «Alternance - RH assistant» est désormais : Refusée', 1, '2025-04-19 01:10:22'),
(3, 2, 'Le statut de votre candidature pour «Alternance - Admin système réseaux» est désormais : Acceptée', 1, '2025-04-20 00:10:57'),
(4, 2, 'Le statut de votre candidature pour «Alternance - Admin système réseaux» est désormais : Entretien planifié', 1, '2025-04-20 00:11:54'),
(5, 2, 'Le statut de votre candidature pour «Alternance - Concepteur développeur d\'Application» est désormais : Acceptée', 1, '2025-04-27 19:11:13'),
(6, 2, 'Le statut de votre candidature pour «Alternance - Concepteur développeur d\'Application» est désormais : Refusée', 1, '2025-04-27 19:33:24'),
(7, 14, 'Le statut de votre candidature pour «Chef de projet Mak22» est désormais : Entretien planifié', 1, '2025-05-07 01:10:35'),
(8, 22, 'Le statut de votre candidature pour «Chef de projet jr» est désormais : Entretien planifié', 1, '2025-05-11 20:43:51'),
(9, 2, 'Le statut de votre candidature pour «de» est désormais : Acceptée', 1, '2025-05-11 20:46:51'),
(10, 22, 'Le statut de votre candidature pour «Chef de projet jr» est désormais : Refusée', 1, '2025-05-11 20:47:44'),
(11, 14, 'Le statut de votre candidature pour «Technicien support IT» est désormais : Acceptée', 1, '2025-05-11 20:56:56'),
(12, 14, 'Le statut de votre candidature pour «Technicien support IT» est désormais : Entretien planifié', 1, '2025-05-11 20:57:11'),
(13, 14, 'Le statut de votre candidature pour «Chef de projet jr» est désormais : Entretien planifié', 1, '2025-05-11 21:29:29'),
(14, 14, 'Le statut de votre candidature pour «Chef de projet jr» est désormais : Acceptée', 1, '2025-05-11 21:29:40');

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

CREATE TABLE `offres` (
  `id_offre` int(11) NOT NULL,
  `id_recruteur` int(11) DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci,
  `competences_requises` mediumtext COLLATE utf8mb4_unicode_ci,
  `date_publication` date DEFAULT NULL,
  `type_contrat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duree` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salaire` decimal(10,2) NOT NULL,
  `lieu` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_entreprise` int(11) DEFAULT NULL,
  `nom_entreprise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id_offre`, `id_recruteur`, `titre`, `description`, `competences_requises`, `date_publication`, `type_contrat`, `duree`, `salaire`, `lieu`, `id_entreprise`, `nom_entreprise`) VALUES
(21, 1, 'Dev web', 'jgo?(odfkot?gç', '2 année experience', '2025-04-09', 'Apprentissage', '7mois ', '975.00', 'paris', 0, NULL),
(22, 1, 'Dev web', 'kj?ugçu', '2 année experience  ', '2025-04-09', 'CDD', '7mois ', '975.00', 'paris', 0, NULL),
(15, 0, 'Dev web', 'nbiyv', '2 année experience', '2025-04-08', 'Stage', '7mois ', '975.00', 'paris', 0, NULL),
(16, 0, 'Dev web', 'mlk', '2 année experience', '2025-04-08', 'CDD', '7mois ', '975.00', 'paris', 0, NULL),
(17, 0, 'Dev web', ';,?', '2 année experience', '2025-04-08', 'CDD', '7mois ', '975.00', 'paris', 0, NULL),
(19, 1, 'Dev web', 'pnkn', '2 année experience', '2025-04-08', 'Stage', '7mois ', '975.00', 'paris', 0, NULL),
(25, 3, 'Alternance - RH assistant', 'viens postuler', 'bac +6', '2025-04-18', 'Apprentissage', '24 mois', '9000.00', 'Paris, 75', 0, NULL),
(36, 4, 'Chef de projet jr', 'Poste à pourvoir !', 'bac +6', '2025-05-11', 'Apprentissage', '24 mois', '7500.00', 'Paris, 75', 0, 'Orange Telecom');

-- --------------------------------------------------------

--
-- Structure de la table `temoignages`
--

CREATE TABLE `temoignages` (
  `id` int(11) NOT NULL,
  `titre` varchar(300) COLLATE cp1250_bin NOT NULL,
  `contenu` text COLLATE cp1250_bin NOT NULL,
  `image_url` varchar(300) COLLATE cp1250_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1250 COLLATE=cp1250_bin;

--
-- Déchargement des données de la table `temoignages`
--

INSERT INTO `temoignages` (`id`, `titre`, `contenu`, `image_url`) VALUES
(1, 'Témoignage', 'Grâce ? cette plateforme, j\'ai décroché un emploi de r?ve chez Google Inc. en moins de deux semaines !', 'uploads/'),
(2, 'Témoignage', 'Bonjour', 'uploads/'),
(3, 'Témoignage', 'Bonjour', 'uploads/'),
(4, 'Témoignage', 'coucou merci pour votre aide', 'uploads/Capture d\'écran 2025-04-17 180703.png');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `firebase_uid` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('candidat','admin') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profil_linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `competences` mediumtext COLLATE utf8mb4_unicode_ci,
  `cv_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(3200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_contrat` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disponibilite` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Non renseigné',
  `localisation` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT 'Non renseigné',
  `email_verified` tinyint(1) DEFAULT '0',
  `is_blocked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `firebase_uid`, `role`, `profil_linkedin`, `competences`, `cv_url`, `avatar`, `type_contrat`, `disponibilite`, `telephone`, `localisation`, `email_verified`, `is_blocked`) VALUES
(1, 'Kanouté', 'Coumba', 'kanoutecoumba00@gmail.com', '$2y$10$6lun46rrZOdZBEpHoS9c/eP.EyE/Qi9mHEesduGo6oqHCleZA8k1u', NULL, '', 'https://www.linkedin.com/in/coumba-kanout%C3%A9/', NULL, 'uploads/cv/Rapport Tâche du Membre 1 (Scraping des données)[1].pdf', 'uploads/profile_pictures/ghost.bmp', '', '', '', '', 0, 0),
(2, 'candidat1', 'candidat1', 'candidat1@gmail.com', '$2y$10$Achq.mC0XhN8Sovwov.z1u1xt29HrODEodouVpyhSXxeQtZ1bP/6q', NULL, NULL, 'https://www.jobteaser.com/fr', 'python', NULL, '681de30025cf7_ma_photo_profil2.png', 'Alternance', '', '0775719913', 'Noisy-le-grand', 0, 0),
(3, 'candidat2', 'candidat2', 'candidat2@gmail.com', '$2y$10$hjddbIjZCcD1ywV4r72/HewuALrlGJuU.oUBY8FT/41WBp9gEO0VG', NULL, NULL, 'http://localhost/projet-HACKATON-Coumba/frontend/index.php', 'office', '3.pdf', '3.png', 'Alternance', '2 mois', '0775719913', 'Noisy-le-grand', 0, 0),
(4, 'admin', 'admin', 'admin@gmail.com', '$2y$10$0ulHmT6Uw0pu0kz5h9tYkOPEvt/SEIkMbbqRnEV4RQX.mUBDb3Su.', NULL, 'admin', 'https://www.super-admin.com/fr', 'super admin ', '4.pdf', '4.png', 'CDI', 'immédiate', '0775719913', 'Noisy-le-grand', 0, 0),
(6, '', 'F\'K262', 'nguessan.leonce72@gmail.com', '', '4vtEUywD1TOSPY6xW1Blxs0r13C2', 'candidat', NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ5yM3mjoYBwkwmjL2iePhv6uZ0gRzzhp7LuF7rYf_W7VVH80QI=s96-c', 'Non spécifié', 'Non spécifié', 'Non renseigné', 'Non renseigné', 0, 0),
(8, 'candidat3', 'candidat3', 'candidat3@gmail.com', '$2y$10$UWmsmXd1DAvTC3VPSFBZmOnkotwVepBu.7poMVdTeLqn7o4KlVa8G', NULL, 'candidat', 'http://localhost/projet-HACKATON-Coumba/frontend/index.php', 'c++', '8.pdf', '8.png', 'CDI', 'immédiate', '0775719913', 'Noisy-le-grand', 0, 0),
(9, 'candidat707@gmail.comm', 'candidat707', 'candidat707@gmail.comm', '$2y$10$c6Rk5MElQZB81FXgbtK0VeNe8JntyfzAH6pfx.iTb2NqTnqfayM1y', NULL, NULL, 'https://www.jobteaser.com/fr', 'dh', '9.pdf', '9.png', 'Alternance', '1 mois', '0775719913', 'Noisy-le-grand', 0, 0),
(10, 'candidat1', 'candidat1', 'candidat84@gmail.com', '$2y$10$BYWCP6L4WfDEiQlPjrOItOrZBA7JShd0K9tfHH9e.qnP/.qGly5xm', NULL, NULL, 'http://localhost/projet-HACKATON-Coumba/frontend/index.php', 'z', '10.pdf', '10.png', 'CDI', 'immédiate', '0775719913', 'Noisy-le-grand', 0, 0),
(11, 'YAO FRANCK ARSENE', 'KOUASSI', 'marcial2@gmail.com', '$2y$10$3uQN59mWD/8yBa9zIQbzRO3h7JQZLsIU/NcnPPJPm8DUWvb3WM5fG', NULL, NULL, 'http://localhost/projet-HACKATON2/frontend/', 'c9', '11.pdf', '11.png', 'CDI', '1 mois', '0775719913', 'no', 0, 0),
(12, 'Ange', 'orth', 'angeorthi@gmail.com', '$2y$10$uRTliiaZRwBrZerp/UrL2OrdRYmjDlDFKT8OLJ5zxtmTEkGjwth9.', NULL, NULL, 'http://localhost/projet-HACKATON2/frontend/', 'cm', NULL, NULL, 'CDI', '', '0775719913', 'no', 0, 0),
(13, 'kanounou', 'kanounouagdyegdugeuf', 'kanou@gmail.com', '$2y$10$660Txob6WKZf4Np2kdKe5etc.cwwzmXiesEiR1v8vohWrMDP5I/yS', NULL, NULL, 'http://localhost/projet-HACKATON2/frontend/', 'Forte à manger', NULL, '680fa71b82d38_image chat.jpg', 'Alternance', '', '0775719913', 'no', 0, 0),
(14, 'KOUASSI', 'Franck', 'franckkouassi4038@gmail.com', '', '70SiY3KGymSJo9KMIu2SCBb1yYq1', 'candidat', 'https://www.google.fr/', 'c++/maths', NULL, '681ddd2d4352f_ma_photo_profil2.png', 'CDD', 'En recherche active', '78469716', 'Marseille, France', 0, 0),
(15, '', 'Jean', 'jeandubonheur4245@gmail.com', '', '9QouTS2s7zd0zkaH5KeQCPIXr8P2', 'candidat', NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJODtw8zyQ9vdAerIuC4ITy7CuDfe9JAeVS6Y-6ETsQjIF9FQ=s96-c', 'Non spécifié', 'Non spécifié', 'Non renseigné', 'Non renseigné', 0, 0),
(16, 'candidat 5', 'canidat', 'candidat5@gmail.com', '$2y$10$BGeZPxajOoF5vFH7v1CK1.DxiVsGDVuGgObWudTYsPonCPomcfRJm', NULL, NULL, 'https://www.jobteaser.com/fr', 'PHP', '16.pdf', '16.png', 'Alternance', 'immédiate', '0775719913', 'Mantes-la-Jolie, France', 0, 0),
(19, 'test', 'test99', 'ece@gmail.com', '$2y$10$3WffYU81n3z71RQWc7oHO.9.w.2jgEeUT/3/gS1eHreq1pJoocLfm', NULL, 'candidat', 'http://localhost/projet-HACKATON2/frontend/', 'ce', '19.pdf', '19.png', 'CDD', '2 mois', '0101541159', 'Noisy-le-Sec, France', 0, 0),
(20, 'KOUASSI', 'Franck', 'kouassi@gmail.com', '$2y$10$RkCNwiv1MTAuAoPFBDlZgOQprZMYgnkVMeJMDVWumph6vXWR/J/Iu', NULL, NULL, 'https://www.jobteaser.com/fr', 'c++', '20.pdf', '20.png', 'Alternance', 'immédiate', '0775719913', 'Noisy-le-Grand, France', 0, 0),
(21, 'Koné', 'Ariel', 'kone.ariel@gmail.com', '$2y$10$6twYNPBHKG22IWQ5Zvm89eB6/YHPGlT33nz/jEQsHYFBzlmKARzJm', NULL, NULL, 'https://www.jobteaser.com/fr', 'c++', NULL, '6820eb674ceef_ma_photo_profil2.png', 'Alternance', '', '0775719913', 'Normandie, France', 0, 1),
(22, 'Albert', 'Korusi', 'albert.koursi@gmail.com', '$2y$10$zA1WIQrUvPZNvWXblXzmjeGHEOvePZUm2FtpATzb7Q.4Kg11HpDxG', NULL, NULL, 'http://localhost/projet-HACKATON-Coumba/frontend/index.php', 'c++', NULL, '6820eebd0fb3a_ma_photo_profil2.png', 'CDI', '', '0775719913', 'Massy, France', 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id_candidature`),
  ADD KEY `id_candidat` (`id_candidat`),
  ADD KEY `id_offre` (`id_offre`),
  ADD KEY `fk_id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `entretiens`
--
ALTER TABLE `entretiens`
  ADD PRIMARY KEY (`id_entretien`),
  ADD KEY `id_candidature` (`id_candidature`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_expediteur` (`id_expediteur`),
  ADD KEY `id_destinataire` (`id_destinataire`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Index pour la table `offres`
--
ALTER TABLE `offres`
  ADD PRIMARY KEY (`id_offre`),
  ADD KEY `id_recruteur` (`id_recruteur`),
  ADD KEY `fk_entreprise` (`id_entreprise`);

--
-- Index pour la table `temoignages`
--
ALTER TABLE `temoignages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id_candidature` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `entreprise`
--
ALTER TABLE `entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `entretiens`
--
ALTER TABLE `entretiens`
  MODIFY `id_entretien` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `offres`
--
ALTER TABLE `offres`
  MODIFY `id_offre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `temoignages`
--
ALTER TABLE `temoignages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
