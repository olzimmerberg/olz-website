-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: DoctrineMigrations\Version20240406120652

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
    ('DoctrineMigrations\\Version20240406120652', '2024-04-06 14:08:51', '699');

-- Table downloads
INSERT INTO downloads
    (`id`, `name`, `position`, `file_id`, `on_off`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`)
VALUES
    ('1', 'Statuten', '0', 'MIGRATED0000000000010001.pdf', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('2', '---', '1', NULL, '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('3', 'Spesenreglement', '2', 'XV4x94BJaf2JCPWvB8DDqTyt.pdf', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53');

-- Table karten
INSERT INTO karten
    (`id`, `kartennr`, `name`, `center_x`, `center_y`, `jahr`, `massstab`, `ort`, `zoom`, `typ`, `vorschau`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `latitude`, `longitude`)
VALUES
    ('1', '1086', 'Landforst 🗺️', '685000', '236100', '2017', '1:10\'000', NULL, '8', 'ol', 'MIGRATED0000000000010001.jpg', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', NULL, NULL),
    ('2', '0', 'Eidmatt', '693379', '231463', '2020', '1:1\'000', 'Wädenswil', '2', 'scool', '', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', NULL, NULL),
    ('3', '0', 'Horgen Dorfkern', '687900', '234700', '2011', '1:2\'000', 'Horgen', '8', 'stadt', '6R3bpgwcCU3SfUF8vCpepzRJ.jpg', NULL, NULL, NULL, NULL, '1', '2024-02-22 23:07:58', '2024-02-22 23:07:58', NULL, NULL);

-- Table links
INSERT INTO links
    (`id`, `name`, `url`, `position`, `on_off`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`)
