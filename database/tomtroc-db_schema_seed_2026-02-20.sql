-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 20 fév. 2026 à 11:58
-- Version du serveur : 8.2.0
-- Version de PHP : 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tomtroc`
--

-- Base de données
CREATE DATABASE IF NOT EXISTS tomtroc2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tomtroc2;

-- --------------------------------------------------------

--
-- Structure de la table `books`
--

CREATE TABLE `books` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('available','unavailable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `books`
--

INSERT INTO `books` (`id`, `user_id`, `title`, `author`, `description`, `status`, `photo_path`, `created_at`) VALUES
(1, 1, 'The Kinfolk Table', 'Nathan Williams', 'Un livre captivant autour de la cuisine et de la convivialité.', 'available', NULL, '2026-02-02 21:44:30'),
(2, 2, 'Esther', 'Alabaster', 'Roman intimiste.', 'available', NULL, '2026-02-02 21:44:30'),
(3, 1, 'Wabi Sabi', 'Beth Kempton', 'L’art de la simplicité.', 'unavailable', NULL, '2026-02-02 21:44:30'),
(4, 3, 'Milk & honey', 'Rupi Kaur', 'Poésie contemporaine.', 'available', NULL, '2026-02-02 21:44:30'),
(5, 1, 'L’Étranger', 'Albert Camus', 'Meursault, un homme détaché de tout, se retrouve entraîné dans un drame absurde qui interroge le sens de l’existence.', 'available', 'https://covers.openlibrary.org/b/id/10521258-L.jpg', '2026-02-05 18:11:37'),
(6, 1, '1984', 'George Orwell', 'Dans une société totalitaire où Big Brother surveille tout, Winston Smith tente de résister.', 'available', 'https://covers.openlibrary.org/b/id/7222246-L.jpg', '2026-02-05 18:11:37'),
(7, 1, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un conte poétique et philosophique sur l’enfance, l’amitié et l’essentiel invisible.', 'available', 'https://covers.openlibrary.org/b/id/10909258-L.jpg', '2026-02-05 18:11:37'),
(8, 2, 'Dune', 'Frank Herbert', 'Sur la planète désertique Arrakis, Paul Atréides affronte intrigues politiques et enjeux écologiques.', 'available', 'https://covers.openlibrary.org/b/id/8106661-L.jpg', '2026-02-05 18:11:37'),
(9, 2, 'Fondation', 'Isaac Asimov', 'Hari Seldon fonde une organisation destinée à préserver le savoir face à l’effondrement de l’Empire.', 'available', 'https://covers.openlibrary.org/b/id/8231990-L.jpg', '2026-02-05 18:11:37'),
(10, 2, 'Fahrenheit 451', 'Ray Bradbury', 'Dans un monde où les livres sont brûlés, un pompier commence à remettre le système en question.', 'available', 'https://covers.openlibrary.org/b/id/8235117-L.jpg', '2026-02-05 18:11:37'),
(11, 3, 'Le Seigneur des anneaux', 'J.R.R. Tolkien', 'Frodon hérite d’un anneau maléfique et part en quête pour sauver la Terre du Milieu.', 'available', 'uploads/book_69984971512880.11075664.jpg', '2026-02-05 18:11:37'),
(12, 3, 'Harry Potter à l’école des sorciers', 'J.K. Rowling', 'Harry découvre qu’il est sorcier et entre à l’école de Poudlard.', 'available', 'https://covers.openlibrary.org/b/id/7984916-L.jpg', '2026-02-05 18:11:37'),
(13, 3, 'La Horde du Contrevent', 'Alain Damasio', 'Une expédition progresse contre un vent absolu dans un univers brutal et poétique.', 'available', 'https://covers.openlibrary.org/b/id/10592034-L.jpg', '2026-02-05 18:11:37'),
(14, 1, 'Germinal', 'Émile Zola', 'La vie difficile des mineurs et la naissance d’une conscience collective.', 'available', 'https://covers.openlibrary.org/b/id/8231924-L.jpg', '2026-02-05 18:11:37'),
(15, 2, 'Le Rouge et le Noir', 'Stendhal', 'Julien Sorel cherche à s’élever socialement dans une société rigide.', 'available', 'https://covers.openlibrary.org/b/id/8235414-L.jpg', '2026-02-05 18:11:37'),
(16, 3, 'Bel-Ami', 'Guy de Maupassant', 'Georges Duroy utilise son charme pour gravir les échelons du Paris mondain.', 'available', 'https://covers.openlibrary.org/b/id/8234637-L.jpg', '2026-02-05 18:11:37'),
(17, 1, 'La Peste', 'Albert Camus', 'Une épidémie bouleverse la ville d’Oran et révèle la nature humaine.', 'available', 'https://covers.openlibrary.org/b/id/8232857-L.jpg', '2026-02-05 18:11:37'),
(18, 2, 'Le Nom de la rose', 'Umberto Eco', 'Une enquête médiévale mêlant érudition, foi et raison.', 'available', 'https://covers.openlibrary.org/b/id/8231996-L.jpg', '2026-02-05 18:11:37'),
(19, 3, 'Le Meilleur des mondes', 'Aldous Huxley', 'Une société futuriste où le bonheur est imposé.', 'available', 'https://covers.openlibrary.org/b/id/8231994-L.jpg', '2026-02-05 18:11:37'),
(20, 1, 'Crime et Châtiment', 'Fiodor Dostoïevski', 'La culpabilité et la rédemption d’un homme après un meurtre.', 'available', 'https://covers.openlibrary.org/b/id/8235119-L.jpg', '2026-02-05 18:11:37'),
(21, 2, 'Orgueil et Préjugés', 'Jane Austen', 'Elizabeth Bennet affronte les conventions sociales et l’amour.', 'available', 'https://covers.openlibrary.org/b/id/8235114-L.jpg', '2026-02-05 18:11:37'),
(22, 3, 'Le Parfum', 'Patrick Süskind', 'Un homme doté d’un odorat exceptionnel poursuit l’odeur parfaite.', 'available', 'https://covers.openlibrary.org/b/id/8234183-L.jpg', '2026-02-05 18:11:37'),
(23, 1, 'Shutter Island', 'Dennis Lehane', 'Une enquête psychologique troublante dans un hôpital isolé.', 'available', 'https://covers.openlibrary.org/b/id/8234715-L.jpg', '2026-02-05 18:11:37'),
(24, 2, 'Voyage au centre de la Terre', 'Jules Verne', 'Une expédition scientifique hors du commun.', 'available', 'https://covers.openlibrary.org/b/id/8235142-L.jpg', '2026-02-05 18:11:37');

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id` int UNSIGNED NOT NULL,
  `user_one_id` int UNSIGNED NOT NULL,
  `user_two_id` int UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`id`, `user_one_id`, `user_two_id`, `updated_at`, `created_at`) VALUES
