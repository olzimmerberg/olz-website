-- Die Struktur der Datenbank der Webseite der OL Zimmerberg

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
  `newsletter` int(1) DEFAULT 1,
  `newsletter_datum` datetime DEFAULT NULL,
  `titel` tinytext DEFAULT NULL,
  `text` text DEFAULT NULL,
  `textlang` text DEFAULT NULL,
  `link` tinytext DEFAULT NULL,
  `autor` varchar(50) DEFAULT NULL,
  `typ` tinytext NOT NULL,
  `on_off` int(1) DEFAULT 0,
  `bild1` tinytext DEFAULT NULL,
  `bild1_breite` int(5) DEFAULT NULL,
  `bild1_text` tinytext DEFAULT NULL,
  `bild2` tinytext DEFAULT NULL,
  `bild2_breite` int(5) DEFAULT NULL,
  `bild3` tinytext DEFAULT NULL,
  `bild3_breite` int(5) DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Table anm_felder
DROP TABLE IF EXISTS `anm_felder`;
CREATE TABLE `anm_felder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `zeigen` int(11) DEFAULT NULL,
  `label` tinytext DEFAULT NULL,
  `typ` tinytext DEFAULT NULL,
  `info` tinytext DEFAULT NULL,
  `standard` tinytext DEFAULT NULL,
  `test` tinytext DEFAULT NULL,
  `test_result` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table anmeldung
DROP TABLE IF EXISTS `anmeldung`;
CREATE TABLE `anmeldung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(100) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `anzahl` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `uid` varchar(10) DEFAULT NULL,
  `on_off` tinyint(1) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `feld1` text DEFAULT NULL,
  `feld2` text DEFAULT NULL,
  `feld3` text DEFAULT NULL,
  `feld4` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table bild_der_woche
