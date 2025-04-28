-- Die Struktur der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: DoctrineMigrations\Version20250428113621

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40101 SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token_index` (`token`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `FK_58D184BCA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anmelden_bookings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `form_data` longtext NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_71DD97E7833D8F43` (`registration_id`),
  KEY `IDX_71DD97E7A76ED395` (`user_id`),
  KEY `IDX_71DD97E72B18554A` (`owner_user_id`),
  KEY `IDX_71DD97E75A75A473` (`owner_role_id`),
  KEY `IDX_71DD97E77D182D95` (`created_by_user_id`),
  KEY `IDX_71DD97E71A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_71DD97E71A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_71DD97E72B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_71DD97E75A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_71DD97E77D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_71DD97E7833D8F43` FOREIGN KEY (`registration_id`) REFERENCES `anmelden_registrations` (`id`),
  CONSTRAINT `FK_71DD97E7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anmelden_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `opens_at` datetime DEFAULT NULL,
  `closes_at` datetime DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_BBDA45192B18554A` (`owner_user_id`),
  KEY `IDX_BBDA45195A75A473` (`owner_role_id`),
  KEY `IDX_BBDA45197D182D95` (`created_by_user_id`),
  KEY `IDX_BBDA45191A04EF5A` (`last_modified_by_user_id`),
  KEY `opens_at_index` (`opens_at`),
  KEY `closes_at_index` (`closes_at`),
  CONSTRAINT `FK_BBDA45191A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_BBDA45192B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_BBDA45195A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_BBDA45197D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anmelden_registration_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `ident` varchar(255) NOT NULL,
  `index_within_registration` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `is_optional` tinyint(1) NOT NULL,
  `options` longtext NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_D8B1DD49833D8F43` (`registration_id`),
  KEY `IDX_D8B1DD492B18554A` (`owner_user_id`),
  KEY `IDX_D8B1DD495A75A473` (`owner_role_id`),
  KEY `IDX_D8B1DD497D182D95` (`created_by_user_id`),
  KEY `IDX_D8B1DD491A04EF5A` (`last_modified_by_user_id`),
  KEY `ident_index` (`ident`),
  CONSTRAINT `FK_D8B1DD491A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_D8B1DD492B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_D8B1DD495A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_D8B1DD497D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_D8B1DD49833D8F43` FOREIGN KEY (`registration_id`) REFERENCES `anmelden_registrations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `action` varchar(31) NOT NULL,
  `username` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address_timestamp_index` (`ip_address`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `counter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) DEFAULT NULL,
  `args` longtext DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `date_range` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date_range_page_index` (`date_range`,`page`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext DEFAULT NULL,
  `position` int(11) NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_4B73A4B52B18554A` (`owner_user_id`),
  KEY `IDX_4B73A4B55A75A473` (`owner_role_id`),
  KEY `IDX_4B73A4B57D182D95` (`created_by_user_id`),
  KEY `IDX_4B73A4B51A04EF5A` (`last_modified_by_user_id`),
  KEY `position_index` (`on_off`,`position`),
  CONSTRAINT `FK_4B73A4B51A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_4B73A4B52B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_4B73A4B55A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_4B73A4B57D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `karten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kartennr` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `jahr` varchar(255) DEFAULT NULL,
  `massstab` varchar(255) DEFAULT NULL,
  `ort` varchar(255) DEFAULT NULL,
  `zoom` int(11) DEFAULT NULL,
  `typ` varchar(255) DEFAULT NULL,
  `vorschau` varchar(255) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_57ED7BE12B18554A` (`owner_user_id`),
  KEY `IDX_57ED7BE15A75A473` (`owner_role_id`),
  KEY `IDX_57ED7BE17D182D95` (`created_by_user_id`),
  KEY `IDX_57ED7BE11A04EF5A` (`last_modified_by_user_id`),
  KEY `typ_index` (`on_off`,`typ`),
  CONSTRAINT `FK_57ED7BE11A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_57ED7BE12B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_57ED7BE15A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_57ED7BE17D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext DEFAULT NULL,
  `url` longtext DEFAULT NULL,
  `position` int(11) NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_D182A1182B18554A` (`owner_user_id`),
  KEY `IDX_D182A1185A75A473` (`owner_role_id`),
  KEY `IDX_D182A1187D182D95` (`created_by_user_id`),
  KEY `IDX_D182A1181A04EF5A` (`last_modified_by_user_id`),
  KEY `position_index` (`on_off`,`position`),
  CONSTRAINT `FK_D182A1181A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_D182A1182B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_D182A1185A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_D182A1187D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_user_id` int(11) DEFAULT NULL,
  `author_role_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `termin` int(11) NOT NULL,
  `published_date` date NOT NULL,
  `published_time` time DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 1,
  `title` longtext NOT NULL,
  `teaser` longtext DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image_ids` longtext DEFAULT NULL,
  `external_url` longtext DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `format` longtext NOT NULL,
  `tags` longtext NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_1DD39950E2544CD6` (`author_user_id`),
  KEY `IDX_1DD399509339BDEF` (`author_role_id`),
  KEY `IDX_1DD399502B18554A` (`owner_user_id`),
  KEY `IDX_1DD399505A75A473` (`owner_role_id`),
  KEY `IDX_1DD399507D182D95` (`created_by_user_id`),
  KEY `IDX_1DD399501A04EF5A` (`last_modified_by_user_id`),
  KEY `published_index` (`published_date`,`published_time`),
  CONSTRAINT `FK_1DD399501A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1DD399502B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1DD399505A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_1DD399507D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1DD399509339BDEF` FOREIGN KEY (`author_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_1DD39950E2544CD6` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6404 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `delivery_type` varchar(255) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `notification_type_args` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  KEY `notification_type_index` (`notification_type`),
  CONSTRAINT `FK_52C540C8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panini24` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `line1` varchar(255) NOT NULL,
  `line2` varchar(255) DEFAULT NULL,
  `association` varchar(255) DEFAULT NULL,
  `img_src` varchar(255) NOT NULL,
  `img_style` varchar(255) NOT NULL,
  `is_landscape` tinyint(1) NOT NULL,
  `has_top` tinyint(1) NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `infos` longtext NOT NULL,
  `birthdate` date DEFAULT NULL,
  `num_mispunches` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1254A2E52B18554A` (`owner_user_id`),
  KEY `IDX_1254A2E55A75A473` (`owner_role_id`),
  KEY `IDX_1254A2E57D182D95` (`created_by_user_id`),
  KEY `IDX_1254A2E51A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_1254A2E51A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1254A2E52B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1254A2E55A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_1254A2E57D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1013 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ident` varchar(31) NOT NULL,
  `position_within_category` int(11) NOT NULL,
  `question` longtext NOT NULL,
  `answer` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8ADC54D52B18554A` (`owner_user_id`),
  KEY `IDX_8ADC54D55A75A473` (`owner_role_id`),
  KEY `IDX_8ADC54D57D182D95` (`created_by_user_id`),
  KEY `IDX_8ADC54D51A04EF5A` (`last_modified_by_user_id`),
  KEY `IDX_8ADC54D512469DE2` (`category_id`),
  KEY `ident_index` (`on_off`,`ident`),
  KEY `category_position_index` (`on_off`,`category_id`,`position_within_category`),
  CONSTRAINT `FK_8ADC54D512469DE2` FOREIGN KEY (`category_id`) REFERENCES `question_categories` (`id`),
  CONSTRAINT `FK_8ADC54D51A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_8ADC54D52B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_8ADC54D55A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_8ADC54D57D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `position` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5D27D9E02B18554A` (`owner_user_id`),
  KEY `IDX_5D27D9E05A75A473` (`owner_role_id`),
  KEY `IDX_5D27D9E07D182D95` (`created_by_user_id`),
  KEY `IDX_5D27D9E01A04EF5A` (`last_modified_by_user_id`),
  KEY `position_index` (`on_off`,`position`),
  CONSTRAINT `FK_5D27D9E01A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_5D27D9E02B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_5D27D9E05A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_5D27D9E07D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_category_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_739C61542B18554A` (`owner_user_id`),
  KEY `IDX_739C61545A75A473` (`owner_role_id`),
  KEY `IDX_739C61547D182D95` (`created_by_user_id`),
  KEY `IDX_739C61541A04EF5A` (`last_modified_by_user_id`),
  KEY `name_index` (`name`),
  KEY `parent_category_index` (`parent_category_id`),
  CONSTRAINT `FK_739C61541A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_739C61542B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_739C61545A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_739C6154796A8F92` FOREIGN KEY (`parent_category_id`) REFERENCES `quiz_categories` (`id`),
  CONSTRAINT `FK_739C61547D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_skill` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_A24E9DF72B18554A` (`owner_user_id`),
  KEY `IDX_A24E9DF75A75A473` (`owner_role_id`),
  KEY `IDX_A24E9DF77D182D95` (`created_by_user_id`),
  KEY `IDX_A24E9DF71A04EF5A` (`last_modified_by_user_id`),
  KEY `name_index` (`name`),
  CONSTRAINT `FK_A24E9DF71A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A24E9DF72B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A24E9DF75A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_A24E9DF77D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_skills_categories` (
  `skill_id` bigint(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`skill_id`,`category_id`),
  KEY `IDX_7289B4265585C142` (`skill_id`),
  KEY `IDX_7289B42612469DE2` (`category_id`),
  CONSTRAINT `FK_7289B42612469DE2` FOREIGN KEY (`category_id`) REFERENCES `quiz_categories` (`id`),
  CONSTRAINT `FK_7289B4265585C142` FOREIGN KEY (`skill_id`) REFERENCES `quiz_skill` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_skill_levels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `skill_id` bigint(20) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `value` double NOT NULL,
  `recorded_at` datetime NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_6699B5F6A76ED395` (`user_id`),
  KEY `IDX_6699B5F65585C142` (`skill_id`),
  KEY `IDX_6699B5F62B18554A` (`owner_user_id`),
  KEY `IDX_6699B5F65A75A473` (`owner_role_id`),
  KEY `IDX_6699B5F67D182D95` (`created_by_user_id`),
  KEY `IDX_6699B5F61A04EF5A` (`last_modified_by_user_id`),
  KEY `user_skill_index` (`user_id`,`skill_id`),
  CONSTRAINT `FK_6699B5F61A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_6699B5F62B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_6699B5F65585C142` FOREIGN KEY (`skill_id`) REFERENCES `quiz_skill` (`id`),
  CONSTRAINT `FK_6699B5F65A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_6699B5F67D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_6699B5F6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` longtext NOT NULL,
  `old_username` longtext DEFAULT NULL,
  `name` longtext NOT NULL,
  `description` longtext NOT NULL COMMENT 'public',
  `parent_role` int(11) DEFAULT NULL,
  `index_within_parent` int(11) DEFAULT NULL COMMENT 'negative value: hide role',
  `featured_index` int(11) DEFAULT NULL,
  `can_have_child_roles` tinyint(1) NOT NULL DEFAULT 0,
  `guide` longtext NOT NULL COMMENT 'restricted access',
  `permissions` longtext NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_B63E2EC72B18554A` (`owner_user_id`),
  KEY `IDX_B63E2EC75A75A473` (`owner_role_id`),
  KEY `IDX_B63E2EC77D182D95` (`created_by_user_id`),
  KEY `IDX_B63E2EC71A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_B63E2EC71A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_B63E2EC72B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_B63E2EC75A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_B63E2EC77D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snippets` (
  `id` int(11) NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `text` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED21F5DC2B18554A` (`owner_user_id`),
  KEY `IDX_ED21F5DC5A75A473` (`owner_role_id`),
  KEY `IDX_ED21F5DC7D182D95` (`created_by_user_id`),
  KEY `IDX_ED21F5DC1A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_ED21F5DC1A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_ED21F5DC2B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_ED21F5DC5A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_ED21F5DC7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_events` (
  `solv_uid` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `kind` longtext NOT NULL,
  `day_night` longtext NOT NULL,
  `national` int(11) NOT NULL,
  `region` longtext NOT NULL,
  `type` longtext NOT NULL,
  `name` longtext NOT NULL,
  `link` longtext NOT NULL,
  `club` longtext NOT NULL,
  `map` longtext NOT NULL,
  `location` longtext NOT NULL,
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `deadline` date DEFAULT NULL,
  `entryportal` int(11) NOT NULL,
  `start_link` longtext DEFAULT NULL,
  `rank_link` longtext DEFAULT NULL,
  `last_modification` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`solv_uid`),
  KEY `date_index` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `same_as` int(11) DEFAULT NULL,
  `name` longtext NOT NULL,
  `birth_year` longtext NOT NULL,
  `domicile` longtext NOT NULL,
  `member` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `same_as_index` (`same_as`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person` int(11) NOT NULL,
  `event` int(11) NOT NULL,
  `class` varchar(15) NOT NULL,
  `rank` int(11) NOT NULL,
  `name` varchar(31) NOT NULL,
  `birth_year` varchar(3) NOT NULL,
  `domicile` varchar(31) NOT NULL,
  `club` varchar(31) NOT NULL,
  `result` int(11) NOT NULL,
  `splits` longtext NOT NULL,
  `finish_split` int(11) NOT NULL,
  `class_distance` int(11) NOT NULL,
  `class_elevation` int(11) NOT NULL,
  `class_control_count` int(11) NOT NULL,
  `class_competitor_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person_name_index` (`person`,`name`),
  KEY `event_index` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `strava_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_token` longtext NOT NULL,
  `expires_at` datetime NOT NULL,
  `refresh_token` longtext NOT NULL,
  `strava_user` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `FK_72D84739A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telegram_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `pin_expires_at` datetime DEFAULT NULL,
  `telegram_chat_id` varchar(255) DEFAULT NULL,
  `telegram_user_id` varchar(255) DEFAULT NULL,
  `telegram_chat_state` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `linked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pin_index` (`pin`),
  KEY `user_id_index` (`user_id`),
  KEY `telegram_user_id_index` (`telegram_user_id`),
  KEY `telegram_chat_id_index` (`telegram_chat_id`),
  CONSTRAINT `FK_CC49A25AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `title` longtext DEFAULT NULL,
  `go2ol` longtext DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `xkoord` int(11) DEFAULT NULL,
  `ykoord` int(11) DEFAULT NULL,
  `solv_uid` int(11) DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 0,
  `deadline` datetime DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `participants_registration_id` int(11) DEFAULT NULL,
  `volunteers_registration_id` int(11) DEFAULT NULL,
  `num_participants` int(11) DEFAULT NULL,
  `min_participants` int(11) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `num_volunteers` int(11) DEFAULT NULL,
  `min_volunteers` int(11) DEFAULT NULL,
  `max_volunteers` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `image_ids` longtext DEFAULT NULL,
  `from_template_id` int(11) DEFAULT NULL,
  `should_promote` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_168C0A8F2B18554A` (`owner_user_id`),
  KEY `IDX_168C0A8F5A75A473` (`owner_role_id`),
  KEY `IDX_168C0A8F7D182D95` (`created_by_user_id`),
  KEY `IDX_168C0A8F1A04EF5A` (`last_modified_by_user_id`),
  KEY `IDX_168C0A8F80299162` (`participants_registration_id`),
  KEY `IDX_168C0A8F6D54E666` (`volunteers_registration_id`),
  KEY `IDX_168C0A8F64D218E` (`location_id`),
  KEY `start_date_on_off_index` (`start_date`,`on_off`),
  KEY `IDX_168C0A8F9B953EDD` (`from_template_id`),
  CONSTRAINT `FK_168C0A8F1A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_168C0A8F2B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_168C0A8F5A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_168C0A8F64D218E` FOREIGN KEY (`location_id`) REFERENCES `termin_locations` (`id`),
  CONSTRAINT `FK_168C0A8F6D54E666` FOREIGN KEY (`volunteers_registration_id`) REFERENCES `anmelden_registrations` (`id`),
  CONSTRAINT `FK_168C0A8F7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_168C0A8F80299162` FOREIGN KEY (`participants_registration_id`) REFERENCES `anmelden_registrations` (`id`),
  CONSTRAINT `FK_168C0A8F9B953EDD` FOREIGN KEY (`from_template_id`) REFERENCES `termin_templates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `termin_id` int(11) NOT NULL,
  `language` varchar(7) DEFAULT NULL,
  `index` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `content` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E39736B2CA0B7C00` (`termin_id`),
  KEY `termin_language_index` (`termin_id`,`language`,`index`),
  CONSTRAINT `FK_E39736B2CA0B7C00` FOREIGN KEY (`termin_id`) REFERENCES `termine` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(127) NOT NULL,
  `details` longtext DEFAULT NULL,
  `icon` longtext DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ident` varchar(31) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A3B090E02B18554A` (`owner_user_id`),
  KEY `IDX_A3B090E05A75A473` (`owner_role_id`),
  KEY `IDX_A3B090E07D182D95` (`created_by_user_id`),
  KEY `IDX_A3B090E01A04EF5A` (`last_modified_by_user_id`),
  KEY `name_index` (`name`),
  KEY `ident_index` (`on_off`,`ident`),
  KEY `position_index` (`on_off`,`position`),
  CONSTRAINT `FK_A3B090E01A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A3B090E02B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A3B090E05A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_A3B090E07D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_label_map` (
  `termin_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`termin_id`,`label_id`),
  KEY `IDX_6A8B53A8CA0B7C00` (`termin_id`),
  KEY `IDX_6A8B53A833B92F39` (`label_id`),
  CONSTRAINT `FK_6A8B53A833B92F39` FOREIGN KEY (`label_id`) REFERENCES `termin_labels` (`id`),
  CONSTRAINT `FK_6A8B53A8CA0B7C00` FOREIGN KEY (`termin_id`) REFERENCES `termine` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `name` varchar(127) NOT NULL,
  `details` longtext DEFAULT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `image_ids` longtext DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_DA22EA1D2B18554A` (`owner_user_id`),
  KEY `IDX_DA22EA1D5A75A473` (`owner_role_id`),
  KEY `IDX_DA22EA1D7D182D95` (`created_by_user_id`),
  KEY `IDX_DA22EA1D1A04EF5A` (`last_modified_by_user_id`),
  KEY `name_index` (`name`),
  CONSTRAINT `FK_DA22EA1D1A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_DA22EA1D2B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_DA22EA1D5A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_DA22EA1D7D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `termin_id` int(11) NOT NULL,
  `recipient_user_id` int(11) DEFAULT NULL,
  `recipient_role_id` int(11) DEFAULT NULL,
  `fires_at` datetime NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext DEFAULT NULL,
  `recipient_termin_owners` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_termin_volunteers` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_termin_participants` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_23876048B15EFB97` (`recipient_user_id`),
  KEY `IDX_23876048C0330AAE` (`recipient_role_id`),
  KEY `termin_index` (`termin_id`),
  KEY `fires_at_index` (`fires_at`),
  CONSTRAINT `FK_23876048B15EFB97` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_23876048C0330AAE` FOREIGN KEY (`recipient_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_23876048CA0B7C00` FOREIGN KEY (`termin_id`) REFERENCES `termine` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_notification_templates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `termin_template_id` int(11) NOT NULL,
  `recipient_user_id` int(11) DEFAULT NULL,
  `recipient_role_id` int(11) DEFAULT NULL,
  `fires_earlier_seconds` int(11) DEFAULT NULL,
  `title` longtext NOT NULL,
  `content` longtext DEFAULT NULL,
  `recipient_termin_owners` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_termin_volunteers` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_termin_participants` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_43613C90B15EFB97` (`recipient_user_id`),
  KEY `IDX_43613C90C0330AAE` (`recipient_role_id`),
  KEY `termin_template_index` (`termin_template_id`),
  CONSTRAINT `FK_43613C90324A4BBA` FOREIGN KEY (`termin_template_id`) REFERENCES `termin_templates` (`id`),
  CONSTRAINT `FK_43613C90B15EFB97` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_43613C90C0330AAE` FOREIGN KEY (`recipient_role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `deadline_earlier_seconds` int(11) DEFAULT NULL,
  `deadline_time` time DEFAULT NULL,
  `min_participants` int(11) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `min_volunteers` int(11) DEFAULT NULL,
  `max_volunteers` int(11) DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 0,
  `title` longtext DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `image_ids` longtext DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `should_promote` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_A2ECDD2964D218E` (`location_id`),
  KEY `IDX_A2ECDD292B18554A` (`owner_user_id`),
  KEY `IDX_A2ECDD295A75A473` (`owner_role_id`),
  KEY `IDX_A2ECDD297D182D95` (`created_by_user_id`),
  KEY `IDX_A2ECDD291A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_A2ECDD291A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A2ECDD292B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_A2ECDD295A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_A2ECDD2964D218E` FOREIGN KEY (`location_id`) REFERENCES `termin_locations` (`id`),
  CONSTRAINT `FK_A2ECDD297D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termin_template_label_map` (
  `termin_template_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  PRIMARY KEY (`termin_template_id`,`label_id`),
  KEY `IDX_D1B03BE2324A4BBA` (`termin_template_id`),
  KEY `IDX_D1B03BE233B92F39` (`label_id`),
  CONSTRAINT `FK_D1B03BE2324A4BBA` FOREIGN KEY (`termin_template_id`) REFERENCES `termin_templates` (`id`),
  CONSTRAINT `FK_D1B03BE233B92F39` FOREIGN KEY (`label_id`) REFERENCES `termin_labels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `throttlings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `last_occurrence` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_name_index` (`event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `old_username` varchar(255) DEFAULT NULL,
  `password` longtext DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `first_name` longtext NOT NULL,
  `last_name` longtext NOT NULL,
  `permissions` longtext NOT NULL,
  `root` longtext DEFAULT NULL,
  `email_is_verified` tinyint(1) NOT NULL,
  `email_verification_token` longtext DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL COMMENT 'M(ale), F(emale), or O(ther)',
  `street` longtext DEFAULT NULL,
  `postal_code` longtext DEFAULT NULL,
  `city` longtext DEFAULT NULL,
  `region` longtext DEFAULT NULL,
  `country_code` varchar(3) DEFAULT NULL COMMENT 'two-letter code (ISO-3166-alpha-2)',
  `birthdate` date DEFAULT NULL,
  `phone` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login_at` datetime DEFAULT NULL,
  `parent_user` int(11) DEFAULT NULL,
  `member_type` varchar(3) DEFAULT NULL COMMENT 'Aktiv, Ehrenmitglied, Verein, Sponsor',
  `member_last_paid` date DEFAULT NULL,
  `wants_postal_mail` tinyint(1) NOT NULL DEFAULT 0,
  `postal_title` longtext DEFAULT NULL COMMENT 'if not {m: Herr, f: Frau, o: }',
  `postal_name` longtext DEFAULT NULL COMMENT 'if not ''First Last''',
  `joined_on` date DEFAULT NULL,
  `joined_reason` longtext DEFAULT NULL,
  `left_on` date DEFAULT NULL,
  `left_reason` longtext DEFAULT NULL,
  `solv_number` longtext DEFAULT NULL,
  `si_card_number` longtext DEFAULT NULL,
  `notes` longtext NOT NULL DEFAULT '',
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `avatar_image_id` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username_index` (`username`),
  KEY `IDX_1483A5E92B18554A` (`owner_user_id`),
  KEY `IDX_1483A5E95A75A473` (`owner_role_id`),
  KEY `IDX_1483A5E97D182D95` (`created_by_user_id`),
  KEY `IDX_1483A5E91A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_1483A5E91A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1483A5E92B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1483A5E95A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_1483A5E97D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_51498A8EA76ED395` (`user_id`),
  KEY `IDX_51498A8ED60322AC` (`role_id`),
  CONSTRAINT `FK_51498A8EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_51498A8ED60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weekly_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `image_id` longtext DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `IDX_1EABE3862B18554A` (`owner_user_id`),
  KEY `IDX_1EABE3865A75A473` (`owner_role_id`),
  KEY `IDX_1EABE3867D182D95` (`created_by_user_id`),
  KEY `IDX_1EABE3861A04EF5A` (`last_modified_by_user_id`),
  KEY `datum_index` (`datum`),
  CONSTRAINT `FK_1EABE3861A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1EABE3862B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_1EABE3865A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_1EABE3867D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET AUTOCOMMIT=@OLD_AUTOCOMMIT */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