(1, 1, 2, '2026-02-02 21:44:30', '2026-02-02 21:44:30'),
(2, 4, 1, '2026-02-16 20:05:20', '2026-02-04 19:16:05'),
(3, 4, 3, '2026-02-10 14:41:24', '2026-02-08 19:39:59'),
(4, 4, 2, '2026-02-15 20:08:50', '2026-02-12 22:29:38'),
(5, 1, 3, '2026-02-20 08:37:46', '2026-02-20 08:37:46'),
(6, 3, 2, '2026-02-20 12:41:20', '2026-02-20 12:41:20');

-- --------------------------------------------------------

--
-- Structure de la table `exchanges`
--

CREATE TABLE `exchanges` (
  `id` int UNSIGNED NOT NULL,
  `requester_id` int UNSIGNED NOT NULL,
  `owner_id` int UNSIGNED NOT NULL,
  `book_id` int UNSIGNED NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `exchanges`
--

INSERT INTO `exchanges` (`id`, `requester_id`, `owner_id`, `book_id`, `status`, `message`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 14, 'rejected', 'Bonjour, je souhaite échanger ce livre.', '2026-02-14 20:44:32', '2026-02-14 21:07:01'),
(2, 4, 1, 5, 'rejected', 'Bonjour, je souhaite échanger ce livre.', '2026-02-14 20:45:22', '2026-02-14 21:07:01'),
(3, 4, 2, 9, 'rejected', 'Bonjour, je souhaite échanger ce livre.', '2026-02-14 21:06:49', '2026-02-14 21:07:00'),
(4, 4, 1, 14, 'pending', 'Bonjour, je souhaite échanger ce livre.', '2026-02-14 21:07:11', '2026-02-14 21:07:11');

-- --------------------------------------------------------

--
-- Structure de la table `exchange_requests`
--

CREATE TABLE `exchange_requests` (
  `id` int NOT NULL,
  `book_id` int UNSIGNED NOT NULL,
  `requester_id` int UNSIGNED NOT NULL,
  `owner_id` int UNSIGNED NOT NULL,
  `message` text,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `exchange_requests`
--

INSERT INTO `exchange_requests` (`id`, `book_id`, `requester_id`, `owner_id`, `message`, `status`, `created_at`, `updated_at`) VALUES
(31, 14, 4, 1, 'Bonjour, je souhaite échanger ce livre.', 'pending', '2026-02-20 11:12:50', NULL),
(32, 10, 3, 2, 'Bonjour, je souhaite échanger votre livre \"Fahrenheit 451\".', 'pending', '2026-02-20 11:24:56', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `book_id` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `book_id`, `created_at`) VALUES
(29, 3, 10, '2026-02-20 11:24:58');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int UNSIGNED NOT NULL,
  `conversation_id` int UNSIGNED NOT NULL,
  `sender_id` int UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `is_read`, `read_at`, `created_at`) VALUES
(1, 1, 1, 'Bonjour !', 1, '2026-02-16 20:03:07', '2026-02-02 21:44:30'),
(2, 1, 2, 'Salut, tu es intéressé(e) par quel livre ?', 1, '2026-02-10 14:49:11', '2026-02-02 21:44:30'),
(3, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"The Kinfolk Table\".', 1, '2026-02-10 14:49:04', '2026-02-04 19:16:06'),
(4, 2, 4, 'merci pour votre intérêt', 1, '2026-02-10 14:49:04', '2026-02-04 19:25:08'),
(5, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"L’Étranger\".', 1, '2026-02-10 14:49:04', '2026-02-05 20:24:26'),
(6, 3, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Harry Potter à l’école des sorciers\".', 1, '2026-02-20 11:24:44', '2026-02-08 19:39:59'),
(7, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-08 19:40:05'),
(8, 2, 1, 'Message 1 envoyé par Alexlecture (non lu)', 1, '2026-02-08 20:32:01', '2026-02-08 20:31:51'),
(9, 2, 1, 'Message 2 envoyé par Alexlecture (non lu)', 1, '2026-02-08 20:32:01', '2026-02-08 20:31:51'),
(10, 2, 1, 'Message 3 envoyé par Alexlecture (non lu)', 1, '2026-02-08 20:32:01', '2026-02-08 20:31:51'),
(11, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"1984\".', 1, '2026-02-10 14:49:04', '2026-02-09 22:24:10'),
(12, 2, 4, 'Merci', 1, '2026-02-10 14:49:04', '2026-02-09 22:24:18'),
(13, 3, 4, 'Merci', 1, '2026-02-20 11:24:44', '2026-02-09 22:24:29'),
(14, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:46:57'),
(15, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:01'),
(16, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:05'),
(17, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:10'),
(18, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:11'),
(19, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:11'),
(20, 3, 4, 'hello', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:11'),
(21, 3, 4, 'ttt', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:17'),
(22, 3, 4, 'ttt', 1, '2026-02-20 11:24:44', '2026-02-09 22:47:46'),
(23, 3, 4, 'merci pour votre intérêt', 1, '2026-02-20 11:24:44', '2026-02-10 14:41:24'),
(24, 2, 1, 'cc', 1, '2026-02-10 14:54:10', '2026-02-10 14:53:49'),
(25, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"1984\".', 1, '2026-02-11 19:36:03', '2026-02-10 17:07:18'),
(26, 2, 1, 'reponse a ahmed', 1, '2026-02-11 19:36:39', '2026-02-11 19:36:16'),
(27, 4, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Fondation\".', 1, '2026-02-16 20:02:46', '2026-02-12 22:29:38'),
(28, 4, 4, 'reponse a ahmed', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:07'),
(29, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:12'),
(30, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:14'),
(31, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:14'),
(32, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:17'),
(33, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:20'),
(34, 4, 4, 'cc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:23'),
(35, 4, 4, 'ccc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:25'),
(36, 4, 4, 'ccc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:29'),
(37, 4, 4, 'ccc', 1, '2026-02-16 20:02:46', '2026-02-13 22:19:32'),
(38, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Shutter Island\".', 1, '2026-02-15 20:18:19', '2026-02-14 16:44:31'),
(39, 4, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Dune\".', 1, '2026-02-16 20:02:46', '2026-02-14 19:57:48'),
(40, 4, 4, 'echnage', 1, '2026-02-16 20:02:46', '2026-02-14 19:57:56'),
(41, 4, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Dune\".', 1, '2026-02-16 20:02:46', '2026-02-15 20:08:50'),
(42, 2, 1, 'hello', 1, '2026-02-16 20:04:46', '2026-02-16 19:03:25'),
(43, 2, 1, 'hello', 1, '2026-02-16 20:04:46', '2026-02-16 19:03:55'),
(44, 2, 1, 'ttt', 1, '2026-02-16 20:04:46', '2026-02-16 19:04:01'),
(45, 2, 4, 'Bonjour, je suis intéressé(e) par votre livre \"Le Petit Prince\".', 1, '2026-02-20 07:57:55', '2026-02-16 20:05:20'),
(46, 5, 1, 'Bonjour Sas634 !', 1, '2026-02-20 11:24:41', '2026-02-20 08:37:46'),
(47, 6, 3, 'Bonjour, je suis intéressé(e) par votre livre \"Fahrenheit 451\".', 0, NULL, '2026-02-20 12:41:20');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `link`, `is_read`, `created_at`, `read_at`) VALUES
(1, 2, 'exchange', 'Nouvelle demande d\'échange', '/echanges', 1, '2026-02-14 21:06:49', '2026-02-15 21:25:49'),
(2, 1, 'exchange', 'Nouvelle demande d\'échange', '/echanges', 1, '2026-02-14 21:07:11', '2026-02-15 21:17:13'),
(3, 1, 'exchange', 'Nouvelle demande d’échange reçue', '/echanges', 1, '2026-02-15 21:18:39', '2026-02-15 21:19:37'),
(4, 1, 'favorite', 'Livre ajouté à vos favoris', '/favoris', 1, '2026-02-15 21:18:39', '2026-02-15 21:19:37'),
(5, 1, 'message', 'Vous avez reçu un message', '/messagerie', 1, '2026-02-15 21:18:39', '2026-02-15 21:19:37'),
(6, 3, 'exchange', 'Harry Potter à l’école des sorciers', '/livre?id=12', 0, '2026-02-16 20:00:38', NULL),
(7, 2, 'exchange', 'Dune', '/livre?id=8', 1, '2026-02-16 20:01:30', '2026-02-16 20:03:11'),
(8, 2, 'favorite', 'test', '/livre?id=30', 1, '2026-02-16 20:03:50', '2026-02-16 20:04:15'),
(9, 1, 'message', 'Nouveau message', '/messagerie?c=2', 1, '2026-02-16 20:05:20', '2026-02-20 08:21:57'),
(10, 1, 'favorite', 'Fondation', '/livre?id=9', 1, '2026-02-20 07:58:16', '2026-02-20 08:21:57'),
(11, 1, 'favorite', 'Harry Potter à l’école des sorciers', '/livre?id=12', 1, '2026-02-20 08:23:40', '2026-02-20 08:24:34'),
(12, 2, 'exchange', 'Dune', '/livre?id=8', 0, '2026-02-20 08:30:02', NULL),
(13, 4, 'exchange', 'test livre ahmed', '/livre?id=25', 0, '2026-02-20 08:31:47', NULL),
(14, 2, 'exchange', 'Fondation', '/livre?id=9', 0, '2026-02-20 08:32:20', NULL),
(15, 2, 'exchange', 'Fondation', '/livre?id=9', 0, '2026-02-20 08:32:47', NULL),
(16, 3, 'exchange', 'Le Seigneur des anneaux', '/livre?id=11', 0, '2026-02-20 08:35:12', NULL),
(17, 3, 'message', 'Nouveau message', '/messagerie?c=5', 0, '2026-02-20 08:37:46', NULL),
(18, 2, 'exchange', 'Fondation', '/livre?id=9', 0, '2026-02-20 08:50:45', NULL),
(19, 2, 'exchange', 'Le Nom de la rose', '/livre?id=18', 0, '2026-02-20 08:53:29', NULL),
(20, 2, 'exchange', 'Le Rouge et le Noir', '/livre?id=15', 0, '2026-02-20 08:55:46', NULL),
(21, 3, 'exchange', 'Milk & honey', '/livre?id=4', 0, '2026-02-20 09:13:16', NULL),
(22, 1, 'exchange', 'L’Étranger', '/livre?id=5', 0, '2026-02-20 09:48:10', NULL),
(23, 2, 'exchange', 'Fondation', '/livre?id=9', 0, '2026-02-20 10:05:24', NULL),
(24, 1, 'exchange', 'Germinal', '/livre?id=14', 0, '2026-02-20 10:53:54', NULL),
(25, 1, 'exchange', 'Germinal', '/livre?id=14', 0, '2026-02-20 11:12:50', NULL),
(26, 2, 'exchange', 'Fahrenheit 451', '/livre?id=10', 0, '2026-02-20 11:24:56', NULL),
(27, 2, 'message', 'Nouveau message', '/messagerie?c=6', 0, '2026-02-20 12:41:20', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ratings`
--

CREATE TABLE `ratings` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `book_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `book_id`, `rating`, `created_at`, `updated_at`) VALUES
(1, 4, 14, 5, '2026-02-14 19:58:18', '2026-02-14 19:58:18'),
(3, 4, 5, 4, '2026-02-14 19:58:52', '2026-02-14 20:12:55'),
(4, 4, 9, 5, '2026-02-14 19:58:58', '2026-02-14 19:58:58'),
(6, 4, 8, 5, '2026-02-14 20:47:16', '2026-02-14 20:47:16'),
(8, 3, 10, 5, '2026-02-20 11:27:24', '2026-02-20 11:27:24');

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `target_type` enum('book','message') NOT NULL,
  `target_id` int UNSIGNED NOT NULL,
  `reason` varchar(50) NOT NULL,
  `comment` text,
  `status` enum('open','reviewed','closed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `target_type`, `target_id`, `reason`, `comment`, `status`, `created_at`) VALUES
(1, 4, 'book', 5, 'other', 'test alert', 'open', '2026-02-13 22:18:33'),
(2, 4, 'book', 8, 'offensive', '', 'open', '2026-02-14 20:47:26');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `pseudo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `password_hash`, `avatar_path`, `created_at`) VALUES
(1, 'Alexlecture', 'alex@mail.com', '$2a$12$dOscHsiohHOq.ng68N4yI.F/PS.ewgGtA2EH0MNK57jXQSzDEDEq6', 'uploads/book_6998348a0468a2.35021753.jpg', '2026-02-02 21:44:30'),
(2, 'Nathalire', 'nathalie@mail.com', '$2a$12$dOscHsiohHOq.ng68N4yI.F/PS.ewgGtA2EH0MNK57jXQSzDEDEq6', 'uploads/book_699834a9114015.85532697.jpg', '2026-02-02 21:44:30'),
(3, 'Sas634', 'sas@mail.com', '$2a$12$dOscHsiohHOq.ng68N4yI.F/PS.ewgGtA2EH0MNK57jXQSzDEDEq6', 'uploads/book_699836642fbfb2.58835909.png', '2026-02-02 21:44:30'),
(4, '@aboukrim', 'boukrimahmed@gmail.com', '$2a$12$dOscHsiohHOq.ng68N4yI.F/PS.ewgGtA2EH0MNK57jXQSzDEDEq6', 'uploads/book_699835a0d71822.02044667.jpg', '2026-02-04 16:58:33');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_books_user` (`user_id`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_author` (`author`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_pair` (`user_one_id`,`user_two_id`),
  ADD KEY `fk_conv_u2` (`user_two_id`);

--
-- Index pour la table `exchanges`
--
ALTER TABLE `exchanges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_book` (`book_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_book` (`book_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_fav` (`user_id`,`book_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_book` (`book_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg_conv_time` (`conversation_id`,`created_at`),
  ADD KEY `idx_messages_is_read` (`is_read`),
  ADD KEY `idx_messages_sender` (`sender_id`),
  ADD KEY `idx_messages_conversation` (`conversation_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Index pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_rating` (`user_id`,`book_id`),
  ADD KEY `idx_book` (`book_id`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_report` (`user_id`,`target_type`,`target_id`),
  ADD KEY `idx_user_date` (`user_id`,`created_at`),
  ADD KEY `idx_target` (`target_type`,`target_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `books`
--
ALTER TABLE `books`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `exchanges`
--
ALTER TABLE `exchanges`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_u1` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_u2` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exchanges`
--
ALTER TABLE `exchanges`
  ADD CONSTRAINT `fk_exchanges_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exchanges_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exchanges_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD CONSTRAINT `fk_er_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_er_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_er_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