DROP TABLE IF EXISTS `bild_der_woche`;
CREATE TABLE `bild_der_woche` (
  `datum` date DEFAULT NULL,
  `bild1` tinytext DEFAULT NULL,
  `bild2` tinytext DEFAULT NULL,
  `on_off` int(11) DEFAULT 0,
  `text` text DEFAULT NULL,
  `titel` text DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Table blog
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `counter` int(11) DEFAULT 0,
  `datum` date DEFAULT NULL,
  `autor` tinytext DEFAULT NULL,
  `titel` tinytext DEFAULT NULL,
  `text` text DEFAULT NULL,
  `bild1` tinytext DEFAULT NULL,
  `bild2` tinytext DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `dummy` int(11) DEFAULT NULL,
  `file1` tinytext DEFAULT NULL,
  `file1_name` tinytext DEFAULT NULL,
  `file2` tinytext DEFAULT NULL,
  `file2_name` tinytext DEFAULT NULL,
  `newsletter` int(11) DEFAULT NULL,
  `newsletter_datum` timestamp NULL DEFAULT NULL,
  `bild1_breite` int(11) DEFAULT NULL,
  `bild2_breite` int(11) DEFAULT NULL,
  `linkext` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table counter
DROP TABLE IF EXISTS `counter`;
CREATE TABLE `counter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` tinytext DEFAULT NULL,
  `name` tinytext DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `counter_ip` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `counter_bak` int(11) DEFAULT NULL,
  `counter_ip_bak` int(11) DEFAULT NULL,
  `bak_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table downloads
DROP TABLE IF EXISTS `downloads`;
CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `name` tinytext DEFAULT NULL,
  `position` decimal(11,1) DEFAULT NULL,
  `file1` tinytext DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table event
DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_kurz` char(100) DEFAULT NULL,
  `name` tinytext DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `counter_ip_lan` int(11) DEFAULT 0,
  `counter_hit_lan` int(11) DEFAULT 0,
  `counter_ip_web` int(11) DEFAULT 0,
  `counter_hit_web` int(11) DEFAULT 0,
  `stand` datetime DEFAULT NULL,
  `kat_gruppen` text DEFAULT NULL,
  `karten` text DEFAULT NULL,
  `locked` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table facebook_settings
DROP TABLE IF EXISTS `facebook_settings`;
CREATE TABLE `facebook_settings` (
  `k` varchar(64) CHARACTER SET latin1 COLLATE latin1_german2_ci NOT NULL,
  `v` text CHARACTER SET latin1 COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table forum
DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL,
  `eintrag` text NOT NULL,
  `newsletter` int(1) DEFAULT 1,
  `newsletter_datum` datetime DEFAULT NULL,
  `uid` varchar(10) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `on_off` tinyint(1) DEFAULT NULL,
  `allowHTML` tinyint(1) DEFAULT NULL,
  `name2` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`),
  KEY `on_off` (`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Table galerie
DROP TABLE IF EXISTS `galerie`;
CREATE TABLE `galerie` (
  `termin` int(11) NOT NULL,
  `titel` text NOT NULL,
  `datum` date DEFAULT NULL,
  `datum_end` date DEFAULT NULL,
  `autor` tinytext DEFAULT NULL,
  `on_off` int(11) DEFAULT 0,
  `typ` tinytext DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`),
  KEY `on_off` (`on_off`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Table images
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `table_parent` tinytext DEFAULT NULL,
  `pfad` tinytext DEFAULT NULL,
  `bild_name` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table jwoc
DROP TABLE IF EXISTS `jwoc`;
CREATE TABLE `jwoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nr` int(11) DEFAULT NULL,
  `name` tinytext DEFAULT NULL,
  `nation` char(3) DEFAULT NULL,
  `pos` decimal(11,3) DEFAULT NULL,
  `time1` tinytext DEFAULT NULL,
  `time2` tinytext DEFAULT NULL,
  `time3` tinytext DEFAULT NULL,
  `time4` tinytext DEFAULT NULL,
  `time5` tinytext DEFAULT NULL,
  `diff` tinytext DEFAULT NULL,
  `starttime` tinytext DEFAULT NULL,
  `cat` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table karten
DROP TABLE IF EXISTS `karten`;
CREATE TABLE `karten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `kartennr` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `center_x` int(11) DEFAULT NULL,
  `center_y` int(11) DEFAULT NULL,
  `jahr` varchar(100) DEFAULT NULL,
  `massstab` varchar(100) DEFAULT NULL,
  `ort` varchar(100) DEFAULT NULL,
  `zoom` int(11) DEFAULT NULL,
  `typ` varchar(50) DEFAULT NULL,
  `vorschau` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table links
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext DEFAULT NULL,
  `url` tinytext DEFAULT NULL,
  `position` decimal(10,1) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `on_off` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table newsletter
DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `kategorie` tinytext DEFAULT NULL,
  `reg_date` date DEFAULT NULL,
  `uid` varchar(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `on_off` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table olz_result
DROP TABLE IF EXISTS `olz_result`;
CREATE TABLE `olz_result` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rang` int(11) DEFAULT NULL,
  `name` char(100) DEFAULT NULL,
  `club` char(100) DEFAULT NULL,
  `jg` int(4) DEFAULT NULL,
  `zeit` char(100) DEFAULT NULL,
  `kat` char(100) DEFAULT NULL,
  `stand` char(100) DEFAULT NULL,
  `anzahl` char(100) DEFAULT NULL,
  `event` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table olz_text
DROP TABLE IF EXISTS `olz_text`;
CREATE TABLE `olz_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text DEFAULT NULL,
  `on_off` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table rundmail
DROP TABLE IF EXISTS `rundmail`;
CREATE TABLE `rundmail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `betreff` tinytext DEFAULT NULL,
  `mailtext` text DEFAULT NULL,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table solv_events
DROP TABLE IF EXISTS `solv_events`;
CREATE TABLE `solv_events` (
  `solv_uid` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `kind` text NOT NULL,
  `day_night` text NOT NULL,
  `national` int(11) NOT NULL,
  `region` text NOT NULL,
  `type` text NOT NULL,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `club` text NOT NULL,
  `map` text NOT NULL,
  `location` text NOT NULL,
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `deadline` date DEFAULT NULL,
  `entryportal` int(11) NOT NULL,
  `start_link` text DEFAULT NULL,
  `rank_link` text DEFAULT NULL,
  `last_modification` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`solv_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table solv_people
DROP TABLE IF EXISTS `solv_people`;
CREATE TABLE `solv_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `same_as` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `birth_year` text NOT NULL,
  `domicile` text NOT NULL,
  `member` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Table solv_results
DROP TABLE IF EXISTS `solv_results`;
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
  `splits` text NOT NULL,
  `finish_split` int(11) NOT NULL,
  `class_distance` int(11) NOT NULL,
  `class_elevation` int(11) NOT NULL,
  `class_control_count` int(11) NOT NULL,
  `class_competitor_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person` (`person`,`event`,`class`,`name`,`birth_year`,`domicile`,`club`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Table termine
DROP TABLE IF EXISTS `termine`;
CREATE TABLE `termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `datum_end` date DEFAULT NULL,
  `datum_off` date DEFAULT NULL,
  `zeit` time DEFAULT '00:00:00',
  `zeit_end` time DEFAULT '00:00:00',
  `teilnehmer` int(11) DEFAULT 0,
  `newsletter` int(1) DEFAULT NULL,
  `newsletter_datum` datetime DEFAULT NULL,
  `newsletter_anmeldung` datetime DEFAULT NULL,
  `titel` tinytext DEFAULT NULL,
  `go2ol` tinytext DEFAULT NULL,
  `text` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `solv_event_link` text DEFAULT NULL,
  `typ` varchar(50) DEFAULT NULL,
  `on_off` int(1) DEFAULT 0,
  `datum_anmeldung` date DEFAULT NULL,
  `text_anmeldung` text DEFAULT NULL,
  `email_anmeldung` tinytext DEFAULT NULL,
  `xkoord` int(11) DEFAULT NULL,
  `ykoord` int(11) DEFAULT NULL,
  `solv_uid` int(11) DEFAULT NULL,
  `ical_uid` char(50) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`),
  KEY `on_off` (`on_off`),
  KEY `datum_end` (`datum_end`),
  KEY `datum_off` (`datum_off`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Table termine_go2ol
DROP TABLE IF EXISTS `termine_go2ol`;
CREATE TABLE `termine_go2ol` (
  `solv_uid` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `ident` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `post` varchar(255) NOT NULL,
  `verein` varchar(255) NOT NULL,
  `datum` varchar(255) NOT NULL,
  `meldeschluss1` date NOT NULL,
  `meldeschluss2` date NOT NULL,
  PRIMARY KEY (`solv_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table termine_solv
DROP TABLE IF EXISTS `termine_solv`;
CREATE TABLE `termine_solv` (
  `solv_uid` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `kind` varchar(255) DEFAULT NULL,
  `day_night` varchar(255) DEFAULT NULL,
  `national` int(11) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_link` varchar(255) DEFAULT NULL,
  `club` varchar(255) DEFAULT NULL,
  `map` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `entryportal` int(11) DEFAULT NULL,
  `last_modification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table trainingsphotos
DROP TABLE IF EXISTS `trainingsphotos`;
CREATE TABLE `trainingsphotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `datum` date NOT NULL,
  `pfad` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table user
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzername` varchar(100) DEFAULT NULL,
  `passwort` varchar(100) DEFAULT NULL,
  `zugriff` text DEFAULT NULL,
  `root` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Table vorstand
DROP TABLE IF EXISTS `vorstand`;
CREATE TABLE `vorstand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext DEFAULT NULL,
  `funktion` tinytext DEFAULT NULL COMMENT 'alt',
  `adresse` text NOT NULL,
  `tel` tinytext NOT NULL,
  `email` tinytext DEFAULT NULL,
  `bild` tinytext DEFAULT NULL,
  `on_off` int(1) DEFAULT 1,
  `position` int(11) DEFAULT NULL COMMENT 'alt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table vorstand_funktion
DROP TABLE IF EXISTS `vorstand_funktion`;
CREATE TABLE `vorstand_funktion` (
  `vorstand` int(11) NOT NULL,
  `funktion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
