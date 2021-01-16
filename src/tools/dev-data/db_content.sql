-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: OLZ\Migrations\Version20210116164757

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Table aktuell
INSERT INTO aktuell
    (`id`, `termin`, `datum`, `newsletter`, `newsletter_datum`, `titel`, `text`, `textlang`, `link`, `autor`, `typ`, `on_off`, `bild1`, `bild1_breite`, `bild1_text`, `bild2`, `bild2_breite`, `bild3`, `bild3_breite`, `zeit`, `counter`)
VALUES
    ('1', '0', '2006-01-13', '1', NULL, 'Ausschreibungen 📍', '', '<div><a href=\"\" class=\"linkint\">Interner Link</a></div><div><a href=\"\" class=\"linkext\">Externer Link</a></div><div><a href=\"\" class=\"linkpdf\">PDF-Link</a></div><div><a href=\"\" class=\"linkmail\">E-Mail-Link</a></div><div><a href=\"\" class=\"linkmap\">Karten-Link</a></div>', '', 'prä', 'box0', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0'),
    ('2', '0', '2006-01-13', '1', NULL, 'Weekends', '', '<div><a href=\"\" class=\"linkimg\">Bild-Link</a></div><h3>Lager</h3><div><a href=\"\" class=\"linkmovie\">Film-Link</a></div>', '', 'prä', 'box1', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0'),
    ('3', '0', '2020-01-01', '1', NULL, 'Frohes neues Jahr! 🎆', '<BILD1>Im Namen des Vorstands wünsche ich euch allen ein frohes neues Jahr! 🎆', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. Ein Bisschen. Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n<BILD2>\n\nUnd hier nochmals das Emoji: 🎆.', '', 'prä', 'aktuell', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0');

-- Table anm_felder

-- Table anmeldung

-- Table auth_requests

-- Table bild_der_woche
INSERT INTO bild_der_woche
    (`id`, `datum`, `bild1`, `bild2`, `on_off`, `text`, `titel`, `bild1_breite`, `bild2_breite`)
VALUES
    ('1', '2020-01-01', '', '', '0', 'Neujahrs-Impression vom Sihlwald 🌳🌲🌴', 'Titel 1 🌳🌲🌴', '0', '0'),
    ('2', '2020-01-02', '', '', '1', 'Berchtoldstag im Sihlwald 🌳🌲🌴', 'Titel 2 🌳🌲🌴', '0', '0');

-- Table blog
INSERT INTO blog
    (`id`, `counter`, `datum`, `autor`, `titel`, `text`, `bild1`, `bild2`, `on_off`, `zeit`, `dummy`, `file1`, `file1_name`, `file2`, `file2_name`, `newsletter`, `newsletter_datum`, `bild1_breite`, `bild2_breite`, `linkext`)
VALUES
    ('1', '0', '2020-01-01', 'Gold Junge', 'Saisonstart 2020!', '<BILD1> Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: <DATEI1 text=\"Extrem Harte Trainingsstrategie\">', NULL, NULL, '1', '15:15:15', NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);

-- Table counter

-- Table doctrine_migration_versions
INSERT INTO doctrine_migration_versions
    (`version`, `executed_at`, `execution_time`)
VALUES
    ('OLZ\\Migrations\\Version20200409192051', '2020-05-11 22:04:20', NULL),
    ('OLZ\\Migrations\\Version20200423071546', '2020-05-11 22:04:20', NULL),
    ('OLZ\\Migrations\\Version20200511211417', '2020-05-11 22:08:43', NULL),
    ('OLZ\\Migrations\\Version20200620113946', '2020-06-20 11:40:19', NULL),
    ('OLZ\\Migrations\\Version20200913095953', '2020-09-13 10:09:28', '48'),
    ('OLZ\\Migrations\\Version20201123220256', '2020-11-23 22:03:05', '584'),
    ('OLZ\\Migrations\\Version20210116164757', '2021-01-16 16:48:06', '577');

-- Table downloads

-- Table event

-- Table facebook_links

-- Table forum
INSERT INTO forum
    (`id`, `name`, `email`, `eintrag`, `newsletter`, `newsletter_datum`, `uid`, `datum`, `zeit`, `on_off`, `allowHTML`, `name2`)
VALUES
    ('1', 'Guets Nois! 🎉', 'beispiel@olzimmerberg.ch', 'Hoi zäme, au vo mier no Guets Nois! 🎉', '1', NULL, 'hd35lm6glq', '2020-01-01', '21:45:37', '1', '0', 'Bruno 😃 Beispielmitglied'),
    ('2', 'Verspätete Neujahrsgrüsse', 'beispiel@olzimmerberg.ch', 'Has vergesse, aber au vo mier no Guets Nois!', '1', NULL, 'bQjNZ2sy', '2020-01-03', '18:42:01', '1', '0', 'Erwin Exempel'),
    ('3', 'Hallo', 'beispiel@olzimmerberg.ch', 'Mir hend paar OL-Usrüschtigs-Gegeständ us ferne Länder mitbracht, schriibed doch es Mail wenn er öppis devoo wetted', '1', NULL, 'bQjNZ2sy', '2020-01-06', '06:07:08', '1', '0', 'Drei Könige');

-- Table galerie
INSERT INTO galerie
    (`id`, `termin`, `titel`, `datum`, `datum_end`, `autor`, `on_off`, `typ`, `counter`, `content`)
VALUES
    ('1', '0', 'Neujahrsgalerie 📷 2020', '2020-01-01', NULL, 'sh😄', '1', 'foto', '0', ''),
    ('2', '0', 'Berchtoldstagsgalerie 2020', '2020-01-02', NULL, 'sh', '1', 'foto', '0', '');

-- Table google_links

-- Table images

-- Table jwoc

-- Table karten
INSERT INTO karten
    (`id`, `position`, `kartennr`, `name`, `center_x`, `center_y`, `jahr`, `massstab`, `ort`, `zoom`, `typ`, `vorschau`)
VALUES
    ('1', '0', '1086', 'Landforst 🗺️', '685000', '236100', '2017', '1:10\'000', NULL, '8', 'ol', 'landforst_2017_10000.jpg'),
    ('2', '2', '0', 'Eidmatt', '693379', '231463', '2020', '1:1\'000', 'Wädenswil', '2', 'scool', ''),
    ('3', '1', '0', 'Horgen Dorfkern', '687900', '234700', '2011', '1:2\'000', 'Horgen', '8', 'stadt', 'horgen_dorfkern_2011_2000.jpg');

-- Table links

-- Table newsletter

-- Table olz_result

-- Table olz_text
INSERT INTO olz_text
    (`id`, `text`, `on_off`)
VALUES
    ('22', '⚠️ Wichtige Information! ⚠️', '1');

-- Table roles
INSERT INTO roles
    (`id`, `username`, `old_username`, `name`, `parent_role`, `index_within_parent`, `featured_index`, `can_have_child_roles`)
VALUES
    ('1', 'anlaesse', NULL, 'Anlässe🎫, \r\nVizepräsi', NULL, '0', NULL, '1'),
    ('2', 'material', NULL, 'Material \r\n& Karten', NULL, '1', NULL, '1'),
    ('3', 'media', NULL, 'Öffentlich-\r\nkeitsarbeit', NULL, '2', NULL, '1'),
    ('4', 'finanzen', NULL, 'Finanzen', NULL, '3', NULL, '1'),
    ('5', 'praesi', NULL, 'Präsident', NULL, '4', NULL, '1'),
    ('6', 'aktuariat', NULL, 'Aktuariat & \r\nMitgliederliste', NULL, '5', NULL, '1'),
    ('7', 'nachwuchs-ausbildung', NULL, 'Nachwuchs & \r\nAusbildung', NULL, '6', NULL, '1'),
    ('8', 'nachwuchs-leistungssport', NULL, 'Nachwuchs & Leistungssport', NULL, '7', NULL, '1'),
    ('9', 'trainings', NULL, 'Training\r\n& Technik', NULL, '8', NULL, '1'),
    ('10', 'weekends', NULL, 'Weekends', '1', '0', NULL, '1'),
    ('11', 'staffeln', NULL, '5er- und Pfingststaffel', '1', '1', NULL, '1'),
    ('12', 'papiersammlung', NULL, 'Papiersammlung', '1', '2', NULL, '1'),
    ('13', 'papiersammlung-langnau', NULL, 'Langnau', '12', '0', NULL, '0'),
    ('14', 'papiersammlung-thalwil', NULL, 'Thalwil', '12', '1', NULL, '0'),
    ('15', 'flohmarkt', NULL, 'Flohmarkt', '1', '3', NULL, '0'),
    ('16', 'kartenchef', NULL, 'Kartenteam', '2', '0', NULL, '1'),
    ('17', 'kartenteam', NULL, 'Mit dabei', '16', '0', NULL, '0'),
    ('18', 'karten', 'kartenverkauf', 'Kartenverkauf', '2', '1', NULL, '0'),
    ('19', 'kleider', 'kleiderverkauf', 'Kleiderverkauf', '2', '2', NULL, '0'),
    ('20', 'material-group', NULL, 'Material', '2', '3', NULL, '1'),
    ('21', 'materiallager', NULL, 'Lager Thalwil', '20', '0', NULL, '0'),
    ('22', 'sportident', NULL, 'SportIdent', '20', '1', NULL, '0'),
    ('23', 'buessli', NULL, 'OLZ-Büssli', '2', '4', NULL, '1'),
    ('24', 'presse', NULL, 'Presse', '3', '0', NULL, '0'),
    ('25', 'webmaster', NULL, 'Homepage', '3', '1', NULL, '0'),
    ('26', 'holz', NULL, 'Heftli \"HOLZ\"', '3', '2', NULL, '0'),
    ('27', 'revisoren', NULL, 'Revisoren', '4', '0', NULL, '0'),
    ('28', 'ersatzrevisoren', NULL, 'Ersatzrevisor', '27', '0', NULL, '0'),
    ('29', 'sektionen', NULL, 'Sektionen', '5', '0', NULL, '1'),
    ('30', 'sektion-adliswil', NULL, 'Adliswil', '29', '0', NULL, '0'),
    ('31', 'sektion-horgen', NULL, 'Horgen', '29', '1', NULL, '0'),
    ('32', 'sektion-langnau', NULL, 'Langnau', '29', '2', NULL, '0'),
    ('33', 'sektion-richterswil', NULL, 'Richterswil', '29', '3', NULL, '0'),
    ('34', 'sektion-thalwil', NULL, 'Thalwil', '29', '4', NULL, '0'),
    ('35', 'sektion-waedenswil', NULL, 'Wädenswil', '29', '5', NULL, '0'),
    ('36', 'ol-und-umwelt', NULL, 'OL und Umwelt', '5', '1', NULL, '0'),
    ('37', 'versa', 'mira', 'Prävention sexueller Ausbeutung', '5', '2', NULL, '0'),
    ('38', 'archiv', NULL, 'Chronik & Archiv', '6', '0', NULL, '0'),
    ('39', 'js-coaches', NULL, 'J+S Coach', '7', '0', NULL, '0'),
    ('40', 'js-leitende', NULL, 'J+S Leitende', '7', '1', NULL, '0'),
    ('41', 'js-kids', NULL, 'J+S Kids', '7', '2', NULL, '0'),
    ('42', 'scool', NULL, 'sCOOL', '7', '3', NULL, '0'),
    ('43', 'trainer-leistungssport', NULL, 'Trainer Leistungssport', '8', '0', NULL, '0'),
    ('44', 'team-gold', NULL, 'Team Gold', '8', '1', NULL, '1'),
    ('45', 'team-gold-leiter', NULL, 'Leiterteam', '44', '0', NULL, '0'),
    ('46', 'kartentrainings', NULL, 'Kartentraining', '9', '0', NULL, '0'),
    ('47', 'hallentrainings', NULL, 'Hallentraining', '9', '1', NULL, '0'),
    ('48', 'lauftrainings', NULL, 'Lauftraining', '9', '2', NULL, '0');

-- Table rundmail

-- Table solv_events
INSERT INTO solv_events
    (`solv_uid`, `date`, `duration`, `kind`, `day_night`, `national`, `region`, `type`, `name`, `link`, `club`, `map`, `location`, `coord_x`, `coord_y`, `deadline`, `entryportal`, `start_link`, `rank_link`, `last_modification`)
VALUES
    ('6822', '2014-06-29', '1', 'foot', 'day', '1', 'GL/GR', '**A', '6. Nationaler OL 🥶', 'http://www.olg-chur.ch', 'OLG Chur 🦶', 'Crap Sogn Gion/Curnius ⛰️', '', '735550', '188600', '2014-06-10', '1', '', '', '2014-03-05 00:38:15'),
    ('7411', '2015-06-21', '1', 'foot', 'day', '0', 'ZH/SH', '402S', '59. Schweizer 5er Staffel', 'http://www.5erstaffel.ch', 'OLC Kapreolo', 'Chomberg', '', '693700', '259450', '2015-06-01', '1', '', '', '2015-05-15 02:43:20');

-- Table solv_people
INSERT INTO solv_people
    (`id`, `same_as`, `name`, `birth_year`, `domicile`, `member`)
VALUES
    ('1', NULL, 'Toni 😁 Thalwiler', '00', 'Thalwil 🏘️', '1'),
    ('2', NULL, 'Hanna Horgener', '70', 'Horgen', '1'),
    ('3', NULL, 'Walter Wädenswiler', '83', 'Wädenswil', '1'),
    ('4', NULL, 'Regula Richterswiler', '96', 'Richterswil', '1');

-- Table solv_results
INSERT INTO solv_results
    (`id`, `person`, `event`, `class`, `rank`, `name`, `birth_year`, `domicile`, `club`, `result`, `splits`, `finish_split`, `class_distance`, `class_elevation`, `class_control_count`, `class_competitor_count`)
VALUES
    ('1', '1', '6822', 'HAL', '79', 'Toni 😁 Thalwiler', '00', 'Thalwil 🏘️', 'OL Zimmerberg 👍', '1234', '', '12', '4500', '200', '20', '80'),
    ('2', '2', '6822', 'DAM', '3', 'Hanna Horgener', '70', 'Horgen', 'OL Zimmerberg', '4321', '', '43', '3200', '120', '15', '45'),
    ('3', '3', '6822', 'HAK', '13', 'Walter Wädenswiler', '83', 'Wädenswil', 'OL Zimmerberg', '4231', '', '32', '2300', '140', '17', '35'),
    ('4', '1', '7411', 'HAL', '79', 'Anton Thalwiler', '00', 'Thalwil', 'OL Zimmerberg', '1234', '', '12', '4500', '200', '20', '80'),
    ('5', '3', '7411', 'HAK', '13', 'Walti Wädischwiiler', '83', 'Wädenswil', 'OL Zimmerberg', '4231', '', '32', '2300', '140', '17', '35'),
    ('6', '4', '7411', 'DAK', '6', 'Regula Richterswiler', '96', 'Richterswil', 'OL Zimmerberg', '4321', '', '43', '3200', '120', '15', '45');

-- Table strava_links

-- Table telegram_links

-- Table termine
INSERT INTO termine
    (`id`, `datum`, `datum_end`, `datum_off`, `zeit`, `zeit_end`, `teilnehmer`, `newsletter`, `newsletter_datum`, `newsletter_anmeldung`, `titel`, `go2ol`, `text`, `link`, `solv_event_link`, `typ`, `on_off`, `datum_anmeldung`, `text_anmeldung`, `email_anmeldung`, `xkoord`, `ykoord`, `solv_uid`, `ical_uid`, `modified`, `created`)
VALUES
    ('1', '2020-01-02', NULL, NULL, '00:00:00', '00:00:00', '0', '0', NULL, NULL, 'Berchtoldstag 🥈', '', '', '', '', '', '1', NULL, '', '', '0', '0', '0', '', '2020-02-22 01:17:43', '2020-02-22 01:17:09'),
    ('2', '2020-06-06', NULL, NULL, '10:15:00', '12:30:00', '0', '1', NULL, NULL, 'Brunch OL', '', 'Dä Samschtig gits en bsunderä Läckerbissä! <DATEI1> ', 'Infos', 'http://127.0.0.1:30270/', '', '1', NULL, '', '', '685000', '236100', '0', '', '2020-06-01 07:17:09', '2020-06-01 07:17:09'),
    ('3', '2020-08-26', '2020-08-26', NULL, '00:00:00', '00:00:00', '0', '0', NULL, NULL, 'Milchsuppen-Cup, OLZ Trophy 4. Lauf', '', 'Organisation: OL Zimmerberg\r\nKarte: Chopfholz', '<a href=\"?page=20\" class=\"linkint\">OLZ Trophy</a>\r\n<a href=\"https://forms.gle/ixS1ZD22PmbdeYcy6\" class=\"linkext\">Anmeldung</a>\r\n<a href=\"https://olzimmerberg.ch/files/aktuell//504/010.pdf?modified=1597421504\" target=\"_blank\" class=\"linkpdf\">Ausschreibung</a>', NULL, 'ol', '1', NULL, NULL, NULL, '0', '0', '0', NULL, '2020-08-24 22:40:32', '2019-11-20 09:04:26');

-- Table termine_go2ol

-- Table termine_solv

-- Table trainingsphotos

-- Table users
INSERT INTO users
    (`id`, `username`, `old_username`, `password`, `email`, `first_name`, `last_name`, `zugriff`, `root`, `email_is_verified`, `email_verification_token`, `gender`, `street`, `postal_code`, `city`, `region`, `country_code`, `birthdate`, `phone`)
VALUES
    ('1', 'admin', NULL, '$2y$10$RNMfUZk8cdW.VnuC9XZ0tuZhnhnygy9wdhVfs0kkeFN5M0XC1Abce', '', 'Armin 😂', 'Admin 🤣', 'all', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('2', 'vorstand', NULL, '$2y$10$xD9LwSFXo5o0l02p3Jzcde.CsfqFxzLWh2jkuGF19yE0Saqq3J3Kq', '', 'Volker', 'Vorstand', 'ftp olz_text_1 aktuell galerie bild_der_woche', 'vorstand', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('3', 'karten', NULL, '$2y$10$0R5z1L2rbQ8rx5p5hURaje70L0CaSJxVPcnmEhz.iitKhumblmKAW', '', 'Karen', 'Karten', 'ftp', 'karten', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('4', 'hackerman', NULL, '$2y$10$5PZTo/AGC89BX.m637GmGekZaktFet7nno0P8deGt.ASOCHxNVwVe', 'hackerman@test.olzimmerberg.ch', 'Hacker', 'Man', 'all', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Table users_roles
INSERT INTO users_roles
    (`user_id`, `role_id`)
VALUES
    ('1', '5'),
    ('1', '25'),
    ('2', '4'),
    ('2', '17'),
    ('3', '16'),
    ('4', '25');

COMMIT;
