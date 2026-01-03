-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: DoctrineMigrations\Version20260103215409

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Table access_tokens
INSERT INTO access_tokens
    (`id`, `user_id`, `purpose`, `token`, `created_at`, `expires_at`)
VALUES
    ('1', '1', 'Test', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2021-09-13 22:59:11', NULL),
    ('2', '3', 'WebDAV', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', '2021-09-13 23:00:00', NULL),
    ('3', '42', 'access', 'public_dev_data_access_token', '2023-04-22 14:00:00', NULL);

-- Table anmelden_bookings

-- Table anmelden_registration_infos

-- Table anmelden_registrations

-- Table anniversary_runs
INSERT INTO anniversary_runs
    (`on_off`, `created_at`, `last_modified_at`, `id`, `run_at`, `distance_meters`, `elevation_meters`, `source`, `info`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `user_id`, `runner_name`, `is_counting`, `sport_type`)
VALUES
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '1', '2020-08-15 16:51:00', '12340', '123', 'manuell', NULL, '1', NULL, '1', '1', '1', 'Armin 😂 A.', '1', NULL),
    ('1', '2020-08-14 23:45:00', '2020-08-14 23:45:00', '2', '2020-08-14 23:45:00', '43210', '432', 'strava-09d1fe82bdfa94def6a94a4ef800cf95', '{\"resource_state\":2,\"athlete\":{\"resource_state\":2,\"firstname\":\"Simon\",\"lastname\":\"H.\"},\"name\":\"Evening Run\",\"distance\":43210.1,\"moving_time\":29190,\"elapsed_time\":29190,\"total_elevation_gain\":432.1,\"type\":\"Run\",\"sport_type\":\"Run\",\"workout_type\":null,\"device_name\":\"Garmin Forerunner 55\"}', NULL, NULL, NULL, NULL, NULL, 'Simon H.', '1', NULL),
    ('1', '2020-08-15 12:00:00', '2020-08-15 12:00:00', '3', '2020-08-15 12:00:00', '8765', '123', 'strava-abcdef0123456789abcdef0123456789', '{\"resource_state\":2,\"athlete\":{\"resource_state\":2,\"firstname\":\"Test\",\"lastname\":\"U.\"},\"name\":\"Evening Whatever\",\"distance\":8765.4,\"moving_time\":2919,\"elapsed_time\":2919,\"total_elevation_gain\":123.4,\"type\":\"Whatever\",\"sport_type\":\"Whatever\",\"workout_type\":null,\"device_name\":\"Garmin Forerunner 55\"}', NULL, NULL, NULL, NULL, NULL, 'Test U.', '0', NULL),
    ('0', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '4', '2020-08-15 16:51:00', '123', '12', 'softdeleted', NULL, '2', NULL, '2', '2', '2', 'Volker V.', '1', NULL);

-- Table auth_requests
-- (auth_requests omitted)

-- Table counter
-- (counter omitted)

-- Table doctrine_migration_versions
INSERT INTO doctrine_migration_versions
    (`version`, `executed_at`, `execution_time`)
VALUES
    ('DoctrineMigrations\\Version20200409192051', '2020-05-11 22:04:20', NULL),
    ('DoctrineMigrations\\Version20200423071546', '2020-05-11 22:04:20', NULL),
    ('DoctrineMigrations\\Version20200511211417', '2020-05-11 22:08:43', NULL),
    ('DoctrineMigrations\\Version20200620113946', '2020-06-20 11:40:19', NULL),
    ('DoctrineMigrations\\Version20200913095953', '2020-09-13 10:09:28', '48'),
    ('DoctrineMigrations\\Version20201123220256', '2020-11-23 22:03:05', '584'),
    ('DoctrineMigrations\\Version20210116164757', '2021-01-16 16:48:06', '577'),
    ('DoctrineMigrations\\Version20210129192635', '2021-01-29 19:27:00', '221'),
    ('DoctrineMigrations\\Version20210317183728', '2021-03-17 18:38:32', '27'),
    ('DoctrineMigrations\\Version20210405231205', '2021-04-11 18:49:37', '89'),
    ('DoctrineMigrations\\Version20210411185009', '2021-04-11 18:51:04', '266'),
    ('DoctrineMigrations\\Version20210628131310', '2021-06-28 14:37:03', '1254'),
    ('DoctrineMigrations\\Version20210822133337', '2021-08-22 13:35:13', '115'),
    ('DoctrineMigrations\\Version20210913161236', '2021-09-13 16:13:26', '152'),
    ('DoctrineMigrations\\Version20211130230319', '2021-11-30 23:41:24', '1337'),
    ('DoctrineMigrations\\Version20220317172850', '2022-03-17 17:30:24', '2336'),
    ('DoctrineMigrations\\Version20220321214214', '2022-03-21 21:44:24', '2066'),
    ('DoctrineMigrations\\Version20220502172202', '2022-05-02 17:22:32', '107'),
    ('DoctrineMigrations\\Version20220601201046', '2022-06-01 20:11:14', '75'),
    ('DoctrineMigrations\\Version20220719182315', '2022-07-19 18:49:59', '247'),
    ('DoctrineMigrations\\Version20220820142330', '2022-08-20 14:32:06', '130'),
    ('DoctrineMigrations\\Version20220910163629', '2022-09-10 16:37:39', '121'),
    ('DoctrineMigrations\\Version20220912114134', '2022-09-12 11:44:11', '83'),
    ('DoctrineMigrations\\Version20221024123804', '2022-10-24 14:52:16', '552'),
    ('DoctrineMigrations\\Version20221029112426', '2022-10-29 11:25:10', '93'),
    ('DoctrineMigrations\\Version20221207235912', '2022-12-13 10:32:12', '10'),
    ('DoctrineMigrations\\Version20230216214916', '2023-02-16 21:52:12', '85'),
    ('DoctrineMigrations\\Version20230313175531', '2023-03-13 17:57:39', '463'),
    ('DoctrineMigrations\\Version20230319212301', '2023-03-19 21:24:33', '49'),
    ('DoctrineMigrations\\Version20230402173341', '2023-04-02 20:32:38', '17'),
    ('DoctrineMigrations\\Version20230407141618', '2023-04-08 00:06:31', '27'),
    ('DoctrineMigrations\\Version20230508092141', '2023-05-08 09:23:00', '727'),
    ('DoctrineMigrations\\Version20230508102943', '2023-05-08 10:46:34', '441'),
    ('DoctrineMigrations\\Version20230520202843', '2023-05-20 20:34:55', '44'),
    ('DoctrineMigrations\\Version20230611163952', '2023-06-11 16:45:10', '879'),
    ('DoctrineMigrations\\Version20230701122001', '2023-07-01 12:47:09', '10'),
    ('DoctrineMigrations\\Version20230918192344', '2023-09-18 21:37:49', '2264'),
    ('DoctrineMigrations\\Version20230918214135', '2023-09-18 23:43:01', '300'),
    ('DoctrineMigrations\\Version20230918231338', '2023-09-19 18:09:29', '1270'),
    ('DoctrineMigrations\\Version20231018165331', '2023-10-18 18:55:14', '72'),
    ('DoctrineMigrations\\Version20231114221915', '2023-11-14 23:26:53', '1546'),
    ('DoctrineMigrations\\Version20240101225849', '2024-01-01 23:59:31', '56'),
    ('DoctrineMigrations\\Version20240102172229', '2024-01-02 18:24:51', '68'),
    ('DoctrineMigrations\\Version20240103010715', '2024-01-03 19:28:27', '286'),
    ('DoctrineMigrations\\Version20240204153949', '2024-02-04 16:40:49', '74'),
    ('DoctrineMigrations\\Version20240207225304', '2024-02-07 23:54:47', '144'),
    ('DoctrineMigrations\\Version20240219120442', '2024-02-19 13:06:31', '78'),
    ('DoctrineMigrations\\Version20240222220523', '2024-02-22 23:07:58', '792'),
    ('DoctrineMigrations\\Version20240313195047', '2024-03-13 20:52:06', '644'),
    ('DoctrineMigrations\\Version20240317181327', '2024-03-17 19:32:18', '672'),
    ('DoctrineMigrations\\Version20240324230314', '2024-03-25 00:05:14', '704'),
    ('DoctrineMigrations\\Version20240325152618', '2024-03-25 16:31:50', '669'),
    ('DoctrineMigrations\\Version20240406120652', '2024-04-06 14:08:51', '699'),
    ('DoctrineMigrations\\Version20240521174343', '2024-05-21 19:44:54', '1085'),
    ('DoctrineMigrations\\Version20240524112831', '2024-05-25 17:44:57', '51'),
    ('DoctrineMigrations\\Version20240525162538', '2024-05-25 18:48:27', '140'),
    ('DoctrineMigrations\\Version20240530231008', '2024-06-10 21:54:50', '225'),
    ('DoctrineMigrations\\Version20240610194821', '2024-06-10 21:55:07', '8'),
    ('DoctrineMigrations\\Version20240611170404', '2024-06-11 19:05:53', '80'),
    ('DoctrineMigrations\\Version20240728095826', '2024-07-28 12:16:02', '33'),
    ('DoctrineMigrations\\Version20240728114645', '2024-07-28 13:48:11', '119'),
    ('DoctrineMigrations\\Version20241007152642', '2024-10-07 17:28:16', '58'),
    ('DoctrineMigrations\\Version20241111170011', '2024-11-11 18:00:58', '241'),
    ('DoctrineMigrations\\Version20241117162027', '2024-11-17 17:20:58', '112'),
    ('DoctrineMigrations\\Version20250317225100', '2025-03-17 23:55:39', '67'),
    ('DoctrineMigrations\\Version20250428113621', '2025-04-28 13:59:29', '56'),
    ('DoctrineMigrations\\Version20250513214853', '2025-05-13 23:50:47', '598'),
    ('DoctrineMigrations\\Version20250526204822', '2025-05-29 21:16:25', '669'),
    ('DoctrineMigrations\\Version20250618220717', '2025-06-19 00:07:51', '81'),
    ('DoctrineMigrations\\Version20251125174358', '2025-11-25 18:44:24', '438'),
    ('DoctrineMigrations\\Version20251231090437', '2025-12-31 10:05:41', '13'),
    ('DoctrineMigrations\\Version20260103215409', '2026-01-03 22:54:48', '16');

-- Table downloads
INSERT INTO downloads
    (`id`, `name`, `position`, `on_off`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`)
VALUES
    ('1', 'Statuten', '0', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('2', '---', '1', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('3', 'Spesenreglement', '2', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('4', 'Trainingsplan 2020', '3', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('5', 'SOFT DELETED', '2', '0', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53');

-- Table karten
INSERT INTO karten
    (`id`, `kartennr`, `name`, `jahr`, `massstab`, `ort`, `zoom`, `typ`, `vorschau`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `latitude`, `longitude`)
VALUES
    ('1', '1086', 'Landforst 🗺️', '2017', '1:10\'000', NULL, '8', 'ol', 'MIGRATED0000000000010001.jpg', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', '47.270326086742', '8.5619145270506'),
    ('2', '0', 'Eidmatt', '2020', '1:1\'000', 'Wädenswil', '2', 'scool', '', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', '47.227491444839', '8.6716637472003'),
    ('3', '0', 'Horgen Dorfkern', '2011', '1:2\'000', 'Horgen', '8', 'stadt', '6R3bpgwcCU3SfUF8vCpepzRJ.jpg', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', '47.257355383409', '8.5999590514731'),
    ('4', '0', 'Trainings-Karte', '2020', '1:7\'500', '', '8', 'ol', '', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', '47.26641894057469', '8.543090289598807'),
    ('5', '0', 'SOFT DELETED', '2020', '1:7\'500', '', '8', 'ol', '', NULL, NULL, NULL, NULL, '0', '2024-02-22 23:07:58', '2024-02-22 23:07:58', '47.26641894057469', '8.543090289598807');

-- Table links
INSERT INTO links
    (`id`, `name`, `url`, `position`, `on_off`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`)
VALUES
    ('1', 'SOLV', 'https://swiss-orienteering.ch', '0', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('2', '---', '---', '1', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('3', 'GO2OL', 'https://go2ol.ch', '2', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('4', 'Online-Trainings', 'https://olzimmerberg.ch/quiz', '3', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('5', 'SOFT DELETED', 'https://olzimmerberg.ch/quiz', '2', '0', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53');

-- Table members
INSERT INTO members
    (`on_off`, `created_at`, `last_modified_at`, `id`, `ident`, `data`, `updates`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `user_id`)
VALUES
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '1', '2000001', '{\"Nachname\":\"Admin 🤣\",\"Vorname\":\"Armin 😂\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"admin\",\"Anrede\":\"Herr\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"admin@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"E\",\"[Rolle]\":\"Administrator\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000001\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '1'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '2', '2000002', '{\"Nachname\":\"Vorstand\",\"Vorname\":\"Volker\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"vorstand\",\"Anrede\":\"Herr\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000002\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '2'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '3', '2000003', '{\"Nachname\":\"Karten\",\"Vorname\":\"Karen\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"kartenverkauf\",\"Anrede\":\"Frau\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"karen@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000003\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '3'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '4', '2000004', '{\"Nachname\":\"Konto\",\"Vorname\":\"Ohne\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"ohne.konto\",\"Anrede\":\"\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"nutzer@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000004\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', NULL),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '5', '2000005', '{\"Nachname\":\"Nutzer\",\"Vorname\":\"Be\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"benutzer\",\"Anrede\":\"\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"nutzer@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000005\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '5'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', '2000006', '{\"Nachname\":\"Teil\",\"Vorname\":\"Eltern\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"parent\",\"Anrede\":\"\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"parent@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000006\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '6'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '7', '2000007', '{\"Nachname\":\"Eins\",\"Vorname\":\"Kind\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"child1\",\"Anrede\":\"\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"child1@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000007\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '7'),
    ('1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '9', '2000009', '{\"Nachname\":\"Läufer\",\"Vorname\":\"Kader\",\"Firma\":\"\",\"Adresse\":\"\",\"PLZ\":\"\",\"Ort\":\"\",\"Telefon Privat\":\"\",\"Telefon Mobil\":\"\",\"Benutzer-Id\":\"kaderlaeufer\",\"Anrede\":\"\",\"Titel\":\"\",\"Briefanrede\":\"\",\"Adress-Zusatz\":\"\",\"Land\":\"\",\"Nationalit\\u00e4t\":\"\",\"Telefon Gesch\\u00e4ft\":\"\",\"Fax\":\"\",\"E-Mail\":\"kaderlaeufer@staging.olzimmerberg.ch\",\"E-Mail Alternativ\":\"\",\"[Gruppen]\":\"\",\"Status\":\"A\",\"[Rolle]\":\"Standard Benutzer\",\"Eintritt\":\"13.01.2006\",\"Mitgliedsjahre\":\"14\",\"Austritt\":\"\",\"Zivilstand\":\"\",\"Geschlecht\":\"\",\"Geburtsdatum\":\"\",\"Jahrgang\":\"\",\"Alter\":\"\",\"Bemerkungen\":\"\",\"Firmen-Webseite\":\"\",\"Rechnungsversand\":\"E-Mail\",\"Nie mahnen\":\"Nein\",\"IBAN\":\"\",\"BIC\":\"\",\"Kontoinhaber\":\"\",\"Mail-MV\":\"ja\",\"SOLV NR\":\"\",\"Badge Nummer\":\"\",\"Werbegrund\":\"\",\"Geburtsjahr\":\"\",\"[Id]\":\"2000009\",\"[Zuletzt ge\\u00e4ndert am]\":\"01.05.2020 12:34:56\",\"[Zuletzt ge\\u00e4ndert von]\":\"Clubdesk-Benutzer\"}', NULL, '1', NULL, '1', '1', '9');

-- Table messenger_messages
-- (messenger_messages omitted)

-- Table news
INSERT INTO news
    (`id`, `author_user_id`, `author_role_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `termin`, `published_date`, `published_time`, `newsletter`, `title`, `teaser`, `content`, `image_ids`, `external_url`, `author_name`, `author_email`, `format`, `tags`, `counter`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('3', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '00:00:00', '1', 'Frohes neues Jahr! 🎆', '![](./MIGRATED0000000000030001.jpg)Im Namen des Vorstands wünsche ich euch allen ein **frohes neues Jahr**! 🎆 [Neujahrsansprache als PDF](./MIGRATED0000000000030001.pdf)', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. *Ein Bisschen.* Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n![](./MIGRATED0000000000030002.jpg)\n\nUnd hier nochmals das Emoji: 🎆.\n\nUnd hier nochmals die [Neujahrsansprache als PDF](./MIGRATED0000000000030001.pdf)\n\n Und hier ein riesiger Link: https://verylongsubdomain.alsoaverylongdomain.long/andlastbutnotleastaverylongpathwhichmustreallybeonlyjustonewordsuchthatthiswordmustbebrokenupexplicitlyinordertonotcompletelybreaktheUI', '[\"MIGRATED0000000000030001.jpg\",\"MIGRATED0000000000030002.jpg\"]', '', 'prä', NULL, 'aktuell', '', '0', '1', '2021-06-28 16:37:03', '2021-06-28 16:37:03'),
    ('4', '1', '25', '1', NULL, NULL, NULL, '0', '2020-03-16', NULL, '1', 'Neues System für News-Einträge online!', '![](./xkbGJQgO5LFXpTSz2dCnvJzu.jpg)Heute ging ein neues System für **News-Einträge online**. Nach und nach sollen Aktuell- Galerie- Kaderblog- und Forumseinträge auf das neue System migriert werden. Siehe [Motivationsschreiben](./xMpu3ExjfBKa8Cp35bcmsDgq.pdf).', 'All diese Einträge sind ähnlich: Sie werden von einem Autor erstellt, enthalten Titel und Text, evtl. Teaser, Bilder und angehängte Dateien, und sind für alle *OL-Zimmerberg-Mitglieder* von Interesse. Deshalb **vereinheitlichen** wir nun diese verschiedenen Einträge.\n\nDie Gründe für die Änderung haben wir in [diesem Schreiben](./xMpu3ExjfBKa8Cp35bcmsDgq.pdf) zusammengefasst.\n\n![](./xkbGJQgO5LFXpTSz2dCnvJzu.jpg)', '[\"xkbGJQgO5LFXpTSz2dCnvJzu.jpg\"]', NULL, '', NULL, 'aktuell', '  ', '0', '1', '2020-03-16 14:51:00', '2020-03-16 14:51:00'),
    ('5', '1', NULL, '1', NULL, NULL, NULL, '0', '2020-08-15', NULL, '1', 'Neues System für News-Einträge bewährt sich', 'Das neue System für News-Einträge scheint *gut anzukommen*. Neu können eingeloggte Benutzer in ihren News-Einträgen (ehem. Forumseinträgen) auch Bilder und Dateien einbinden.', '', '[]', NULL, '', NULL, 'aktuell', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('6', '3', NULL, '3', NULL, NULL, NULL, '0', '2020-01-02', NULL, '1', 'Berchtoldstagsgalerie 2020', '', 'Ein paar Fotos vom Berchtoldstag.', '[\"eGbiJQgOyLF5p6S92kC3vTzE.jpg\",\"Frw83uTOyLF5p6S92kC7zpEW.jpg\"]', NULL, '', NULL, 'galerie', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('7', '2', '26', '2', NULL, NULL, NULL, '0', '2020-08-15', NULL, '1', 'Test Video', '', 'https://youtu.be/JVL0vgcnM6c', '[\"aRJIflbxtkF5p6S92k470912.jpg\"]', NULL, '', NULL, 'video', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('8', '1', '5', '1', NULL, '1', '1', '0', '2020-01-15', '16:51:00', '1', 'Hinweis vom Präsi', '', 'Auch der **Präsident** schreibt im Forum!', '[\"9GjbtlsSu96AWZ-oH0rHjxup.jpg\",\"zUXE3aKfbK3edmqS35FhaF8g.jpg\"]', NULL, NULL, NULL, 'forum', '  ', '0', '1', '2020-01-15 16:51:00', '2020-01-15 16:51:00'),
    ('9', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-06', '16:51:00', '1', 'Longjogg am Sonntag', '', 'Ich will mich nicht einloggen, aber hier mein Beitrag:\n\nIch organisiere einen **Longjogg am Sonntag**.\n\nPackt *warme* Kleidung ein.\n\nWer zum Pastaessen bleiben will, muss sich bis am Samstagmittag bei mir melden.\n\nAusserdem habe ich weitere unfassbar komplizierte Anforderungen an meine Gäste und geht dermassen tief ins Detail, dass dieser Forumseintrag unter keinen Umständen in seiner ganzen Länge in der Liste der Forumseinträge angezeigt werden sollte!\n\nAnreise:\nRichterswil ab 09:30\nWädenswil ab 09:31\nHorgen ab 09:32\nThalwil ab 09:33\nZürich HB ab 09:34\nBei mir an 09:35', '[]', NULL, 'Anonymous', 'anonymous@gmail.com', 'forum', '  ', '0', '1', '2020-01-06 16:51:00', '2020-01-06 16:51:00'),
    ('10', '9', '50', '9', NULL, '9', '9', '0', '2020-08-15', '16:51:00', '1', 'Dank dem neuen News-System trainiere ich 50% besser!', '', '![](./DvDB8QkHcGuxQ4lAFwyvHnVd.jpg)Ich bin total Fan vom neuen News-System! Meine Trainingsleistung hat deswegen um 50% zugenommen (keine Ahnung wieso). Hier die Beweise:\n\n- https://verylongsubdomain.alsoaverylongdomain.long/andlastbutnotleastaverylongpathwhichmustreallybeonlyjustonewordsuchthatthiswordmustbebrokenupexplicitlyinordertonotcompletelybreaktheUI\n- [Beweisstück A](./gAQa_kYXqXTP1_DKKU1s1pGr.csv)\n- [Beweisstück B](./8kCalo9sQtu2mrgrmMjoGLUW.pdf)\n\n![](./OOVJIqrWlitR_iTZuIIhztKC.jpg)', '[\"DvDB8QkHcGuxQ4lAFwyvHnVd.jpg\",\"OOVJIqrWlitR_iTZuIIhztKC.jpg\"]', NULL, NULL, NULL, 'kaderblog', '  ', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('11', '9', '50', '9', NULL, '9', '9', '0', '2020-08-15', '16:51:00', '1', 'SOFT DELETED', 'SOFT DELETED', 'SOFT DELETED', '[]', NULL, NULL, NULL, 'aktuell', '  ', '0', '0', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('1202', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', NULL, '1', 'Neujahrsgalerie 📷 2020', '', 'Ein paar Fotos vom Neujahrstag.', '[\"MIGRATED0000000012020001.jpg\",\"MIGRATED0000000012020002.jpg\",\"MIGRATED0000000012020003.jpg\",\"MIGRATED0000000012020004.jpg\",\"MIGRATED0000000012020005.jpg\"]', NULL, '', NULL, 'galerie', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('1203', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-08-13', NULL, '1', 'Test Video', '', '', '[\"MIGRATED0000000012030001.jpg\"]', 'https://youtu.be/JVL0vgcnM6c', '', NULL, 'video', '  ', '0', '1', '2020-08-13 14:51:00', '2020-08-13 14:51:00'),
    ('2901', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '21:45:37', '0', 'Guets Nois! 🎉', '', 'Hoi zäme, au vo mier no *Guets Nois*! 🎉', '[]', NULL, 'Bruno 😃 Beispielmitglied', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-01 21:45:37', '2020-01-01 21:45:37'),
    ('2902', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-03', '18:42:01', '0', 'Verspätete Neujahrsgrüsse', '', 'Has vergesse, aber au vo *mier* no Guets Nois!', '[]', NULL, 'Erwin Exempel', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-03 18:42:01', '2020-01-03 18:42:01'),
    ('2903', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-06', '02:07:08', '0', 'Hallo', '', 'Mir hend paar **OL-Usrüschtigs-Gegeständ** us ferne Länder mitbracht.\n\nSchriibed doch es Mail wenn er öppis devoo wetted', '[]', NULL, 'Drei Könige', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-06 06:07:08', '2020-01-06 06:07:08'),
    ('6401', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2019-01-01', '15:15:15', '0', 'Saisonstart 2019!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Hoi zäme, dieser Eintrag wurde noch mit dem alten System geschrieben. Hier die Anhänge:\n![](./MIGRATED0000000064010001.jpg)\n![](./MIGRATED0000000064010002.jpg)\n<DL1>\n<DL2>', '[\"MIGRATED0000000064010001.jpg\",\"MIGRATED0000000064010002.jpg\"]', NULL, 'Gold Junge', NULL, 'kaderblog', '', '0', '1', '2019-01-01 15:15:15', '2019-01-01 15:15:15'),
    ('6402', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2019-08-15', '15:15:15', '0', 'Neuer Eintrag auf meinem externen Blog', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Kleiner Teaser', '[]', 'https://www.external-blog.com/entry/1234', 'Eliteläuferin', NULL, 'kaderblog', '', '0', '1', '2019-08-15 15:15:15', '2019-08-15 15:15:15'),
    ('6403', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '15:15:15', '0', 'Saisonstart 2020!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', '![](./MIGRATED0000000064030001.jpg) Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: [Extrem Harte Trainingsstrategie](./MIGRATED0000000064030001.pdf)', '[\"MIGRATED0000000064030001.jpg\"]', NULL, 'Gold Junge', NULL, 'kaderblog', '', '0', '1', '2020-01-01 15:15:15', '2020-01-01 15:15:15');

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

-- Table panini24
INSERT INTO panini24
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `line1`, `line2`, `association`, `img_src`, `img_style`, `is_landscape`, `has_top`, `on_off`, `created_at`, `last_modified_at`, `infos`, `birthdate`, `num_mispunches`)
VALUES
    ('10', NULL, NULL, NULL, NULL, 'Logo', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-15%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('11', NULL, NULL, NULL, NULL, 'Fahne', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-7%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('12', NULL, NULL, NULL, NULL, 'Glogge', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-10%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('13', NULL, NULL, NULL, NULL, 'Vorstand 2023', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-12%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('14', NULL, NULL, NULL, NULL, 'OLZ-Award', NULL, NULL, 'other/portrait.jpg', 'width:105%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('15', NULL, NULL, NULL, NULL, 'Website', NULL, NULL, 'other/landscape.jpg', 'width:104%; left:-2%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('16', NULL, NULL, NULL, NULL, 'HOLZ', NULL, NULL, 'other/portrait.jpg', 'width:120%; left:-10%; top:-3%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('17', NULL, NULL, NULL, NULL, 'Der Posten', NULL, NULL, 'other/portrait.jpg', 'width:107%; left:-4%; top:0%;', '0', '0', '1', '2023-12-28 18:54:04', '2023-12-28 18:54:04', '', NULL, NULL),
    ('18', NULL, NULL, NULL, NULL, '2009         Paninigewinner         2016', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-10%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('20', NULL, NULL, NULL, NULL, 'Panini-OK', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-15%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('40', NULL, NULL, NULL, NULL, 'Original', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-10%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('41', NULL, NULL, NULL, NULL, 'Special Klubreise Prag', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-25%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('42', NULL, NULL, NULL, NULL, 'Special Klubreise Elsass', NULL, NULL, 'other/landscape.jpg', 'width:110%; left:-5%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('43', NULL, NULL, NULL, NULL, 'Wald-Motiv', NULL, NULL, 'other/landscape.jpg', 'width:120%; left:0%; top:-40%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('44', NULL, NULL, NULL, NULL, 'Stadt-Motiv', NULL, NULL, 'other/landscape.jpg', 'width:120%; left:0%; top:-40%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('50', NULL, NULL, NULL, NULL, '2006: Gründung OL Zimmerberg', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('51', NULL, NULL, NULL, NULL, '2009: 1. OLZ Paninialbum', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:0%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('52', NULL, NULL, NULL, NULL, '2010: 1. grünes OLZ Büssli', NULL, NULL, 'other/landscape.jpg', 'width:120%; left:0%; top:-40%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('53', NULL, NULL, NULL, NULL, '2012: 1. Klubreise Prag', NULL, NULL, 'other/landscape.jpg', 'width:130%; left:-20%; top:-5%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('54', NULL, NULL, NULL, NULL, '2015: 2. Klubreise Elsass', NULL, NULL, 'other/landscape.jpg', 'width:120%; left:-17%; top:-15%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('55', NULL, NULL, NULL, NULL, '2016: 1. Jahr OLZ Trophy', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('56', NULL, NULL, NULL, NULL, '2016: 2. OLZ Paninialbum', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('57', NULL, NULL, NULL, NULL, '2017: 3. Klubreise Regensburg', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-12%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('58', NULL, NULL, NULL, NULL, '2018: Julia holt Gold an WM- und EM-Staffel', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('59', NULL, NULL, NULL, NULL, '2018: 1. Jahr Team Gold', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-5%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('60', NULL, NULL, NULL, NULL, '2019: Nationaler Zimmerberg OL Richterswil', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('61', NULL, NULL, NULL, NULL, '2020: Corona', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-13%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('62', NULL, NULL, NULL, NULL, '2021: Nationaler Zimmerberg OL Flumserberg', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-15%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('63', NULL, NULL, NULL, NULL, '2022: Nationaler Zimmerberg OL Madrisa', NULL, NULL, 'other/landscape.jpg', 'width:130%; left:-15%; top:0%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('64', NULL, NULL, NULL, NULL, '2024: 4. Klubreise Provence', NULL, NULL, 'other/landscape.jpg', 'width:100%; left:0%; top:-25%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('65', NULL, NULL, NULL, NULL, '2046: Helferessen vom OL Lager 2011', NULL, NULL, 'other/landscape.jpg', 'width:160%; left:-30%; top:-20%;', '1', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('100', NULL, NULL, NULL, NULL, 'Adliswil-Sood', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('101', NULL, NULL, NULL, NULL, 'Allmendhölzli-Aabachtobel', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('102', NULL, NULL, NULL, NULL, 'Halbinsel Au', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('103', NULL, NULL, NULL, NULL, 'Sportanlage Brand', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('104', NULL, NULL, NULL, NULL, 'Buchenegg', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('105', NULL, NULL, NULL, NULL, 'Chopfholz', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('106', NULL, NULL, NULL, NULL, 'Entlisberg', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('107', NULL, NULL, NULL, NULL, 'Horgen', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('108', NULL, NULL, NULL, NULL, 'Landforst', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('109', NULL, NULL, NULL, NULL, 'Reidholz-Burghalden', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('110', NULL, NULL, NULL, NULL, 'Richterswil', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('111', NULL, NULL, NULL, NULL, 'Rüschlikon', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('112', NULL, NULL, NULL, NULL, 'Langenrain', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('113', NULL, NULL, NULL, NULL, 'Thalwil', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('114', NULL, NULL, NULL, NULL, 'Uetliberg', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('115', NULL, NULL, NULL, NULL, 'Wädenswil', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('116', NULL, NULL, NULL, NULL, 'Hirzel Dorf', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('117', NULL, NULL, NULL, NULL, 'Horgen Waldegg', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('118', NULL, NULL, NULL, NULL, 'Mullern Chummenwald', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('119', NULL, NULL, NULL, NULL, 'Oberrieden', NULL, NULL, 'other/portrait.jpg', 'width:100%; left:0%; top:-5%;', '0', '0', '1', '2023-05-15 21:00:00', '2023-05-15 21:00:00', '', NULL, NULL),
    ('150', NULL, NULL, NULL, NULL, 'Thalwil', NULL, NULL, 'wappen/thalwil.jpg', 'width:100%; top:0%; left:0%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"info1\",\"info2\",\"info3\",\"info4\",\"info5\"]', '0000-00-00', '-1'),
    ('151', NULL, NULL, NULL, NULL, 'Andere Orte', NULL, NULL, 'wappen/other.jpg', 'width:100%; top:0%; left:0%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"info1\",\"info2\",\"info3\",\"info4\",\"info5\"]', '0000-00-00', '-1'),
    ('1001', '1', NULL, '1', '1', 'Armïn 😂', 'Admïn 🤣', 'Thalwil', 'vptD8fzvXIhv_6X32Zkw2s5s.jpg', 'width:200%; top:0%; left:-50%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"SchlADMINg\",\"Die Registrierung der Domain olzimmerberg.ch\",\"\\u00dcber die Website\",\"2006\",\"Admins! Admins! Admins!\"]', '2006-01-13', '0'),
    ('1002', '1', NULL, '1', '1', 'Volker', 'Vorstand', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', '2017-08-01', '123'),
    ('1003', NULL, NULL, NULL, NULL, 'Fill', 'Up (3; 2000)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"\\u0160il\\u0117n\\u0173 Mi\\u0161kas (Litauen)\",\"OL Lager 2025 in Schweden\",\"Durch Grosseltere\",\"2015\",\"Schnell starte, will am Schluss wird\'s e h\\u00e4rt.\"]', '2000-00-00', NULL),
    ('1004', NULL, NULL, NULL, NULL, 'Fill', 'Up (4; \"\")', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Ein ziemlich langer Text, sodass mindestend drei Zeilen nötig sind zum Rendern.\"]', '0000-00-00', NULL),
    ('1005', NULL, NULL, NULL, NULL, 'Fill', 'Up (5)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1006', NULL, NULL, NULL, NULL, 'Fill', 'Up (6)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1007', NULL, NULL, NULL, NULL, 'Fill', 'Up (7)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1008', NULL, NULL, NULL, NULL, 'Fill', 'Up (8)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1009', NULL, NULL, NULL, NULL, 'Fill', 'Up (9)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1010', NULL, NULL, NULL, NULL, 'Fill', 'Up (10)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1011', NULL, NULL, NULL, NULL, 'Fill', 'Up (11)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL),
    ('1012', NULL, NULL, NULL, NULL, 'Fill', 'Up (12)', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]', NULL, NULL);

-- Table question_categories
INSERT INTO question_categories
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `position`, `name`)
VALUES
    ('1', '1', NULL, '1', '1', '1', '2024-05-15 23:31:07', '2024-05-15 23:31:07', '0', 'Allgemein'),
    ('2', '1', NULL, '1', '1', '1', '2024-05-15 23:31:07', '2024-05-15 23:31:07', '1', 'Website'),
    ('3', '1', NULL, '1', '1', '1', '2024-05-15 23:31:07', '2024-05-15 23:31:07', '2', 'Leer'),
    ('4', '1', NULL, '1', '1', '0', '2024-05-15 23:31:07', '2024-05-15 23:31:07', '1', 'SOFT DELETED');

-- Table questions
INSERT INTO questions
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `category_id`, `on_off`, `created_at`, `last_modified_at`, `ident`, `position_within_category`, `question`, `answer`)
VALUES
    ('1', '1', '25', '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'was_ist_ol', '0', 'Was ist OL?', '![](./bKfDuE4hocQhbw9FGMD7WCW3.jpg)\n\nDas erklären wir dir in unserem kurzen [Youtube Video](https://youtu.be/JVL0vgcnM6c) und in [diesem Dokument](./4a7J72vVQFrqkboyD358S4cf.pdf).'),
    ('2', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'ausprobieren', '1', 'Wie kann ich OL ausprobieren?', 'Am besten kommst du in eines unserer **Trainings** (mit [Youtube Video](https://youtu.be/PjsDAQM1kxA) zur Vorbereitung).\r\n\r\nJährlich organisieren wir ein **OL-Lager** und ein **Tageslager** für Kinder und Jugendliche. Wann genau diese stattfinden, verraten wir dir bei den [Terminen](/termine). '),
    ('3', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'trainings_wann', '2', 'Wann finden diese Trainings statt?', 'Alle Anlässe und damit auch die [Trainings](/termine?filter={\"typ\":\"training\",\"datum\":\"bevorstehend\"}) werden bei uns auf der [**Termine-Seite**](/termine) bekannt gegeben.\n'),
    ('4', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'trainings_wo', '3', 'Wo finden die OL-Trainings statt?', 'Meistens in der Region Zimmerberg, auf [**unseren Karten**](/karten). \n'),
    ('5', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'trainings_anreise', '4', 'Wie reise ich zu einem Training?', 'Entweder du kommst zu Fuss, mit dem Velo, mit dem eigenen Auto, mit einer Fahrgemeinschaft, oder mit dem Trainings-Büssli.\n\nWenn du mit dem Büssli anreisen möchtest, melde dich bitte im Voraus beim [Büsslikoordinator](mailto:buessli@staging.olzimmerberg.ch) an.'),
    ('6', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'ol_anreise', '5', 'Wie reise ich zum OL?', 'Bei manchen Läufen wird im [**Forum**](/news?filter={\"format\":\"forum\"}) eine **öV-Verbindung** bestimmt, mit der die meisten anreisen werden.'),
    ('7', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'zimmerbergler_erkennen', '6', 'Wie erkenne ich andere OL Zimmerberg Mitglieder?', 'An der guten Stimmung und an unserem grün-gelb-schwarzen Dress, das auch tausendfach in den [**Galerien**](/news?filter={\"format\":\"galerie\"}) zu sehen ist.'),
    ('8', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'mitglied_werden', '7', 'Wie werde ich OL Zimmerberg Mitglied?', 'Melde dich [per E-Mail](mailto:aktuariat@staging.olzimmerberg.ch) als Neumitglied an.'),
    ('9', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'kader', '8', 'Gibt es auch schnelle Läufer in der OL Zimmerberg?', 'Ja. Sie schreiben sogar manchmal Beiträge im [**Kaderblog**](/news?filter={\"format\":\"kaderblog\"}).'),
    ('10', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'berichte', '9', 'Wo kann ich Berichte von vergangenen Anlässen nachlesen?', 'Auf der [**News-Seite**](/news).'),
    ('11', '1', NULL, '1', '1', '1', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'verein_organigramm', '10', 'Wer ist im Vorstand der OL Zimmerberg?', 'Porträts unseres Vorstandes sind auf der [**Vereins-Seite**](/verein) zu finden.'),
    ('12', '1', NULL, '1', '1', '2', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'forumsregeln', '0', 'Welche Regeln gelten für das Forum?', '**Im Forum können Mitteilungen aller Art platziert werden (Kommentare, Fragen, Hinweise usw.). Dabei ist folgendes zu beachten:**\r\n\r\n- Ein Eintrag muss mit dem richtigen Namen und Vornamen gemacht werden.\r\n- Es muss eine gültige Emailadresse angegeben werden. An diese Emailadresse wird ein Code geschickt, mit welchem der Eintrag später bearbeitet oder gelöscht werden kann. Um die Gefahr von Spam zu minimieren werden Emailadressen verschlüsselt angezeigt.\r\n- Es liegt im Ermessen des Website-Betreibers, Einträge jederzeit zu entfernen, insbesondere wenn sie verletzenden Inhalt haben, gegen Gesetze verstossen oder Spam enthalten.\r\n'),
    ('13', '1', NULL, '1', '1', '2', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'weshalb_telegram_push', '1', 'Weshalb verwendet ihr Telegram für den Nachrichten-Push?', 'Das ist natürlich eine sehr berechtigte Frage, denn die Chat-App Telegram steht oft datenschutztechnisch in der Kritik, und wird auch politisch teilweise als nicht neutral wahrgenommen.\r\n\r\nDie einfache Antwort ist, dass kein anderes Chat-App einen solchen automatisierten Chat so einfach und kostenfrei anbietet. Um genau zu sein:\r\n\r\n- Threema hat zwar eine solche Funktionalität, sie ist aber kompliziert zu implementieren und kostenpflichtig: Es kostet sowohl für uns jede Nachricht als auch das App für den Nutzer.\r\n- WhatsApp hat zwar die \"WhatsApp Business API\" mit einer ähnichen Funktionalität, diese ist aber ausdrücklich eher an Grossunternehmen gerichtet, und somit auch kostenpflichtig.\r\n- Signal bietet zwar auch eine Möglichkeit, automatische Nachrichten zu schreiben, aber auch diese ist kompliziert und nur mit weiteren Kosten zu implementieren.\r\n\r\nDie Website-Entwickler danken für euer Verständnis. '),
    ('14', '1', NULL, '1', '1', '2', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'benutzername_email_herausfinden', '2', 'Wie finde ich meinen Benutzernamen bzw. E-Mail heraus?', '- Erhälst du den Newsletter? Dann ist es die E-Mail Adresse, an welche der Newsletter versendet wird.\r\n- Hast du Telegram verlinkt? Dann schreib deinem OLZ Bot die Nachricht `/ich`, und er wird dir deinen Benutzernamen und deine E-Mail Adresse mitteilen.\r\n- Wenn du hier angelangt bist, bleibt leider nur noch raten, welche E-Mail Adresse du verwendet haben könntest.\r\n\r\n'),
    ('15', '1', NULL, '1', '1', '2', '1', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'neues_familienmitglied', '3', 'Wie kann ich ein OLZ-Konto für ein Familienmitglied erstellen?', '1. Stelle sicher, dass du eingeloggt bist\r\n2. Gehe auf dein Profil (OLZ-Konto-Menu rechts oben > Profil)\r\n3. Wähle \"Neues Familienmitglied erstellen\"\r\n4. Formular ausfüllen und abschicken (Hinweis: Im Gegensatz zum Hauptkonto dürfen E-Mail und Passwort leer bleiben)\r\n5. Nun hast du im OLZ-Konto-Menu rechts oben die Möglichkeit, zwischen deinem Hauptkonto und dem Kind-Konto hin- und herzuwechseln\r\n\r\n'),
    ('16', '127', '25', '127', '127', '2', '1', '2025-02-12 10:40:04', '2025-02-12 20:38:22', 'markdown', '4', 'Wie kann ich Text formatieren?', '---\n\n**Wie es aussehen wird**\n{.float-right}\n\n**Was du eingibst**\n\n---\n{.clear-both}\n\n# Riesen-Titel (1)\n{.float-right}\n\n`# Riesen-Titel (1)`\n\n---\n{.clear-both}\n\n### Titel (3)\n{.float-right}\n\n`### Titel (3)`\n\n---\n{.clear-both}\n\n*kursiv*\n{.float-right}\n\n`*kursiv*`\n\n---\n{.clear-both}\n\n**fett**\n{.float-right}\n\n`**fett**`\n\n---\n{.clear-both}\n\n- Punkt Eins\n- Punkt Zwei\n{.float-right}\n\n```\n- Punkt Eins\n- Punkt Zwei\n```\n\n---\n{.clear-both}\n\n1. Punkt Eins\n2. Punkt Zwei\n{.float-right}\n\n```\n1. Punkt Eins\n2. Punkt Zwei\n```\n\n---\n{.clear-both}\n\n[Swiss Orienteering](https://swiss-orienteering.ch)\n{.float-right}\n\n`[Swiss Orienteering](https://swiss-orienteering.ch)`\n\n---\n{.clear-both}\n\n**Bild**: Hochladen, dann auf ![](/assets/icns/copy_16.svg) (Kopieren) klicken, und im Text einfügen.\n\n---\n{.clear-both}\n\n**Datei**: Hochladen, dann auf ![](/assets/icns/copy_16.svg) (Kopieren) klicken, und im Text einfügen. `LABEL` durch gewünschten Link-Text ersetzen.\n\n---\n{.clear-both}\n\n[Ausführliche Dokumentation von Markdown (Englisch)](https://www.markdownguide.org/basic-syntax/)'),
    ('17', '1', '25', '1', '1', '1', '0', '2024-05-15 23:33:24', '2024-05-15 23:33:24', 'soft_deleted', '0', 'SOFT DELETED', 'SOFT DELETED');

-- Table quiz_categories
INSERT INTO quiz_categories
    (`id`, `parent_category_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, NULL, 'Kartensymbole', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('2', '1', NULL, NULL, NULL, NULL, 'Geländeformen', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('3', '1', NULL, NULL, NULL, NULL, 'Felsen und Steine', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('4', '1', NULL, NULL, NULL, NULL, 'Gewässer und Sümpfe', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('5', '1', NULL, NULL, NULL, NULL, 'Vegetation', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('6', '1', NULL, NULL, NULL, NULL, 'Künstliche Objekte', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('7', '1', NULL, NULL, NULL, NULL, 'Bahnsymbole', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('8', '1', NULL, NULL, NULL, NULL, 'SOFT DELETED', '0', '2020-08-15 16:51:00', '2020-08-15 16:51:00');

-- Table quiz_skill
INSERT INTO quiz_skill
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, 'Höhenkurve', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('2', NULL, NULL, NULL, NULL, 'Zählkurve', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('3', NULL, NULL, NULL, NULL, 'Formlinie', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('4', NULL, NULL, NULL, NULL, 'Unpassierbare Felswand', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('5', NULL, NULL, NULL, NULL, 'Unpassierbares Gewässer', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('6', NULL, NULL, NULL, NULL, 'Offenes Gebiet', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('7', NULL, NULL, NULL, NULL, 'Befestigte Fläche', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('8', NULL, NULL, NULL, NULL, 'Startpunkt', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('9', NULL, NULL, NULL, NULL, 'SOFT DELETED', '0', '2020-08-15 16:51:00', '2020-08-15 16:51:00');

-- Table quiz_skill_levels
INSERT INTO quiz_skill_levels
    (`id`, `user_id`, `skill_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `value`, `recorded_at`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', '1', '1', '1', NULL, '1', '1', '0.5', '2022-03-17 00:25:26', '1', '2022-03-17 00:25:26', '2022-03-17 00:25:26'),
    ('2', '1', '2', '1', NULL, '1', '1', '0.25', '2022-03-17 00:30:43', '1', '2022-03-17 00:30:43', '2022-03-17 00:30:43'),
    ('3', '2', '5', '2', NULL, '2', '2', '0.25', '2022-03-17 00:30:43', '1', '2022-03-17 00:30:43', '2022-03-17 00:30:43'),
    ('4', '2', '5', '2', NULL, '2', '2', '999999999', '2022-03-17 00:30:43', '0', '2022-03-17 00:30:43', '2022-03-17 00:30:43');

-- Table quiz_skills_categories
INSERT INTO quiz_skills_categories
    (`skill_id`, `category_id`)
VALUES
    ('1', '2'),
    ('2', '2'),
    ('3', '2'),
    ('4', '3'),
    ('5', '4'),
    ('6', '5'),
    ('7', '6'),
    ('8', '7');

-- Table roles
INSERT INTO roles
    (`id`, `username`, `old_username`, `name`, `description`, `parent_role`, `can_have_child_roles`, `guide`, `permissions`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `position_within_parent`, `featured_position`)
VALUES
    ('1', 'anlaesse', NULL, 'Anlässe🎫, \r\nVizepräsi', '# Anlässe🎫, \r\nVizepräsi\n\nOrganisiert Anlässe', NULL, '1', 'Anlässe organisieren:\n- 1 Jahr vorher: abklären\n- ...', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('2', 'material', NULL, 'Material \r\n& Karten', '# Material \r\n& Karten\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('3', 'media', NULL, 'Öffentlich-\r\nkeitsarbeit', '# Öffentlich-\r\nkeitsarbeit\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('4', 'finanzen', NULL, 'Finanzen', '# Finanzen\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '3', NULL),
    ('5', 'praesi', NULL, 'Präsident', '# Präsident\n\nPortrait: ![](./ZntVatFCHj3h8KZh7LyiB9x5.jpg)\n\n[Mein Programm](./c44s3s8QjwZd2WYTEVg3iW9k.pdf)', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '4', '0'),
    ('6', 'aktuariat', NULL, 'Aktuariat & \r\nMitgliederliste', '# Aktuariat & \r\nMitgliederliste\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '5', '1'),
    ('7', 'nachwuchs-ausbildung', NULL, 'Nachwuchs & \r\nAusbildung', '# Nachwuchs & \r\nAusbildung\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '6', NULL),
    ('8', 'nachwuchs-leistungssport', NULL, 'Nachwuchs & Leistungssport', '# Nachwuchs & Leistungssport\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '7', NULL),
    ('9', 'trainings', NULL, 'Training\r\n& Technik', '# Training\r\n& Technik\n\n', NULL, '1', '', ' vorstand termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '8', NULL),
    ('10', 'weekends', NULL, 'Weekends', '# Weekends\n\n', '1', '1', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('11', 'staffeln', NULL, '5er- und Pfingststaffel', '# 5er- und Pfingststaffel\n\n', '1', '1', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('12', 'papiersammlung', NULL, 'Papiersammlung', '# Papiersammlung\n\n', '1', '1', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('13', 'papiersammlung-langnau', NULL, 'Langnau', '# Langnau\n\n', '12', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('14', 'papiersammlung-thalwil', NULL, 'Thalwil', '# Thalwil\n\n', '12', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('15', 'flohmarkt', NULL, 'Flohmarkt', '# Flohmarkt\n\n', '1', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '3', NULL),
    ('16', 'kartenchef', NULL, 'Kartenteam', '# Kartenteam\n\n', '2', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('17', 'kartenteam', NULL, 'Mit dabei', '# Mit dabei\n\n', '16', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('18', 'karten', 'kartenverkauf', 'Kartenverkauf', '# Kartenverkauf\n\n', '2', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', '2'),
    ('19', 'kleider', 'kleiderverkauf', 'Kleiderverkauf', '# Kleiderverkauf\n\n', '2', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', '3'),
    ('20', 'material-group', NULL, 'Material', '# Material\n\n', '2', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '3', NULL),
    ('21', 'materiallager', NULL, 'Lager Thalwil', '# Lager Thalwil\n\n', '20', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('22', 'sportident', NULL, 'SportIdent', '# SportIdent\n\n', '20', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('23', 'buessli', NULL, 'OLZ-Büssli', '# OLZ-Büssli\n\n', '2', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '4', NULL),
    ('24', 'presse', NULL, 'Presse', '# Presse\n\n', '3', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('25', 'website', NULL, 'Homepage', '# Homepage\n\n', '3', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('26', 'holz', NULL, 'Heftli \"HOLZ\"', '# Heftli \"HOLZ\"\n\n', '3', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('27', 'revisoren', NULL, 'Revisoren', '# Revisoren\n\n', '4', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('28', 'ersatzrevisoren', NULL, 'Ersatzrevisor', '# Ersatzrevisor\n\n', '27', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('29', 'sektionen', NULL, 'Sektionen', '# Sektionen\n\n', '5', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('30', 'sektion-adliswil', NULL, 'Adliswil', '# Orientierungslauf-Verein Adliswil\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('31', 'sektion-horgen', NULL, 'Horgen', '# Orientierungslauf-Verein Horgen\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('32', 'sektion-langnau', NULL, 'Langnau', '# Orientierungslauf-Verein Langnau am Albis\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('33', 'sektion-richterswil', NULL, 'Richterswil', '# Orientierungslauf-Verein Richterswil\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '3', NULL),
    ('34', 'sektion-thalwil', NULL, 'Thalwil', '# Orientierungslauf-Verein Thalwil\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '4', NULL),
    ('35', 'sektion-waedenswil', NULL, 'Wädenswil', '# Orientierungslauf-Verein Wädenswil\n\n', '29', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '5', NULL),
    ('36', 'ol-und-umwelt', NULL, 'OL und Umwelt', '# OL und Umwelt\n\n', '5', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('37', 'versa', 'mira', 'Prävention sexueller Ausbeutung', '# Prävention sexueller Ausbeutung\n\n', '5', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('38', 'archiv', NULL, 'Chronik & Archiv', '# Chronik & Archiv\n\n', '6', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('39', 'js-coaches', NULL, 'J+S Coach', '# J+S Coach\n\n', '7', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('40', 'js-leitende', NULL, 'J+S Leitende', '# J+S Leitende\n\n', '7', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('41', 'js-kids', NULL, 'J+S Kids', '# J+S Kids\n\n', '7', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('42', 'scool', NULL, 'sCOOL', '# sCOOL\n\n', '7', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '3', NULL),
    ('43', 'trainer-leistungssport', NULL, 'Trainer Leistungssport', '# Trainer Leistungssport\n\n', '8', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('44', 'team-gold', NULL, 'Team Gold', '# Team Gold\n\n', '8', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('45', 'team-gold-leiter', NULL, 'Leiterteam', '# Leiterteam\n\n', '44', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('46', 'kartentrainings', NULL, 'Kartentraining', '# Kartentraining\n\n', '9', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '0', NULL),
    ('47', 'hallentrainings', NULL, 'Hallentraining', '# Hallentraining\n\n', '9', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('48', 'lauftrainings', NULL, 'Lauftraining', '# Lauftraining\n\n', '9', '0', '', ' termine termine_admin ', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '2', NULL),
    ('49', 'nachwuchs-kontakt', NULL, 'Kontaktperson Nachwuchs', '# Kontaktperson Nachwuchs\n\n', '7', '0', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '4', NULL),
    ('50', 'gold-athleten', NULL, 'Athleten', '# Athleten\n\n', '44', '0', '', 'kaderblog', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06', '1', NULL),
    ('51', 'fan-olz-elite', NULL, 'Fan OLZ Elite', '# Fan OLZ Elite\n\n', '8', '1', '', '', NULL, NULL, NULL, NULL, '1', '2024-03-20 20:52:06', '2024-03-20 20:52:06', '3', '4'),
    ('52', 'deleted-role', NULL, 'SOFT DELETED', '# SOFT DELETED\n\n', '5', '0', '', '', NULL, NULL, NULL, NULL, '0', '2024-03-20 20:52:06', '2024-03-20 20:52:06', '4', NULL);

-- Table snippets
INSERT INTO snippets
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `text`)
VALUES
    ('1', NULL, NULL, NULL, NULL, '1', '2024-03-25 00:05:14', '2024-03-25 00:05:14', '**OL-Training (im Sommerhalbjahr)**\n\n*für Kartentechnik und Orientierung im Wald (ab 6 Jahren)*\n\njeden Dienstag gemäss Terminkalender\n\n[Trainingsplan 2020](/pdf/Trainingsplan_2020.pdf)\n\n**Hallentraining (im Winterhalbjahr)**\n\n*für Kondition, Kraft, Schnelligkeit mit viel Spiel &amp; Spass (ab 6 Jahren)*\n\nSchulhaus Schweikrüti Gattikon (Montag 18.10 - 19.45 Uhr)\n\nSchulhaus Steinacher Au (Dienstag, 18.00-19.15-20.30 Uhr)\n\nTurnhalle Platte Thalwil (Freitag, 20.15-22.00 Uhr, Spiel)\n\n**Longjoggs (im Winterhalbjahr)**\n\n*für Ausdauer und Kondition (Jugendliche &amp; Erwachsene)*\n\nan Sonntagen gemäss Terminkalender'),
    ('12', '1', NULL, '1', '1', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '{.equal}\nKartentyp | Format | Karte gedruckt | Karte digital\n--- | --- | --- | ---\nWald-/Dorf-Karte | A4 | 2.50 | 2.50\nWald-/Dorf-Karte | A3 | 4.00 | 2.50\nSchulhauskarte | A4 | 1.50 | 1.00\n\n(Kartenpreise gültig ab 1.1.2019)\n\n[Karten bestellen](mailto:karten@staging.olzimmerberg.ch?subject=Bestellung%20OL-Karten)\n\n[Kartenverzeichnis swiss orienteering](https://www.swiss-orienteering.ch/karten/index.php)\n'),
    ('22', NULL, NULL, NULL, NULL, '1', '2024-03-25 00:05:14', '2024-03-25 00:05:14', '⚠️ Wichtige Information! ⚠️'),
    ('23', NULL, NULL, NULL, NULL, '1', '2024-03-25 00:05:14', '2024-03-25 00:05:14', '⚠️ Abgesagt! ⚠️'),
    ('24', NULL, NULL, NULL, NULL, '1', '2024-03-25 00:05:14', '2024-03-25 00:05:14', '⚠️ Wichtig! ⚠️\n\n![](./oCGvpb96V6bZNLoQNe8djJgw.jpg) [PDF](./AXfZYP3eyLKTWJmfBRGTua7H.pdf)\n\n1. [Intern](/service)\n2. [Extern](https://solv.ch)\n\n- [E-Mail](mailto:user@staging.olzimmerberg.ch)');

-- Table solv_events
INSERT INTO solv_events
    (`solv_uid`, `date`, `duration`, `kind`, `day_night`, `national`, `region`, `type`, `name`, `link`, `club`, `map`, `location`, `coord_x`, `coord_y`, `deadline`, `entryportal`, `start_link`, `rank_link`, `last_modification`)
VALUES
    ('6822', '2014-06-29', '1', 'foot', 'day', '1', 'GL/GR', '**A', '6. Nationaler OL 🥶', 'http://www.olg-chur.ch', 'OLG Chur 🦶', 'Crap Sogn Gion/Curnius ⛰️', '', '735550', '188600', '2014-06-10', '1', '', '', '2014-03-05 00:38:15'),
    ('7411', '2015-06-21', '1', 'foot', 'day', '0', 'ZH/SH', '402S', '59. Schweizer 5er Staffel', 'http://www.5erstaffel.ch', 'OLC Kapreolo', 'Chomberg', '', '693700', '259450', '2015-06-01', '1', '', '', '2015-05-15 02:43:20'),
    ('12345', '2020-08-22', '1', 'foot', 'day', '1', 'ZH/SH', '402S', 'Grossanlass', 'http://www.grossanlass.ch', 'OLG Bern', 'Grosswald', '', '600000', '200000', '2020-08-10', '1', '', '', '2015-05-15 02:43:20');

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

-- Table termin_infos

-- Table termin_label_map
INSERT INTO termin_label_map
    (`termin_id`, `label_id`)
VALUES
    ('2', '6'),
    ('3', '3'),
    ('4', '3'),
    ('5', '4'),
    ('5', '5'),
    ('6', '3'),
    ('7', '3'),
    ('8', '3'),
    ('9', '3'),
    ('10', '1'),
    ('10', '5'),
    ('11', '1'),
    ('11', '2'),
    ('11', '5'),
    ('12', '1'),
    ('12', '5'),
    ('13', '1'),
    ('13', '6'),
    ('1001', '1'),
    ('1001', '6');

-- Table termin_labels
INSERT INTO termin_labels
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `details`, `icon`, `on_off`, `created_at`, `last_modified_at`, `ident`, `position`)
VALUES
    ('1', NULL, NULL, NULL, NULL, 'Jahresprogramm', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'programm', '0'),
    ('2', NULL, NULL, NULL, NULL, 'Weekends', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'weekend', '1'),
    ('3', NULL, NULL, NULL, NULL, 'Trainings', '![](./QQ8ZApZjsNSBM2wKrkRQxXZG.jpg) Komm an eines unserer Trainings! [Trainingskonzept als PDF](./6f6novQPv2fjHGzzguXE6nzi.pdf)', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'training', '2'),
    ('4', NULL, NULL, NULL, NULL, 'OLZ-Trophy', 'Nimm teil an der OLZ Trophy, einer Reihe von OLs für alle Leistungsstufen!', 'EM8hA6vye74doeon2RWzZyRf.svg', '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'trophy', '3'),
    ('5', NULL, NULL, NULL, NULL, 'Wettkämpfe', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'ol', '4'),
    ('6', NULL, NULL, NULL, NULL, 'Vereinsanlässe', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'club', '5'),
    ('7', NULL, NULL, NULL, NULL, 'SOFT DELETED', '', NULL, '0', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'club', '5');

-- Table termin_locations
INSERT INTO termin_locations
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `details`, `latitude`, `longitude`, `image_ids`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, 'Chilbiplatz (Thalwil)', '**Infrastruktur:** keine, Garderoben im Freien, keine WCs vorhanden\n\n**öV:** Bus bis \"Thalwil, Chilbiplatz\", oder Zug bis \"Thalwil\", dann 10 min Fussmarsch\n\n**Parkplätze:** auf dem Chilbiplatz', '47.288245737451', '8.5627673724772', '[\"2ZiW6T9biPNjEERzj5xjLRDz.jpg\"]', '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06'),
    ('2', NULL, NULL, NULL, NULL, 'Stumpenhölzlimooshütte (Landforst)', NULL, '47.267405813501', '8.5698615816165', NULL, '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06'),
    ('3', NULL, NULL, NULL, NULL, 'Sportanlage Brand', NULL, '47.288737009648', '8.5510643251822', NULL, '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06'),
    ('4', NULL, NULL, NULL, NULL, 'SOFT DELETED', NULL, '47.2631769788326', '8.589723706823843', NULL, '0', '2023-06-11 19:39:06', '2023-06-11 19:39:06');

-- Table termin_notification_templates

-- Table termin_notifications

-- Table termin_template_label_map
INSERT INTO termin_template_label_map
    (`termin_template_id`, `label_id`)
VALUES
    ('1', '3'),
    ('2', '3'),
    ('3', '1'),
    ('3', '2'),
    ('3', '6');

-- Table termin_templates
INSERT INTO termin_templates
    (`id`, `location_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `start_time`, `duration_seconds`, `deadline_earlier_seconds`, `deadline_time`, `min_participants`, `max_participants`, `min_volunteers`, `max_volunteers`, `newsletter`, `title`, `text`, `image_ids`, `on_off`, `created_at`, `last_modified_at`, `should_promote`)
VALUES
    ('1', '3', '2', '47', '2', '2', '18:15:00', '5400', NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Hallentraining Brand', 'für alle ab 14 Jahren\n\n', '[]', '1', '2023-10-02 17:03:21', '2023-10-02 17:03:21', '0'),
    ('2', NULL, '2', '46', '2', '2', '18:30:00', '5400', '172800', '23:59:59', NULL, NULL, NULL, NULL, '0', 'Kartentraining: <<< TODO >>>', 'Karte: <<< TODO >>>\r\nOrganisator: <<< TODO >>>\n\n[Datei](./qjhUey6Lc6svXsmUcSaguWkJ.pdf)[Link](https://solv.ch)', '[\"bv3KeYVKDJNg3MTyjhSQsDRx.jpg\"]', '1', '2023-10-02 17:06:51', '2023-10-02 17:06:51', '1'),
    ('3', NULL, '2', '10', '2', '2', '09:00:00', '108000', '604800', '23:59:59', NULL, NULL, NULL, NULL, '1', '<<< TODO >>> Weekend', '\n\n', NULL, '1', '2023-10-02 18:20:53', '2023-10-02 18:20:53', '0'),
    ('4', NULL, '2', '10', '2', '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Minimal', '\n\n', NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53', '0'),
    ('5', NULL, '2', '10', '2', '2', '22:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Open end', '\n\n', NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53', '0'),
    ('6', NULL, '2', '10', '2', '2', NULL, '3600', NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Unknown start', '\n\n', NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53', '0'),
    ('7', NULL, '2', '10', '2', '2', NULL, '3600', NULL, NULL, NULL, NULL, NULL, NULL, '0', 'SOFT DELETED', 'SOFT DELETED', NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53', '0');

-- Table termine
INSERT INTO termine
    (`id`, `start_date`, `start_time`, `end_date`, `end_time`, `title`, `go2ol`, `text`, `on_off`, `xkoord`, `ykoord`, `solv_uid`, `newsletter`, `deadline`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`, `participants_registration_id`, `volunteers_registration_id`, `num_participants`, `min_participants`, `max_participants`, `num_volunteers`, `min_volunteers`, `max_volunteers`, `location_id`, `image_ids`, `from_template_id`, `should_promote`)
VALUES
    ('1', '2020-01-02', '00:00:00', NULL, '00:00:00', 'Berchtoldstag 🥈', '', '\n\n', '1', '0', '0', '0', '1', NULL, NULL, NULL, NULL, NULL, '2019-02-22 01:17:09', '2020-01-01 17:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
    ('2', '2020-06-06', '10:15:00', NULL, '12:30:00', 'Brunch OL', '', 'Dä Samschtig gits en bsunderä Läckerbissä!\n\n[Infos](./MIGRATED0000000000020001.pdf)', '1', '685000', '236100', '0', '1', NULL, NULL, NULL, NULL, NULL, '2019-12-31 07:17:09', '2019-12-31 20:17:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
    ('3', '2020-08-18', '18:30:00', NULL, '20:00:00', 'Training 1', '', '\n\n', '1', NULL, NULL, '0', '0', '2020-08-17 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, '2', '0'),
    ('4', '2020-08-25', '18:30:00', NULL, '20:00:00', 'Training 2', '', '\n\n', '1', '683498', '236660', '0', '0', '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2', '0'),
    ('5', '2020-08-26', '18:00:00', '2020-08-26', '19:30:00', 'Milchsuppen-Cup, OLZ Trophy 4. Lauf', '', 'Organisation: OL Zimmerberg\r\nKarte: Chopfholz\n\n[OLZ Trophy 2020](/termine?filter={\"typ\":\"trophy\",\"datum\":\"2020\",\"archiv\":\"ohne\"})\r\n[Anmeldung](https://forms.gle/ixS1ZD22PmbdeYcy6)\r\n[Ausschreibung](./MIGRATED0000000000050001.pdf)', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2019-11-20 09:04:26', '2020-08-24 22:40:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Ffpi3PK5wBjKfN4etpvGK3ti.jpg\"]', NULL, '0'),
    ('6', '2020-09-01', '18:30:00', NULL, '20:00:00', 'Training 3', '', '\n\n', '1', '684376', '236945', '0', '0', '2020-08-31 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '0'),
    ('7', '2020-09-08', '18:00:00', NULL, '19:30:00', 'Training 4', '', '\n\n[Details](./Kzt5p5g6cjM5k9CXdVaSsGFx.pdf)', '1', '0', '0', '0', '0', '2020-09-06 23:59:59', '2', NULL, '2', '2', '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '0'),
    ('8', '2020-08-11', '18:30:00', NULL, '20:00:00', 'Trainingsstart', '', '\n\n', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '0'),
    ('9', '2020-08-04', '18:30:00', NULL, '20:00:00', 'Training -1', '', '\n\n', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '0'),
    ('10', '2020-08-22', '00:00:00', NULL, '00:00:00', 'Grossanlass', 'gal', 'Mit allem drum und dran!\n\n', '1', NULL, NULL, '12345', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"659gCbqzigX8D37XgWMbedB3.jpg\"]', NULL, '1'),
    ('11', '2020-09-13', '00:00:00', '2020-09-19', '00:00:00', 'Mehrtägeler', 'sow', 'Mir werdeds schaffe!\n\n', '1', NULL, NULL, '123456', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
    ('12', '2020-08-16', '17:00:00', '2020-08-17', '17:00:00', '24h-OL', '24h', 'Dauert genau 24h\n\n', '1', NULL, NULL, '1234567', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
    ('13', '2021-03-12', '18:30:00', NULL, NULL, 'Mitgliederversammlung', NULL, 'schon jetzt für 2021 geplant!\n\n', '1', NULL, NULL, NULL, '0', '2021-03-05 23:59:59', NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"8EKYh2n8DZWShYMWo9ZRnor5.jpg\"]', NULL, '1'),
    ('14', '2021-03-11', '18:30:00', NULL, NULL, 'SOFT DELETED', NULL, 'SOFT DELETED', '0', NULL, NULL, NULL, '0', '2021-03-05 23:59:59', NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, '1'),
    ('1001', '2006-01-13', '18:00:00', NULL, '18:00:00', 'Gründungsversammlung OL Zimmerberg', NULL, 'wir gründen uns!\n\n', '1', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');

-- Table throttlings

-- Table users
INSERT INTO users
    (`id`, `username`, `old_username`, `password`, `email`, `first_name`, `last_name`, `permissions`, `root`, `email_is_verified`, `email_verification_token`, `gender`, `street`, `postal_code`, `city`, `region`, `country_code`, `birthdate`, `phone`, `created_at`, `last_modified_at`, `last_login_at`, `parent_user`, `member_type`, `member_last_paid`, `wants_postal_mail`, `postal_title`, `postal_name`, `joined_on`, `joined_reason`, `left_on`, `left_reason`, `solv_number`, `si_card_number`, `notes`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `avatar_image_id`, `ahv_number`, `dress_size`)
VALUES
    ('1', 'admin', NULL, '$2y$10$RNMfUZk8cdW.VnuC9XZ0tuZhnhnygy9wdhVfs0kkeFN5M0XC1Abce', 'admin@staging.olzimmerberg.ch', 'Armin 😂', 'Admin 🤣', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', '8sVwnV3aAEtQUUxmQYFmojMs.jpg', NULL, NULL),
    ('2', 'vorstand', NULL, '$2y$10$xD9LwSFXo5o0l02p3Jzcde.CsfqFxzLWh2jkuGF19yE0Saqq3J3Kq', '', 'Volker', 'Vorstand', 'ftp webdav snippet_1 aktuell galerie weekly_picture faq', 'OLZ Dokumente/vorstand', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('3', 'karten', 'kartenverkauf', '$2y$10$0R5z1L2rbQ8rx5p5hURaje70L0CaSJxVPcnmEhz.iitKhumblmKAW', 'karen@staging.olzimmerberg.ch', 'Karen', 'Karten', 'ftp webdav', 'OLZ Dokumente/karten', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', 'oyLeyPTaCfmadcm5ShEJ236e.jpg', NULL, NULL),
    ('4', 'hackerman', NULL, '$2y$10$5PZTo/AGC89BX.m637GmGekZaktFet7nno0P8deGt.ASOCHxNVwVe', 'hackerman@staging.olzimmerberg.ch', 'Hacker', 'Man', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('5', 'benutzer', NULL, '$2y$10$DluJUi60YHZh6LksqClkmeTX.Giyt3kLHZG3HddV6Zm1UoYXzyXqC', 'nutzer@staging.olzimmerberg.ch', 'Be', 'Nutzer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('6', 'parent', NULL, '$2y$10$iU9SqVRurO.4N1ak1j.p/OP0qT6rEst7.mLd/hM7EzyfI5rBX7nva', 'parent@staging.olzimmerberg.ch', 'Eltern', 'Teil', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('7', 'child1', NULL, NULL, 'child1@staging.olzimmerberg.ch', 'Kind', 'Eins', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('8', 'child2', NULL, '', '', 'Kind', 'Zwei', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('9', 'kaderlaeufer', NULL, '$2y$10$YTelsKQLm.Ps9lnXRbDIAOP3SqkE8m9Z/Uw75X4wtyBUA1xY95Lui', 'kaderlaeufer@staging.olzimmerberg.ch', 'Kader', 'Läufer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL),
    ('10', 'soft.deleted', NULL, '$2y$10$YTelsKQLm.Ps9lnXRbDIAOP3SqkE8m9Z/Uw75X4wtyBUA1xY95Lui', 'soft.deleted@staging.olzimmerberg.ch', 'SOFT DELETED', 'SOFT DELETED', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL),
    ('42', 'monitoring', NULL, '', 'website@staging.olzimmerberg.ch', 'Monitoring', 'Bot', ' command_cache:clear command_cache:warmup command_olz:db-backup command_olz:db-reset command_olz:monitor-logs command_olz:monitor-backup command_olz:test command_olz:send-telegram-configuration command_messenger:consume ', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL);

-- Table users_roles
INSERT INTO users_roles
    (`user_id`, `role_id`)
VALUES
    ('1', '5'),
    ('1', '7'),
    ('1', '22'),
    ('1', '23'),
    ('1', '25'),
    ('1', '49'),
    ('2', '4'),
    ('2', '17'),
    ('2', '23'),
    ('3', '4'),
    ('3', '16'),
    ('3', '18'),
    ('3', '25'),
    ('4', '22'),
    ('4', '25'),
    ('9', '50');

-- Table weekly_picture
INSERT INTO weekly_picture
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `datum`, `image_id`, `text`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, '2020-01-01', 'ed48ksmyjVgRsaKXUXmmcbRN.jpg', 'Neujahrs-Impression vom Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17'),
    ('2', NULL, NULL, NULL, NULL, '2020-01-02', 'C8k84ncvWyVptk6kjtMJxTUu.jpg', 'Berchtoldstag im Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17'),
    ('3', NULL, NULL, NULL, NULL, '2020-03-10', 'C8k84ncvWyVptk6kjtMJxTUu.jpg', 'SOFT DELETED', '0', '2022-10-24 16:52:17', '2022-10-24 16:52:17');

COMMIT;