VALUES
    ('1', 'SOLV', 'https://swiss-orienteering.ch', '0', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('2', '---', '---', '1', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53'),
    ('3', 'GO2OL', 'https://go2ol.ch', '2', '1', NULL, NULL, NULL, NULL, '2023-11-14 23:26:53', '2023-11-14 23:26:53');

-- Table messenger_messages

-- Table news
INSERT INTO news
    (`id`, `author_user_id`, `author_role_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `termin`, `published_date`, `published_time`, `newsletter`, `title`, `teaser`, `content`, `image_ids`, `external_url`, `author_name`, `author_email`, `format`, `tags`, `counter`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('3', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '00:00:00', '1', 'Frohes neues Jahr! 🎆', '<BILD1>Im Namen des Vorstands wünsche ich euch allen ein **frohes neues Jahr**! 🎆 <DATEI=MIGRATED0000000000030001.pdf text=\"Neujahrsansprache als PDF\">', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. *Ein Bisschen.* Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n<BILD2>\n\nUnd hier nochmals das Emoji: 🎆.\n\nUnd hier nochmals die <DATEI=MIGRATED0000000000030001.pdf text=\"Neujahrsansprache als PDF\">\n\n Und hier ein riesiger Link: https://verylongsubdomain.alsoaverylongdomain.long/andlastbutnotleastaverylongpathwhichmustreallybeonlyjustonewordsuchthatthiswordmustbebrokenupexplicitlyinordertonotcompletelybreaktheUI', '[\"MIGRATED0000000000030001.jpg\",\"MIGRATED0000000000030002.jpg\"]', '', 'prä', NULL, 'aktuell', '', '0', '1', '2021-06-28 16:37:03', '2021-06-28 16:37:03'),
    ('4', '1', '25', '1', NULL, NULL, NULL, '0', '2020-03-16', NULL, '1', 'Neues System für News-Einträge online!', '<BILD1>Heute ging ein neues System für **News-Einträge online**. Nach und nach sollen Aktuell- Galerie- Kaderblog- und Forumseinträge auf das neue System migriert werden. Siehe <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"Motivationsschreiben\">.', 'All diese Einträge sind ähnlich: Sie werden von einem Autor erstellt, enthalten Titel und Text, evtl. Teaser, Bilder und angehängte Dateien, und sind für alle *OL-Zimmerberg-Mitglieder* von Interesse. Deshalb **vereinheitlichen** wir nun diese verschiedenen Einträge.\n\nDie Gründe für die Änderung haben wir in <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"diesem Schreiben\"> zusammengefasst.\n\n<BILD1>', '[\"xkbGJQgO5LFXpTSz2dCnvJzu.jpg\"]', NULL, '', NULL, 'aktuell', '  ', '0', '1', '2020-03-16 14:51:00', '2020-03-16 14:51:00'),
    ('5', '1', NULL, '1', NULL, NULL, NULL, '0', '2020-08-15', NULL, '1', 'Neues System für News-Einträge bewährt sich', 'Das neue System für News-Einträge scheint *gut anzukommen*. Neu können eingeloggte Benutzer in ihren News-Einträgen (ehem. Forumseinträgen) auch Bilder und Dateien einbinden.', '', '[]', NULL, '', NULL, 'aktuell', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('6', '3', NULL, '3', NULL, NULL, NULL, '0', '2020-01-02', NULL, '1', 'Berchtoldstagsgalerie 2020', '', '', '[\"eGbiJQgOyLF5p6S92kC3vTzE.jpg\",\"Frw83uTOyLF5p6S92kC7zpEW.jpg\"]', NULL, '', NULL, 'galerie', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('7', '2', '26', '2', NULL, NULL, NULL, '0', '2020-08-15', NULL, '1', 'Test Video', '', 'https://youtu.be/JVL0vgcnM6c', '[\"aRJIflbxtkF5p6S92k470912.jpg\"]', NULL, '', NULL, 'video', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('8', '1', '5', '1', NULL, '1', '1', '0', '2020-01-15', '16:51:00', '1', 'Hinweis vom Präsi', '', 'Auch der **Präsident** schreibt im Forum!', '[\"9GjbtlsSu96AWZ-oH0rHjxup.jpg\",\"zUXE3aKfbK3edmqS35FhaF8g.jpg\"]', NULL, NULL, NULL, 'forum', '  ', '0', '1', '2020-01-15 16:51:00', '2020-01-15 16:51:00'),
    ('9', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-06', '16:51:00', '1', 'Longjogg am Sonntag', '', 'Ich will mich nicht einloggen, aber hier mein Beitrag:\n\nIch organisiere einen **Longjogg am Sonntag**.\n\nPackt *warme* Kleidung ein.\n\nWer zum Pastaessen bleiben will, muss sich bis am Samstagmittag bei mir melden.\n\nAusserdem habe ich weitere unfassbar komplizierte Anforderungen an meine Gäste und geht dermassen tief ins Detail, dass dieser Forumseintrag unter keinen Umständen in seiner ganzen Länge in der Liste der Forumseinträge angezeigt werden sollte!\n\nAnreise:\nRichterswil ab 09:30\nWädenswil ab 09:31\nHorgen ab 09:32\nThalwil ab 09:33\nZürich HB ab 09:34\nBei mir an 09:35', '[]', NULL, 'Anonymous', 'anonymous@gmail.com', 'forum', '  ', '0', '1', '2020-01-06 16:51:00', '2020-01-06 16:51:00'),
    ('10', '9', '50', '9', NULL, '9', '9', '0', '2020-08-15', '16:51:00', '1', 'Dank dem neuen News-System trainiere ich 50% besser!', '', '<BILD1>Ich bin total Fan vom neuen News-System! Meine Trainingsleistung hat deswegen um 50% zugenommen (keine Ahnung wieso). Hier die Beweise:\n\n- https://verylongsubdomain.alsoaverylongdomain.long/andlastbutnotleastaverylongpathwhichmustreallybeonlyjustonewordsuchthatthiswordmustbebrokenupexplicitlyinordertonotcompletelybreaktheUI\n- <DATEI=gAQa_kYXqXTP1_DKKU1s1pGr.csv text=\"Beweisstück A\">\n- <DATEI=8kCalo9sQtu2mrgrmMjoGLUW.pdf text=\"Beweisstück B\">\n\n<BILD2>', '[\"DvDB8QkHcGuxQ4lAFwyvHnVd.jpg\",\"OOVJIqrWlitR_iTZuIIhztKC.jpg\"]', NULL, NULL, NULL, 'kaderblog', '  ', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00'),
    ('1202', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', NULL, '1', 'Neujahrsgalerie 📷 2020', '', '', '[\"MIGRATED0000000012020001.jpg\",\"MIGRATED0000000012020002.jpg\",\"MIGRATED0000000012020003.jpg\",\"MIGRATED0000000012020004.jpg\",\"MIGRATED0000000012020005.jpg\"]', NULL, '', NULL, 'galerie', '  ', '0', '1', '2020-08-15 14:51:00', '2020-08-15 14:51:00'),
    ('1203', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-08-13', NULL, '1', 'Test Video', '', 'https://youtu.be/JVL0vgcnM6c', '[\"MIGRATED0000000012030001.jpg\"]', NULL, '', NULL, 'video', '  ', '0', '1', '2020-08-13 14:51:00', '2020-08-13 14:51:00'),
    ('2901', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '21:45:37', '0', 'Guets Nois! 🎉', '', 'Hoi zäme, au vo mier no *Guets Nois*! 🎉', '[]', NULL, 'Bruno 😃 Beispielmitglied', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-01 21:45:37', '2020-01-01 21:45:37'),
    ('2902', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-03', '18:42:01', '0', 'Verspätete Neujahrsgrüsse', '', 'Has vergesse, aber au vo *mier* no Guets Nois!', '[]', NULL, 'Erwin Exempel', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-03 18:42:01', '2020-01-03 18:42:01'),
    ('2903', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-06', '06:07:08', '0', 'Hallo', '', 'Mir hend paar **OL-Usrüschtigs-Gegeständ** us ferne Länder mitbracht.\n\nSchriibed doch es Mail wenn er öppis devoo wetted', '[]', NULL, 'Drei Könige', 'beispiel@olzimmerberg.ch', 'forum', '', '0', '1', '2020-01-06 06:07:08', '2020-01-06 06:07:08'),
    ('6401', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2019-01-01', '15:15:15', '0', 'Saisonstart 2019!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Hoi zäme, dieser Eintrag wurde noch mit dem alten System geschrieben. Hier die Anhänge:\n<BILD1>\n<BILD2>\n<DL1>\n<DL2>', '[\"MIGRATED0000000064010001.jpg\",\"MIGRATED0000000064010002.jpg\"]', NULL, 'Gold Junge', NULL, 'kaderblog', '', '0', '1', '2019-01-01 15:15:15', '2019-01-01 15:15:15'),
    ('6402', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2019-08-15', '15:15:15', '0', 'Neuer Eintrag auf meinem externen Blog', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Kleiner Teaser', '[]', 'https://www.external-blog.com/entry/1234', 'Eliteläuferin', NULL, 'kaderblog', '', '0', '1', '2019-08-15 15:15:15', '2019-08-15 15:15:15'),
    ('6403', NULL, NULL, NULL, NULL, NULL, NULL, '0', '2020-01-01', '15:15:15', '0', 'Saisonstart 2020!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', '<BILD1> Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: <DATEI=MIGRATED0000000064030001.pdf text=\"Extrem Harte Trainingsstrategie\">', '[\"MIGRATED0000000064030001.jpg\"]', NULL, 'Gold Junge', NULL, 'kaderblog', '', '0', '1', '2020-01-01 15:15:15', '2020-01-01 15:15:15');

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
    ('7', '1', NULL, NULL, NULL, NULL, 'Bahnsymbole', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00');

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
    ('8', NULL, NULL, NULL, NULL, 'Startpunkt', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00');

-- Table quiz_skill_levels
INSERT INTO quiz_skill_levels
    (`id`, `user_id`, `skill_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `value`, `recorded_at`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', '1', '1', '1', NULL, '1', '1', '0.5', '2022-03-17 00:25:26', '1', '2022-03-17 00:25:26', '2022-03-17 00:25:26'),
    ('2', '1', '2', '1', NULL, '1', '1', '0.25', '2022-03-17 00:30:43', '1', '2022-03-17 00:30:43', '2022-03-17 00:30:43'),
    ('3', '2', '5', '2', NULL, '2', '2', '0.25', '2022-03-17 00:30:43', '1', '2022-03-17 00:30:43', '2022-03-17 00:30:43');

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
    (`id`, `username`, `old_username`, `name`, `description`, `page`, `parent_role`, `index_within_parent`, `featured_index`, `can_have_child_roles`, `guide`, `permissions`, `title`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', 'anlaesse', NULL, 'Anlässe🎫, \r\nVizepräsi', 'Organisiert Anlässe', '', NULL, '0', NULL, '1', 'Anlässe organisieren:\n- 1 Jahr vorher: abklären\n- ...', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('2', 'material', NULL, 'Material \r\n& Karten', '', '', NULL, '1', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('3', 'media', NULL, 'Öffentlich-\r\nkeitsarbeit', '', '', NULL, '2', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('4', 'finanzen', NULL, 'Finanzen', '', '', NULL, '3', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('5', 'praesi', NULL, 'Präsident', 'Portrait: ![](./ZntVatFCHj3h8KZh7LyiB9x5.jpg)\n\n[Mein Programm](./c44s3s8QjwZd2WYTEVg3iW9k.pdf)', '', NULL, '4', '0', '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('6', 'aktuariat', NULL, 'Aktuariat & \r\nMitgliederliste', '', '', NULL, '5', '1', '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('7', 'nachwuchs-ausbildung', NULL, 'Nachwuchs & \r\nAusbildung', '', '', NULL, '6', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('8', 'nachwuchs-leistungssport', NULL, 'Nachwuchs & Leistungssport', '', '', NULL, '7', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('9', 'trainings', NULL, 'Training\r\n& Technik', '', '', NULL, '8', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('10', 'weekends', NULL, 'Weekends', '', '', '1', '0', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('11', 'staffeln', NULL, '5er- und Pfingststaffel', '', '', '1', '1', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('12', 'papiersammlung', NULL, 'Papiersammlung', '', '', '1', '2', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('13', 'papiersammlung-langnau', NULL, 'Langnau', '', '', '12', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('14', 'papiersammlung-thalwil', NULL, 'Thalwil', '', '', '12', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('15', 'flohmarkt', NULL, 'Flohmarkt', '', '', '1', '3', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('16', 'kartenchef', NULL, 'Kartenteam', '', '', '2', '0', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('17', 'kartenteam', NULL, 'Mit dabei', '', '', '16', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('18', 'karten', 'kartenverkauf', 'Kartenverkauf', '', '', '2', '1', '2', '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('19', 'kleider', 'kleiderverkauf', 'Kleiderverkauf', '', '', '2', '2', '3', '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('20', 'material-group', NULL, 'Material', '', '', '2', '3', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('21', 'materiallager', NULL, 'Lager Thalwil', '', '', '20', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('22', 'sportident', NULL, 'SportIdent', '', '', '20', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('23', 'buessli', NULL, 'OLZ-Büssli', '', '', '2', '4', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('24', 'presse', NULL, 'Presse', '', '', '3', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('25', 'website', NULL, 'Homepage', '', '', '3', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('26', 'holz', NULL, 'Heftli \"HOLZ\"', '', '', '3', '2', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('27', 'revisoren', NULL, 'Revisoren', '', '', '4', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('28', 'ersatzrevisoren', NULL, 'Ersatzrevisor', '', '', '27', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('29', 'sektionen', NULL, 'Sektionen', '', '', '5', '0', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('30', 'sektion-adliswil', NULL, 'Adliswil', '', '', '29', '0', NULL, '0', '', '', 'Orientierungslauf-Verein Adliswil', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('31', 'sektion-horgen', NULL, 'Horgen', '', '', '29', '1', NULL, '0', '', '', 'Orientierungslauf-Verein Horgen', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('32', 'sektion-langnau', NULL, 'Langnau', '', '', '29', '2', NULL, '0', '', '', 'Orientierungslauf-Verein Langnau am Albis', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('33', 'sektion-richterswil', NULL, 'Richterswil', '', '', '29', '3', NULL, '0', '', '', 'Orientierungslauf-Verein Richterswil', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('34', 'sektion-thalwil', NULL, 'Thalwil', '', '', '29', '4', NULL, '0', '', '', 'Orientierungslauf-Verein Thalwil', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('35', 'sektion-waedenswil', NULL, 'Wädenswil', '', '', '29', '5', NULL, '0', '', '', 'Orientierungslauf-Verein Wädenswil', NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('36', 'ol-und-umwelt', NULL, 'OL und Umwelt', '', '', '5', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('37', 'versa', 'mira', 'Prävention sexueller Ausbeutung', '', '', '5', '2', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('38', 'archiv', NULL, 'Chronik & Archiv', '', '', '6', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('39', 'js-coaches', NULL, 'J+S Coach', '', '', '7', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('40', 'js-leitende', NULL, 'J+S Leitende', '', '', '7', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('41', 'js-kids', NULL, 'J+S Kids', '', '', '7', '2', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('42', 'scool', NULL, 'sCOOL', '', '', '7', '3', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('43', 'trainer-leistungssport', NULL, 'Trainer Leistungssport', '', '', '8', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('44', 'team-gold', NULL, 'Team Gold', '', '', '8', '1', NULL, '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('45', 'team-gold-leiter', NULL, 'Leiterteam', '', '', '44', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('46', 'kartentrainings', NULL, 'Kartentraining', '', '', '9', '0', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('47', 'hallentrainings', NULL, 'Hallentraining', '', '', '9', '1', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('48', 'lauftrainings', NULL, 'Lauftraining', '', '', '9', '2', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('49', 'nachwuchs-kontakt', NULL, 'Kontaktperson Nachwuchs', '', '', '7', '4', NULL, '0', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('50', 'gold-athleten', NULL, 'Athleten', '', '', '44', '1', NULL, '0', '', 'kaderblog', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-13 20:52:06', '2024-03-13 20:52:06'),
    ('51', 'fan-olz-elite', NULL, 'Fan OLZ Elite', '', '', '8', '3', '4', '1', '', '', NULL, NULL, NULL, NULL, NULL, '1', '2024-03-20 20:52:06', '2024-03-20 20:52:06');

-- Table snippets
INSERT INTO snippets
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`, `created_at`, `last_modified_at`, `text`)
VALUES
    ('1', NULL, NULL, NULL, NULL, '1', '2024-03-25 00:05:14', '2024-03-25 00:05:14', '**OL-Training (im Sommerhalbjahr)**\n\n*für Kartentechnik und Orientierung im Wald (ab 6 Jahren)*\n\njeden Dienstag gemäss Terminkalender\n\n[Trainingsplan 2020](/pdf/Trainingsplan_2020.pdf)\n\n**Hallentraining (im Winterhalbjahr)**\n\n*für Kondition, Kraft, Schnelligkeit mit viel Spiel &amp; Spass (ab 6 Jahren)*\n\nSchulhaus Schweikrüti Gattikon (Montag 18.10 - 19.45 Uhr)\n\nSchulhaus Steinacher Au (Dienstag, 18.00-19.15-20.30 Uhr)\n\nTurnhalle Platte Thalwil (Freitag, 20.15-22.00 Uhr, Spiel)\n\n**Longjoggs (im Winterhalbjahr)**\n\n*für Ausdauer und Kondition (Jugendliche &amp; Erwachsene)*\n\nan Sonntagen gemäss Terminkalender'),
    ('12', '1', NULL, '1', '1', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '{.equal}\nKartentyp | Format | Karte gedruckt | Karte digital\n--- | --- | --- | ---\nWald-/Dorf-Karte | A4 | 2.50 | 2.50\nWald-/Dorf-Karte | A3 | 4.00 | 2.50\nSchulhauskarte | A4 | 1.50 | 1.00\n\n(Kartenpreise gültig ab 1.1.2019)\n'),
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

-- Table termin_labels
INSERT INTO termin_labels
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `details`, `icon`, `on_off`, `created_at`, `last_modified_at`, `ident`, `position`)
VALUES
    ('1', NULL, NULL, NULL, NULL, 'Jahresprogramm', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'programm', '0'),
    ('2', NULL, NULL, NULL, NULL, 'Weekends', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'weekend', '1'),
    ('3', NULL, NULL, NULL, NULL, 'Trainings', '![](./QQ8ZApZjsNSBM2wKrkRQxXZG.jpg) Komm an eines unserer Trainings! [Trainingskonzept als PDF](./6f6novQPv2fjHGzzguXE6nzi.pdf)', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'training', '2'),
    ('4', NULL, NULL, NULL, NULL, 'OLZ-Trophy', 'Nimm teil an der OLZ Trophy, einer Reihe von OLs für alle Leistungsstufen!', 'EM8hA6vye74doeon2RWzZyRf.svg', '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'trophy', '3'),
    ('5', NULL, NULL, NULL, NULL, 'Wettkämpfe', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'ol', '4'),
    ('6', NULL, NULL, NULL, NULL, 'Vereinsanlässe', '', NULL, '1', '2020-03-13 19:30:00', '2020-03-13 19:30:00', 'club', '5');

-- Table termin_locations
INSERT INTO termin_locations
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `name`, `details`, `latitude`, `longitude`, `image_ids`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, 'Chilbiplatz (Thalwil)', '**Infrastruktur:** keine, Garderoben im Freien, keine WCs vorhanden\n\n**öV:** Bus bis \"Thalwil, Chilbiplatz\", oder Zug bis \"Thalwil\", dann 10 min Fussmarsch\n\n**Parkplätze:** auf dem Chilbiplatz', '47.288245737451', '8.5627673724772', '[\"2ZiW6T9biPNjEERzj5xjLRDz.jpg\"]', '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06'),
    ('2', NULL, NULL, NULL, NULL, 'Stumpenhölzlimooshütte (Landforst)', NULL, '47.267405813501', '8.5698615816165', NULL, '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06'),
    ('3', NULL, NULL, NULL, NULL, 'Sportanlage Brand', NULL, '47.288737009648', '8.5510643251822', NULL, '1', '2023-06-11 19:39:06', '2023-06-11 19:39:06');

-- Table termin_notification_templates

-- Table termin_notifications

-- Table termin_template_label_map

-- Table termin_templates
INSERT INTO termin_templates
    (`id`, `location_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `start_time`, `duration_seconds`, `deadline_earlier_seconds`, `deadline_time`, `min_participants`, `max_participants`, `min_volunteers`, `max_volunteers`, `newsletter`, `title`, `text`, `link`, `types`, `image_ids`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', '3', '2', '47', '2', '2', '18:15:00', '5400', NULL, NULL, NULL, NULL, NULL, NULL, '0', 'Hallentraining Brand', 'für alle ab 14 Jahren', NULL, ' training ', '[\"bv3KeYVKDJNg3MTyjhSQsDRx.jpg\"]', '1', '2023-10-02 17:03:21', '2023-10-02 17:03:21'),
    ('2', NULL, '2', '46', '2', '2', '18:30:00', '5400', '172800', '23:59:59', NULL, NULL, NULL, NULL, '0', 'Kartentraining: <<< TODO >>>', 'Karte: <<< TODO >>>\r\nOrganisator: <<< TODO >>>', '<DATEI=qjhUey6Lc6svXsmUcSaguWkJ.pdf text=\"Datei\"><a href=\"https://solv.ch\" class=\"linkint\">Link</a>', ' training ', NULL, '1', '2023-10-02 17:06:51', '2023-10-02 17:06:51'),
    ('3', NULL, '2', '10', '2', '2', '09:00:00', '108000', '604800', '23:59:59', NULL, NULL, NULL, NULL, '1', '<<< TODO >>> Weekend', NULL, NULL, ' programm weekend club ', NULL, '1', '2023-10-02 18:20:53', '2023-10-02 18:20:53'),
    ('4', NULL, '2', '10', '2', '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Minimal', NULL, NULL, NULL, NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53'),
    ('5', NULL, '2', '10', '2', '2', '22:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Open end', NULL, NULL, NULL, NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53'),
    ('6', NULL, '2', '10', '2', '2', NULL, '3600', NULL, NULL, NULL, NULL, NULL, NULL, '0', '<<< TODO >>> Unknown start', NULL, NULL, NULL, NULL, '0', '2023-10-02 18:20:53', '2023-10-02 18:20:53');

-- Table termine
INSERT INTO termine
    (`id`, `start_date`, `start_time`, `end_date`, `end_time`, `title`, `go2ol`, `text`, `link`, `typ`, `on_off`, `xkoord`, `ykoord`, `solv_uid`, `newsletter`, `deadline`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`, `participants_registration_id`, `volunteers_registration_id`, `num_participants`, `min_participants`, `max_participants`, `num_volunteers`, `min_volunteers`, `max_volunteers`, `location_id`, `image_ids`)
VALUES
    ('1', '2020-01-02', '00:00:00', NULL, '00:00:00', 'Berchtoldstag 🥈', '', '', '', '', '1', '0', '0', '0', '1', NULL, NULL, NULL, NULL, NULL, '2019-02-22 01:17:09', '2020-01-01 17:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('2', '2020-06-06', '10:15:00', NULL, '12:30:00', 'Brunch OL', '', 'Dä Samschtig gits en bsunderä Läckerbissä!', '<DATEI=MIGRATED0000000000020001.pdf text=\"Infos\">', 'club', '1', '685000', '236100', '0', '1', NULL, NULL, NULL, NULL, NULL, '2019-12-31 07:17:09', '2019-12-31 20:17:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('3', '2020-08-18', '18:30:00', NULL, '20:00:00', 'Training 1', '', '', '', 'training', '1', NULL, NULL, '0', '0', '2020-08-17 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL),
    ('4', '2020-08-25', '18:30:00', NULL, '20:00:00', 'Training 2', '', '', '', 'training', '1', '683498', '236660', '0', '0', '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL),
    ('5', '2020-08-26', '18:00:00', '2020-08-26', '19:30:00', 'Milchsuppen-Cup, OLZ Trophy 4. Lauf', '', 'Organisation: OL Zimmerberg\r\nKarte: Chopfholz', '<a href=\"/trophy\" class=\"linkint\">OLZ Trophy</a>\r\n<a href=\"https://forms.gle/ixS1ZD22PmbdeYcy6\" class=\"linkext\">Anmeldung</a>\r\n<DATEI=MIGRATED0000000000050001.pdf text=\"Ausschreibung\">', ' ol trophy ', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2019-11-20 09:04:26', '2020-08-24 22:40:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Ffpi3PK5wBjKfN4etpvGK3ti.jpg\"]'),
    ('6', '2020-09-01', '18:30:00', NULL, '20:00:00', 'Training 3', '', '', '', 'training', '1', '684376', '236945', '0', '0', '2020-08-31 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('7', '2020-09-08', '18:00:00', NULL, '19:30:00', 'Training 4', '', '', '<DATEI=Kzt5p5g6cjM5k9CXdVaSsGFx.pdf text=\"Details\">', 'training', '1', '0', '0', '0', '0', '2020-09-06 23:59:59', '2', NULL, '2', '2', '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('8', '2020-08-11', '18:30:00', NULL, '20:00:00', 'Training 0', '', '', '', 'training', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('9', '2020-08-04', '18:30:00', NULL, '20:00:00', 'Training -1', '', '', '', 'training', '1', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('10', '2020-08-22', '00:00:00', NULL, '00:00:00', 'Grossanlass', 'gal', 'Mit allem drum und dran!', NULL, 'programm ol', '1', NULL, NULL, '12345', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('11', '2020-09-13', '00:00:00', '2020-09-19', '00:00:00', 'Mehrtägeler', 'sow', 'Mir werdeds schaffe!', NULL, 'programm weekend ol', '1', NULL, NULL, '123456', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('12', '2020-08-16', '17:00:00', '2020-08-17', '17:00:00', '24h-OL', '24h', 'Dauert genau 24h', NULL, 'programm ol', '1', NULL, NULL, '1234567', '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('13', '2021-03-12', '18:30:00', NULL, NULL, 'Mitgliederversammlung', NULL, 'schon jetzt für 2021 geplant!', NULL, 'programm club', '1', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('1001', '2006-01-13', '18:00:00', NULL, '18:00:00', 'Gründungsversammlung OL Zimmerberg', NULL, 'wir gründen uns!', NULL, 'programm club', '1', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Table throttlings

-- Table users
INSERT INTO users
    (`id`, `username`, `old_username`, `password`, `email`, `first_name`, `last_name`, `permissions`, `root`, `email_is_verified`, `email_verification_token`, `gender`, `street`, `postal_code`, `city`, `region`, `country_code`, `birthdate`, `phone`, `created_at`, `last_modified_at`, `last_login_at`, `parent_user`, `member_type`, `member_last_paid`, `wants_postal_mail`, `postal_title`, `postal_name`, `joined_on`, `joined_reason`, `left_on`, `left_reason`, `solv_number`, `si_card_number`, `notes`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `on_off`)
VALUES
    ('1', 'admin', NULL, '$2y$10$RNMfUZk8cdW.VnuC9XZ0tuZhnhnygy9wdhVfs0kkeFN5M0XC1Abce', 'admin@staging.olzimmerberg.ch', 'Armin 😂', 'Admin 🤣', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('2', 'vorstand', NULL, '$2y$10$xD9LwSFXo5o0l02p3Jzcde.CsfqFxzLWh2jkuGF19yE0Saqq3J3Kq', '', 'Volker', 'Vorstand', 'ftp webdav olz_text_1 aktuell galerie weekly_picture', 'OLZ Dokumente/vorstand', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('3', 'karten', 'kartenverkauf', '$2y$10$0R5z1L2rbQ8rx5p5hURaje70L0CaSJxVPcnmEhz.iitKhumblmKAW', 'karten@staging.olzimmerberg.ch', 'Karen', 'Karten', 'ftp webdav', 'OLZ Dokumente/karten', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('4', 'hackerman', NULL, '$2y$10$5PZTo/AGC89BX.m637GmGekZaktFet7nno0P8deGt.ASOCHxNVwVe', 'hackerman@staging.olzimmerberg.ch', 'Hacker', 'Man', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('5', 'benutzer', NULL, '$2y$10$DluJUi60YHZh6LksqClkmeTX.Giyt3kLHZG3HddV6Zm1UoYXzyXqC', 'nutzer@staging.olzimmerberg.ch', 'Be', 'Nutzer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('6', 'parent', NULL, '$2y$10$iU9SqVRurO.4N1ak1j.p/OP0qT6rEst7.mLd/hM7EzyfI5rBX7nva', 'parent@staging.olzimmerberg.ch', 'Eltern', 'Teil', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('7', 'child1', NULL, NULL, 'child1@staging.olzimmerberg.ch', 'Kind', 'Eins', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('8', 'child2', NULL, '', 'child2@staging.olzimmerberg.ch', 'Kind', 'Zwei', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('9', 'kaderlaeufer', NULL, '$2y$10$YTelsKQLm.Ps9lnXRbDIAOP3SqkE8m9Z/Uw75X4wtyBUA1xY95Lui', 'kaderlaeufer@staging.olzimmerberg.ch', 'Kader', 'Läufer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1'),
    ('42', 'monitoring', NULL, '', 'website@staging.olzimmerberg.ch', 'Monitoring', 'Bot', ' command_olz:monitor-logs command_olz:monitor-backup command_olz:db-backup command_olz:db-reset command_cache:clear command_olz:send-telegram-configuration ', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '1');

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
    ('3', '25'),
    ('4', '25'),
    ('9', '50');

-- Table weekly_picture
INSERT INTO weekly_picture
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `datum`, `image_id`, `text`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, '2020-01-01', '001.jpg', 'Neujahrs-Impression vom Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17'),
    ('2', NULL, NULL, NULL, NULL, '2020-01-02', '001.jpg', 'Berchtoldstag im Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17');

COMMIT;
