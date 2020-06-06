-- Die Struktur der Datenbank der Webseite der OL Zimmerberg

-- NOTE: Database structure is managed by doctrine migrations.
--       This file is only used if migrations bootstrap fails.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Table aktuell
DROP TABLE IF EXISTS `aktuell`;
CREATE TABLE `aktuell` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termin` int(11) NOT NULL,
  `datum` date NOT NULL,
  `newsletter` int(11) NOT NULL DEFAULT 1,
  `newsletter_datum` datetime DEFAULT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `textlang` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typ` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `on_off` int(11) NOT NULL DEFAULT 0,
  `bild1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild1_text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  `bild3` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild3_breite` int(11) DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datum_index` (`datum`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table anm_felder
DROP TABLE IF EXISTS `anm_felder`;
CREATE TABLE `anm_felder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `zeigen` int(11) DEFAULT NULL,
  `label` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typ` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `standard` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `test` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `test_result` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table anmeldung
DROP TABLE IF EXISTS `anmeldung`;
CREATE TABLE `anmeldung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `anzahl` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feld1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feld2` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feld3` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feld4` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table bild_der_woche
DROP TABLE IF EXISTS `bild_der_woche`;
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

-- Table blog
DROP TABLE IF EXISTS `blog`;
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
  `newsletter` int(11) DEFAULT NULL,
  `newsletter_datum` datetime DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  `linkext` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table counter
DROP TABLE IF EXISTS `counter`;
CREATE TABLE `counter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `counter_ip` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `counter_bak` int(11) DEFAULT NULL,
  `counter_ip_bak` int(11) DEFAULT NULL,
  `bak_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table doctrine_migration_versions
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table downloads
DROP TABLE IF EXISTS `downloads`;
CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `file1` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table event
DROP TABLE IF EXISTS `event`;
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

-- Table facebook_settings
DROP TABLE IF EXISTS `facebook_settings`;
CREATE TABLE `facebook_settings` (
  `k` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `v` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table forum
DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eintrag` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `newsletter` int(11) NOT NULL DEFAULT 1,
  `newsletter_datum` datetime DEFAULT NULL,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  `allowHTML` int(11) DEFAULT NULL,
  `name2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `datum_on_off_index` (`datum`,`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table galerie
DROP TABLE IF EXISTS `galerie`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table images
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `table_parent` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pfad` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bild_name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table jwoc
DROP TABLE IF EXISTS `jwoc`;
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

-- Table karten
DROP TABLE IF EXISTS `karten`;
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

-- Table links
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table newsletter
DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategorie` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_date` date DEFAULT NULL,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table olz_result
DROP TABLE IF EXISTS `olz_result`;
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

-- Table olz_text
DROP TABLE IF EXISTS `olz_text`;
CREATE TABLE `olz_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_username` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_role` int(11) DEFAULT NULL,
  `index_within_parent` int(11) DEFAULT NULL,
  `featured_index` int(11) DEFAULT NULL,
  `can_have_child_roles` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table rundmail
DROP TABLE IF EXISTS `rundmail`;
CREATE TABLE `rundmail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `betreff` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailtext` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table solv_events
DROP TABLE IF EXISTS `solv_events`;
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

-- Table solv_people
DROP TABLE IF EXISTS `solv_people`;
CREATE TABLE `solv_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `same_as` int(11) DEFAULT NULL,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_year` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `member` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table solv_results
DROP TABLE IF EXISTS `solv_results`;
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

-- Table termine
DROP TABLE IF EXISTS `termine`;
CREATE TABLE `termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `datum_end` date DEFAULT NULL,
  `datum_off` date DEFAULT NULL,
  `zeit` time DEFAULT '00:00:00',
  `zeit_end` time DEFAULT '00:00:00',
  `teilnehmer` int(11) NOT NULL DEFAULT 0,
  `newsletter` int(11) DEFAULT NULL,
  `newsletter_datum` datetime DEFAULT NULL,
  `newsletter_anmeldung` datetime DEFAULT NULL,
  `titel` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `go2ol` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solv_event_link` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typ` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_off` int(11) NOT NULL DEFAULT 0,
  `datum_anmeldung` date DEFAULT NULL,
  `text_anmeldung` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_anmeldung` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xkoord` int(11) DEFAULT NULL,
  `ykoord` int(11) DEFAULT NULL,
  `solv_uid` int(11) DEFAULT NULL,
  `ical_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `datum_on_off_index` (`datum`,`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table termine_go2ol
DROP TABLE IF EXISTS `termine_go2ol`;
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

-- Table termine_solv
DROP TABLE IF EXISTS `termine_solv`;
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

-- Table trainingsphotos
DROP TABLE IF EXISTS `trainingsphotos`;
CREATE TABLE `trainingsphotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `pfad` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_username` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `zugriff` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `root` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table users_roles
DROP TABLE IF EXISTS `users_roles`;
CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_51498A8EA76ED395` (`user_id`),
  KEY `IDX_51498A8ED60322AC` (`role_id`),
  CONSTRAINT `FK_51498A8EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_51498A8ED60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
