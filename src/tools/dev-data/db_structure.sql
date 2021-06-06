-- Die Struktur der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: OLZ\Migrations\Version20220321214214

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `action` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address_timestamp_index` (`ip_address`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bild_der_woche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `bild1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 0,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datum_index` (`datum`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `file1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_kurz` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `counter_ip_lan` int(11) NOT NULL DEFAULT 0,
  `counter_hit_lan` int(11) NOT NULL DEFAULT 0,
  `counter_ip_web` int(11) NOT NULL DEFAULT 0,
  `counter_hit_web` int(11) NOT NULL DEFAULT 0,
  `stand` datetime DEFAULT NULL,
  `kat_gruppen` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `karten` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locked` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galerie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termin` int(11) NOT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum` date DEFAULT NULL,
  `datum_end` date DEFAULT NULL,
  `autor` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 0,
  `typ` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datum_on_off_index` (`datum`,`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `table_parent` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pfad` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild_name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jwoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nr` int(11) DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pos` int(11) DEFAULT NULL,
  `time1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time3` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time4` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time5` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diff` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starttime` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cat` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `karten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `kartennr` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `center_x` int(11) DEFAULT NULL,
  `center_y` int(11) DEFAULT NULL,
  `jahr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `massstab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ort` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom` int(11) DEFAULT NULL,
  `typ` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vorschau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `olz_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rang` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `club` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jg` int(11) DEFAULT NULL,
  `zeit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anzahl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_events` (
  `solv_uid` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `kind` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_night` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `national` int(11) NOT NULL,
  `region` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `club` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `map` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `deadline` date DEFAULT NULL,
  `entryportal` int(11) NOT NULL,
  `start_link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank_link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_modification` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`solv_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `same_as` int(11) DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_year` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `member` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solv_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person` int(11) NOT NULL,
  `event` int(11) NOT NULL,
  `class` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int(11) NOT NULL,
  `name` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_year` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `club` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` int(11) NOT NULL,
  `splits` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `finish_split` int(11) NOT NULL,
  `class_distance` int(11) NOT NULL,
  `class_elevation` int(11) NOT NULL,
  `class_control_count` int(11) NOT NULL,
  `class_competitor_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_run_unique` (`person`,`event`,`class`,`name`,`birth_year`,`domicile`,`club`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termine_go2ol` (
  `solv_uid` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ident` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verein` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meldeschluss1` date NOT NULL,
  `meldeschluss2` date NOT NULL,
  PRIMARY KEY (`solv_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termine_solv` (
  `solv_uid` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `kind` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day_night` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national` int(11) DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `club` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `entryportal` int(11) DEFAULT NULL,
  `last_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`solv_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainingsphotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `pfad` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
CREATE TABLE `facebook_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `facebook_user` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `FK_3444E616A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_user` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `FK_486FA817A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `strava_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `strava_user` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_expires_at` datetime DEFAULT NULL,
  `telegram_chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_chat_state` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
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
CREATE TABLE `throttlings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_occurrence` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_name_index` (`event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `delivery_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type_args` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  KEY `notification_type_index` (`notification_type`),
  CONSTRAINT `FK_52C540C8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `olz_text` (
  `id` int(11) NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `counter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `args` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `date_range` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date_range_page_index` (`date_range`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_username` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'public',
  `page` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_role` int(11) DEFAULT NULL,
  `index_within_parent` int(11) DEFAULT NULL,
  `featured_index` int(11) DEFAULT NULL,
  `can_have_child_roles` tinyint(1) NOT NULL DEFAULT 0,
  `guide` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'restricted access',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token_index` (`token`),
  KEY `user_id_index` (`user_id`),
  CONSTRAINT `FK_58D184BCA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aktuell` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termin` int(11) NOT NULL,
  `datum` date NOT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `textlang` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typ` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  `bild1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild1_text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  `bild3` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild3_breite` int(11) DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  `author_user_id` int(11) DEFAULT NULL,
  `author_role_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `tags` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `image_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `datum_index` (`datum`),
  KEY `IDX_417D7104E2544CD6` (`author_user_id`),
  KEY `IDX_417D71049339BDEF` (`author_role_id`),
  KEY `IDX_417D71042B18554A` (`owner_user_id`),
  KEY `IDX_417D71045A75A473` (`owner_role_id`),
  KEY `IDX_417D71047D182D95` (`created_by_user_id`),
  KEY `IDX_417D71041A04EF5A` (`last_modified_by_user_id`),
  CONSTRAINT `FK_417D71041A04EF5A` FOREIGN KEY (`last_modified_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_417D71042B18554A` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_417D71045A75A473` FOREIGN KEY (`owner_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_417D71047D182D95` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_417D71049339BDEF` FOREIGN KEY (`author_role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `FK_417D7104E2544CD6` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `counter` int(11) NOT NULL DEFAULT 0,
  `datum` date DEFAULT NULL,
  `autor` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `dummy` int(11) DEFAULT NULL,
  `file1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file1_name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file2_name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  `linkext` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eintrag` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  `allowHTML` int(11) DEFAULT NULL,
  `name2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `newsletter` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `datum_on_off_index` (`datum`,`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `zugriff` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `root` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_is_verified` tinyint(1) NOT NULL,
  `email_verification_token` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'M(ale), F(emale), or O(ther)',
  `street` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'two-letter code (ISO-3166-alpha-2)',
  `birthdate` date DEFAULT NULL,
  `phone` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username_index` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
CREATE TABLE `anmelden_bookings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `form_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
CREATE TABLE `anmelden_registration_infos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `ident` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `index_within_registration` int(11) NOT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_optional` tinyint(1) NOT NULL,
  `options` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
CREATE TABLE `anmelden_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `last_modified_by_user_id` int(11) DEFAULT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
CREATE TABLE `termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `datum_end` date DEFAULT NULL,
  `datum_off` date DEFAULT NULL,
  `zeit` time DEFAULT '00:00:00',
  `zeit_end` time DEFAULT '00:00:00',
  `teilnehmer` int(11) NOT NULL DEFAULT 0,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `go2ol` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solv_event_link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typ` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 0,
  `xkoord` int(11) DEFAULT NULL,
  `ykoord` int(11) DEFAULT NULL,
  `solv_uid` int(11) DEFAULT NULL,
  `ical_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `newsletter` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datum_on_off_index` (`datum`,`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

