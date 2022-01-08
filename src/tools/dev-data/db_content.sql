-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: OLZ\Migrations\Version20211130230319

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Table access_tokens
INSERT INTO access_tokens
    (`id`, `user_id`, `purpose`, `token`, `created_at`, `expires_at`)
VALUES
    ('1', '1', 'Test', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2021-09-13 22:59:11', NULL),
    ('2', '3', 'WebDAV', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '2021-09-13 23:00:00', NULL);

-- Table aktuell
INSERT INTO aktuell
    (`id`, `termin`, `datum`, `titel`, `text`, `textlang`, `link`, `autor`, `typ`, `on_off`, `bild1`, `bild1_breite`, `bild1_text`, `bild2`, `bild2_breite`, `bild3`, `bild3_breite`, `zeit`, `counter`, `author_user_id`, `author_role_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `tags`, `created_at`, `last_modified_at`, `image_ids`, `newsletter`)
VALUES
    ('1', '0', '2006-01-13', 'Ausschreibungen 📍', '', '<div><a href=\"\" class=\"linkint\">Interner Link</a></div><div><a href=\"\" class=\"linkext\">Externer Link</a></div><div><a href=\"\" class=\"linkpdf\">PDF-Link</a></div><div><a href=\"\" class=\"linkmail\">E-Mail-Link</a></div><div><a href=\"\" class=\"linkmap\">Karten-Link</a></div>', '', 'prä', 'box0', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2021-06-28 16:37:03', '2021-06-28 16:37:03', NULL, '1'),
    ('2', '0', '2006-01-13', 'Weekends', '', '<div><a href=\"\" class=\"linkimg\">Bild-Link</a></div><h3>Lager</h3><div><a href=\"\" class=\"linkmovie\">Film-Link</a></div>', '', 'prä', 'box1', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2021-06-28 16:37:03', '2021-06-28 16:37:03', NULL, '1'),
    ('3', '0', '2020-01-01', 'Frohes neues Jahr! 🎆', '<BILD1>Im Namen des Vorstands wünsche ich euch allen ein frohes neues Jahr! 🎆 <DATEI1 text=\"Neujahrsansprache als PDF\">', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. Ein Bisschen. Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n<BILD2>\n\nUnd hier nochmals das Emoji: 🎆.\n\nUnd hier nochmals die <DATEI1 text=\"Neujahrsansprache als PDF\">', '', 'prä', 'aktuell', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2021-06-28 16:37:03', '2021-06-28 16:37:03', NULL, '1'),
    ('4', '0', '2020-08-15', 'Neues System für News-Einträge online!', '<BILD1>Heute ging ein neues System für News-Einträge online. Nach und nach sollen Aktuell- Galerie- Kaderblog- und Forumseinträge auf das neue System migriert werden. Siehe <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"Motivationsschreiben\">.', 'All diese Einträge sind ähnlich: Sie werden von einem Autor erstellt, enthalten Titel und Text, evtl. Teaser, Bilder und angehängte Dateien, und sind für alle OL-Zimmerberg-Mitglieder von Interesse. Deshalb vereinheitlichen wir nun diese verschiedenen Einträge.\n\nDie Gründe für die Änderung haben wir in <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"diesem Schreiben\"> zusammengefasst.\n\n<BILD1>', NULL, 's.h.', 'aktuell', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '1', NULL, '1', NULL, NULL, NULL, '  ', '2020-08-15 14:51:00', '2020-08-15 14:51:00', '[\"xkbGJQgO5LFXpTSz2dCnvJzu.jpg\"]', '1');

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
    (`id`, `counter`, `datum`, `autor`, `titel`, `text`, `bild1`, `bild2`, `on_off`, `zeit`, `dummy`, `file1`, `file1_name`, `file2`, `file2_name`, `bild1_breite`, `bild2_breite`, `linkext`, `newsletter`)
VALUES
    ('1', '0', '2020-01-01', 'Gold Junge', 'Saisonstart 2020!', '<BILD1> Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: <DATEI1 text=\"Extrem Harte Trainingsstrategie\">', NULL, NULL, '1', '15:15:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1');

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
    ('OLZ\\Migrations\\Version20210116164757', '2021-01-16 16:48:06', '577'),
    ('OLZ\\Migrations\\Version20210129192635', '2021-01-29 19:27:00', '221'),
    ('OLZ\\Migrations\\Version20210317183728', '2021-03-17 18:38:32', '27'),
    ('OLZ\\Migrations\\Version20210405231205', '2021-04-11 18:49:37', '89'),
    ('OLZ\\Migrations\\Version20210411185009', '2021-04-11 18:51:04', '266'),
    ('OLZ\\Migrations\\Version20210628131310', '2021-06-28 14:37:03', '1254'),
    ('OLZ\\Migrations\\Version20210822133337', '2021-08-22 13:35:13', '115'),
    ('OLZ\\Migrations\\Version20210913161236', '2021-09-13 16:13:26', '152'),
    ('OLZ\\Migrations\\Version20211130230319', '2021-11-30 23:41:24', '1337');

-- Table downloads

-- Table event

-- Table facebook_links

-- Table forum
INSERT INTO forum
    (`id`, `name`, `email`, `eintrag`, `uid`, `datum`, `zeit`, `on_off`, `allowHTML`, `name2`, `newsletter`)
VALUES
    ('1', 'Guets Nois! 🎉', 'beispiel@olzimmerberg.ch', 'Hoi zäme, au vo mier no Guets Nois! 🎉', 'hd35lm6glq', '2020-01-01', '21:45:37', '1', '0', 'Bruno 😃 Beispielmitglied', '1'),
    ('2', 'Verspätete Neujahrsgrüsse', 'beispiel@olzimmerberg.ch', 'Has vergesse, aber au vo mier no Guets Nois!', 'bQjNZ2sy', '2020-01-03', '18:42:01', '1', '0', 'Erwin Exempel', '1'),
    ('3', 'Hallo', 'beispiel@olzimmerberg.ch', 'Mir hend paar OL-Usrüschtigs-Gegeständ us ferne Länder mitbracht, schriibed doch es Mail wenn er öppis devoo wetted', 'bQjNZ2sy', '2020-01-06', '06:07:08', '1', '0', 'Drei Könige', '1');

-- Table galerie
INSERT INTO galerie
    (`id`, `termin`, `titel`, `datum`, `datum_end`, `autor`, `on_off`, `typ`, `counter`, `content`)
VALUES
    ('1', '0', 'Neujahrsgalerie 📷 2020', '2020-01-01', NULL, 'sh😄', '1', 'foto', '0', ''),
    ('2', '0', 'Berchtoldstagsgalerie 2020', '2020-01-02', NULL, 'sh', '1', 'foto', '0', ''),
    ('3', '0', 'Test Video', '2020-08-15', NULL, 'admin', '1', 'movie', '2', 'https://youtu.be/JVL0vgcnM6c');

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

-- Table notification_subscriptions
INSERT INTO notification_subscriptions
    (`id`, `user_id`, `delivery_type`, `notification_type`, `notification_type_args`, `created_at`)
VALUES
    ('1', '1', 'email', 'monthly_preview', '{}', '2021-01-30 17:37:23'),
    ('2', '1', 'telegram', 'weekly_preview', '{}', '2021-01-30 17:37:23'),
    ('3', '1', 'telegram', 'deadline_warning', '{\"days\":7}', '2021-01-30 17:37:23'),
    ('4', '4', 'telegram', 'deadline_warning', '{\"days\":3}', '2021-01-30 17:37:23'),
    ('5', '2', 'telegram', 'deadline_warning', '{\"days\":3}', '2021-01-30 17:37:23'),
    ('6', '1', 'email', 'daily_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}', '2021-01-30 17:37:23'),
    ('7', '4', 'email', 'weekly_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}', '2021-01-30 17:37:23');

-- Table olz_result

-- Table olz_text
INSERT INTO olz_text
    (`id`, `text`, `on_off`)
VALUES
    ('1', '<div><p><b>OL-Training (im Sommerhalbjahr)</b><br>\n<i>für Kartentechnik und Orientierung im Wald (ab 6 Jahren)</i><br>\njeden Dienstag gemäss Terminkalender<br>\n<a href=\"/pdf/Trainingsplan_2020.pdf\" target=\"_blank\">Trainingsplan 2020</a></p>\n<p><b>Hallentraining (im Winterhalbjahr)</b><br>\n<i>für Kondition, Kraft, Schnelligkeit mit viel Spiel &amp; Spass (ab 6 Jahren)</i><br>\nSchulhaus Schweikrüti Gattikon (Montag 18.10 - 19.45 Uhr)<br>\nSchulhaus Steinacher Au (Dienstag, 18.00-19.15-20.30 Uhr)<br>\nTurnhalle Platte Thalwil (Freitag, 20.15-22.00 Uhr, Spiel)</p>\n<!--<p><b>Lauftraining</b><br>\n<i>für Ausdauer und Kondition (Jugendliche & Erwachsene)</i><br>\njeden Donnerstag, 18.45 Uhr, 60 Min. (In den Schulferien nur nach Absprache.)</p>-->\n<p><b>Longjoggs (im Winterhalbjahr)</b><br>\n<i>für Ausdauer und Kondition (Jugendliche &amp; Erwachsene)</i><br>\nan Sonntagen gemäss Terminkalender</p></div>', '1'),
    ('22', '⚠️ Wichtige Information! ⚠️', '1');

-- Table roles
INSERT INTO roles
    (`id`, `username`, `old_username`, `name`, `description`, `page`, `parent_role`, `index_within_parent`, `featured_index`, `can_have_child_roles`, `guide`)
VALUES
    ('1', 'anlaesse', NULL, 'Anlässe🎫, \r\nVizepräsi', 'Organisiert Anlässe', '', NULL, '0', NULL, '1', 'Anlässe organisieren:\n- 1 Jahr vorher: abklären\n- ...'),
    ('2', 'material', NULL, 'Material \r\n& Karten', '', '', NULL, '1', NULL, '1', ''),
    ('3', 'media', NULL, 'Öffentlich-\r\nkeitsarbeit', '', '', NULL, '2', NULL, '1', ''),
    ('4', 'finanzen', NULL, 'Finanzen', '', '', NULL, '3', NULL, '1', ''),
    ('5', 'praesi', NULL, 'Präsident', '', '', NULL, '4', NULL, '1', ''),
    ('6', 'aktuariat', NULL, 'Aktuariat & \r\nMitgliederliste', '', '', NULL, '5', NULL, '1', ''),
    ('7', 'nachwuchs-ausbildung', NULL, 'Nachwuchs & \r\nAusbildung', '', '', NULL, '6', NULL, '1', ''),
    ('8', 'nachwuchs-leistungssport', NULL, 'Nachwuchs & Leistungssport', '', '', NULL, '7', NULL, '1', ''),
    ('9', 'trainings', NULL, 'Training\r\n& Technik', '', '', NULL, '8', NULL, '1', ''),
    ('10', 'weekends', NULL, 'Weekends', '', '', '1', '0', NULL, '1', ''),
    ('11', 'staffeln', NULL, '5er- und Pfingststaffel', '', '', '1', '1', NULL, '1', ''),
    ('12', 'papiersammlung', NULL, 'Papiersammlung', '', '', '1', '2', NULL, '1', ''),
    ('13', 'papiersammlung-langnau', NULL, 'Langnau', '', '', '12', '0', NULL, '0', ''),
    ('14', 'papiersammlung-thalwil', NULL, 'Thalwil', '', '', '12', '1', NULL, '0', ''),
    ('15', 'flohmarkt', NULL, 'Flohmarkt', '', '', '1', '3', NULL, '0', ''),
    ('16', 'kartenchef', NULL, 'Kartenteam', '', '', '2', '0', NULL, '1', ''),
    ('17', 'kartenteam', NULL, 'Mit dabei', '', '', '16', '0', NULL, '0', ''),
    ('18', 'karten', 'kartenverkauf', 'Kartenverkauf', '', '', '2', '1', NULL, '0', ''),
    ('19', 'kleider', 'kleiderverkauf', 'Kleiderverkauf', '', '', '2', '2', NULL, '0', ''),
    ('20', 'material-group', NULL, 'Material', '', '', '2', '3', NULL, '1', ''),
    ('21', 'materiallager', NULL, 'Lager Thalwil', '', '', '20', '0', NULL, '0', ''),
    ('22', 'sportident', NULL, 'SportIdent', '', '', '20', '1', NULL, '0', ''),
    ('23', 'buessli', NULL, 'OLZ-Büssli', '', '', '2', '4', NULL, '1', ''),
    ('24', 'presse', NULL, 'Presse', '', '', '3', '0', NULL, '0', ''),
    ('25', 'website', NULL, 'Homepage', '', '', '3', '1', NULL, '0', ''),
    ('26', 'holz', NULL, 'Heftli \"HOLZ\"', '', '', '3', '2', NULL, '0', ''),
    ('27', 'revisoren', NULL, 'Revisoren', '', '', '4', '0', NULL, '0', ''),
    ('28', 'ersatzrevisoren', NULL, 'Ersatzrevisor', '', '', '27', '0', NULL, '0', ''),
    ('29', 'sektionen', NULL, 'Sektionen', '', '', '5', '0', NULL, '1', ''),
    ('30', 'sektion-adliswil', NULL, 'Adliswil', '', '', '29', '0', NULL, '0', ''),
    ('31', 'sektion-horgen', NULL, 'Horgen', '', '', '29', '1', NULL, '0', ''),
    ('32', 'sektion-langnau', NULL, 'Langnau', '', '', '29', '2', NULL, '0', ''),
    ('33', 'sektion-richterswil', NULL, 'Richterswil', '', '', '29', '3', NULL, '0', ''),
    ('34', 'sektion-thalwil', NULL, 'Thalwil', '', '', '29', '4', NULL, '0', ''),
    ('35', 'sektion-waedenswil', NULL, 'Wädenswil', '', '', '29', '5', NULL, '0', ''),
    ('36', 'ol-und-umwelt', NULL, 'OL und Umwelt', '', '', '5', '1', NULL, '0', ''),
    ('37', 'versa', 'mira', 'Prävention sexueller Ausbeutung', '', '', '5', '2', NULL, '0', ''),
    ('38', 'archiv', NULL, 'Chronik & Archiv', '', '', '6', '0', NULL, '0', ''),
    ('39', 'js-coaches', NULL, 'J+S Coach', '', '', '7', '0', NULL, '0', ''),
    ('40', 'js-leitende', NULL, 'J+S Leitende', '', '', '7', '1', NULL, '0', ''),
    ('41', 'js-kids', NULL, 'J+S Kids', '', '', '7', '2', NULL, '0', ''),
    ('42', 'scool', NULL, 'sCOOL', '', '', '7', '3', NULL, '0', ''),
    ('43', 'trainer-leistungssport', NULL, 'Trainer Leistungssport', '', '', '8', '0', NULL, '0', ''),
    ('44', 'team-gold', NULL, 'Team Gold', '', '', '8', '1', NULL, '1', ''),
    ('45', 'team-gold-leiter', NULL, 'Leiterteam', '', '', '44', '0', NULL, '0', ''),
    ('46', 'kartentrainings', NULL, 'Kartentraining', '', '', '9', '0', NULL, '0', ''),
    ('47', 'hallentrainings', NULL, 'Hallentraining', '', '', '9', '1', NULL, '0', ''),
    ('48', 'lauftrainings', NULL, 'Lauftraining', '', '', '9', '2', NULL, '0', ''),
    ('49', 'nachwuchs-kontakt', NULL, 'Kontaktperson Nachwuchs', '', '', '7', '4', NULL, '0', '');

-- Table solv_events
INSERT INTO solv_events
    (`solv_uid`, `date`, `duration`, `kind`, `day_night`, `national`, `region`, `type`, `name`, `link`, `club`, `map`, `location`, `coord_x`, `coord_y`, `deadline`, `entryportal`, `start_link`, `rank_link`, `last_modification`)
VALUES
    ('6822', '2014-06-29', '1', 'foot', 'day', '1', 'GL/GR', '**A', '6. Nationaler OL 🥶', 'http://www.olg-chur.ch', 'OLG Chur 🦶', 'Crap Sogn Gion/Curnius ⛰️', '', '735550', '188600', '2014-06-10', '1', '', '', '2014-03-05 00:38:15'),
    ('7411', '2015-06-21', '1', 'foot', 'day', '0', 'ZH/SH', '402S', '59. Schweizer 5er Staffel', 'http://www.5erstaffel.ch', 'OLC Kapreolo', 'Chomberg', '', '693700', '259450', '2015-06-01', '1', '', '', '2015-05-15 02:43:20'),
    ('12345', '2020-08-22', '1', 'foot', 'day', '1', 'ZH/SH', '402S', 'Grossanlass', 'http://www.grossanlass.ch', 'OLG Bern', 'Grosswald', '', '0', '0', '2020-08-17', '1', '', '', '2015-05-15 02:43:20');

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
INSERT INTO telegram_links
    (`id`, `user_id`, `pin`, `pin_expires_at`, `telegram_chat_id`, `telegram_user_id`, `telegram_chat_state`, `created_at`, `linked_at`)
VALUES
    ('1', '1', '12345678', '2021-04-29 17:16:19', '1', '1', '[]', '2021-04-29 17:16:19', '2021-04-29 17:16:19');

-- Table termine
INSERT INTO termine
    (`id`, `datum`, `datum_end`, `datum_off`, `zeit`, `zeit_end`, `teilnehmer`, `titel`, `go2ol`, `text`, `link`, `solv_event_link`, `typ`, `on_off`, `datum_anmeldung`, `text_anmeldung`, `email_anmeldung`, `xkoord`, `ykoord`, `solv_uid`, `ical_uid`, `modified`, `created`, `newsletter`)
VALUES
    ('1', '2020-01-02', NULL, NULL, '00:00:00', '00:00:00', '0', 'Berchtoldstag 🥈', '', '', '', '', '', '1', NULL, '', '', '0', '0', '0', '', '2020-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('2', '2020-06-06', NULL, NULL, '10:15:00', '12:30:00', '0', 'Brunch OL', '', 'Dä Samschtig gits en bsunderä Läckerbissä!', '<DATEI1 text=\"Infos\">', 'http://127.0.0.1:30270/', '', '1', NULL, '', '', '685000', '236100', '0', '', '2020-06-01 07:17:09', '2020-06-01 07:17:09', '1'),
    ('3', '2020-08-18', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training 1', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2220-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('4', '2020-08-25', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training 2', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2220-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('5', '2020-08-26', '2020-08-26', NULL, '00:00:00', '00:00:00', '0', 'Milchsuppen-Cup, OLZ Trophy 4. Lauf', '', 'Organisation: OL Zimmerberg\r\nKarte: Chopfholz', '<a href=\"?page=20\" class=\"linkint\">OLZ Trophy</a>\r\n<a href=\"https://forms.gle/ixS1ZD22PmbdeYcy6\" class=\"linkext\">Anmeldung</a>\r\n<a href=\"https://olzimmerberg.ch/files/aktuell//504/010.pdf?modified=1597421504\" target=\"_blank\" class=\"linkpdf\">Ausschreibung</a>', NULL, 'ol', '1', NULL, NULL, NULL, '0', '0', '0', NULL, '2020-08-24 22:40:32', '2019-11-20 09:04:26', '0'),
    ('6', '2020-09-01', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training 3', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2020-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('7', '2020-09-08', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training 4', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2020-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('8', '2020-08-11', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training 0', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2220-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('9', '2020-08-04', NULL, NULL, '00:00:00', '00:00:00', '0', 'Training -1', '', '', '', '', 'training', '1', NULL, '', '', '0', '0', '0', '', '2220-02-22 01:17:43', '2020-02-22 01:17:09', '0'),
    ('10', '2020-08-22', NULL, NULL, '00:00:00', '00:00:00', '0', 'Grossanlass', 'gal', 'Mit allem drum und dran!', NULL, NULL, 'ol', '1', NULL, NULL, NULL, NULL, NULL, '12345', NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', '0');

-- Table termine_go2ol

-- Table termine_solv

-- Table throttlings

-- Table trainingsphotos

-- Table users
INSERT INTO users
    (`id`, `username`, `old_username`, `password`, `email`, `first_name`, `last_name`, `zugriff`, `root`, `email_is_verified`, `email_verification_token`, `gender`, `street`, `postal_code`, `city`, `region`, `country_code`, `birthdate`, `phone`, `created_at`, `last_modified_at`, `last_login_at`)
VALUES
    ('1', 'admin', NULL, '$2y$10$RNMfUZk8cdW.VnuC9XZ0tuZhnhnygy9wdhVfs0kkeFN5M0XC1Abce', 'admin@test.olzimmerberg.ch', 'Armin 😂', 'Admin 🤣', 'all', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL),
    ('2', 'vorstand', NULL, '$2y$10$xD9LwSFXo5o0l02p3Jzcde.CsfqFxzLWh2jkuGF19yE0Saqq3J3Kq', '', 'Volker', 'Vorstand', 'ftp webdav olz_text_1 aktuell galerie bild_der_woche', 'vorstand', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL),
    ('3', 'karten', NULL, '$2y$10$0R5z1L2rbQ8rx5p5hURaje70L0CaSJxVPcnmEhz.iitKhumblmKAW', '', 'Karen', 'Karten', 'ftp webdav', 'karten', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL),
    ('4', 'hackerman', NULL, '$2y$10$5PZTo/AGC89BX.m637GmGekZaktFet7nno0P8deGt.ASOCHxNVwVe', 'hackerman@test.olzimmerberg.ch', 'Hacker', 'Man', 'all', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL);

-- Table users_roles
INSERT INTO users_roles
    (`user_id`, `role_id`)
VALUES
    ('1', '5'),
    ('1', '7'),
    ('1', '25'),
    ('1', '49'),
    ('2', '4'),
    ('2', '17'),
    ('3', '16'),
    ('4', '25');

COMMIT;
