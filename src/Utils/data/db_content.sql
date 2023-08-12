-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg
-- MIGRATION: DoctrineMigrations\Version20230701122001

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

-- Table aktuell
INSERT INTO aktuell
    (`id`, `termin`, `datum`, `titel`, `text`, `textlang`, `link`, `autor`, `typ`, `on_off`, `bild1`, `bild1_breite`, `bild1_text`, `bild2`, `bild2_breite`, `bild3`, `bild3_breite`, `zeit`, `counter`, `author_user_id`, `author_role_id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `tags`, `created_at`, `last_modified_at`, `image_ids`, `newsletter`, `autor_email`)
VALUES
    ('3', '0', '2020-01-01', 'Frohes neues Jahr! 🎆', '<BILD1>Im Namen des Vorstands wünsche ich euch allen ein **frohes neues Jahr**! 🎆 <DATEI=MIGRATED0000000000030001.pdf text=\"Neujahrsansprache als PDF\">', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. *Ein Bisschen.* Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n<BILD2>\n\nUnd hier nochmals das Emoji: 🎆.\n\nUnd hier nochmals die <DATEI=MIGRATED0000000000030001.pdf text=\"Neujahrsansprache als PDF\">', '', 'prä', 'aktuell', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2021-06-28 16:37:03', '2021-06-28 16:37:03', '[\"MIGRATED0000000000030001.jpg\",\"MIGRATED0000000000030002.jpg\"]', '1', NULL),
    ('4', '0', '2020-03-16', 'Neues System für News-Einträge online!', '<BILD1>Heute ging ein neues System für **News-Einträge online**. Nach und nach sollen Aktuell- Galerie- Kaderblog- und Forumseinträge auf das neue System migriert werden. Siehe <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"Motivationsschreiben\">.', 'All diese Einträge sind ähnlich: Sie werden von einem Autor erstellt, enthalten Titel und Text, evtl. Teaser, Bilder und angehängte Dateien, und sind für alle *OL-Zimmerberg-Mitglieder* von Interesse. Deshalb **vereinheitlichen** wir nun diese verschiedenen Einträge.\n\nDie Gründe für die Änderung haben wir in <DATEI=xMpu3ExjfBKa8Cp35bcmsDgq.pdf text=\"diesem Schreiben\"> zusammengefasst.\n\n<BILD1>', NULL, '', 'aktuell', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '1', '25', '1', NULL, NULL, NULL, '  ', '2020-03-16 14:51:00', '2020-03-16 14:51:00', '[\"xkbGJQgO5LFXpTSz2dCnvJzu.jpg\"]', '1', NULL),
    ('5', '0', '2020-08-15', 'Neues System für News-Einträge bewährt sich', 'Das neue System für News-Einträge scheint *gut anzukommen*. Neu können eingeloggte Benutzer in ihren News-Einträgen (ehem. Forumseinträgen) auch Bilder und Dateien einbinden.', '', NULL, '', 'aktuell', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '1', NULL, '1', NULL, NULL, NULL, '  ', '2020-08-15 14:51:00', '2020-08-15 14:51:00', '[]', '1', NULL),
    ('6', '0', '2020-01-02', 'Berchtoldstagsgalerie 2020', '', '', NULL, '', 'galerie', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '3', NULL, '3', NULL, NULL, NULL, '  ', '2020-08-15 14:51:00', '2020-08-15 14:51:00', '[\"eGbiJQgOyLF5p6S92kC3vTzE.jpg\",\"Frw83uTOyLF5p6S92kC7zpEW.jpg\"]', '1', NULL),
    ('7', '0', '2020-08-15', 'Test Video', '', 'https://youtu.be/JVL0vgcnM6c', NULL, '', 'video', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '2', '26', '2', NULL, NULL, NULL, '  ', '2020-08-15 14:51:00', '2020-08-15 14:51:00', '[\"aRJIflbxtkF5p6S92k470912.jpg\"]', '1', NULL),
    ('8', '0', '2020-01-15', 'Hinweis vom Präsi', '', 'Auch der **Präsident** schreibt im Forum!', NULL, NULL, 'forum', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '16:51:00', '0', '1', '5', '1', NULL, '1', '1', '  ', '2020-01-15 16:51:00', '2020-01-15 16:51:00', '[\"9GjbtlsSu96AWZ-oH0rHjxup.jpg\",\"zUXE3aKfbK3edmqS35FhaF8g.jpg\"]', '1', NULL),
    ('9', '0', '2020-01-06', 'Longjogg am Sonntag', '', 'Ich will mich nicht einloggen, aber hier mein Beitrag:\n\nIch organisiere einen **Longjogg am Sonntag**.\n\nPackt *warme* Kleidung ein.\n\nWer zum Pastaessen bleiben will, muss sich bis am Samstagmittag bei mir melden.\n\nAusserdem habe ich weitere unfassbar komplizierte Anforderungen an meine Gäste und geht dermassen tief ins Detail, dass dieser Forumseintrag unter keinen Umständen in seiner ganzen Länge in der Liste der Forumseinträge angezeigt werden sollte!\n\nAnreise:\nRichterswil ab 09:30\nWädenswil ab 09:31\nHorgen ab 09:32\nThalwil ab 09:33\nZürich HB ab 09:34\nBei mir an 09:35', NULL, 'Anonymous', 'forum', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '16:51:00', '0', NULL, NULL, NULL, NULL, NULL, NULL, '  ', '2020-01-06 16:51:00', '2020-01-06 16:51:00', '[]', '1', 'anonymous@gmail.com'),
    ('10', '0', '2020-08-15', 'Dank dem neuen News-System trainiere ich 50% besser!', '', '<BILD1>Ich bin total Fan vom neuen News-System! Meine Trainingsleistung hat deswegen um 50% zugenommen (keine Ahnung wieso). Hier die Beweise:\n\n- <DATEI=gAQa_kYXqXTP1_DKKU1s1pGr.csv text=\"Beweisstück A\">\n- <DATEI=8kCalo9sQtu2mrgrmMjoGLUW.pdf text=\"Beweisstück B\">\n\n<BILD2>', NULL, NULL, 'kaderblog', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '16:51:00', '0', '9', '50', '9', NULL, '9', '9', '  ', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"DvDB8QkHcGuxQ4lAFwyvHnVd.jpg\",\"OOVJIqrWlitR_iTZuIIhztKC.jpg\"]', '1', NULL),
    ('1202', '0', '2020-01-01', 'Neujahrsgalerie 📷 2020', '', '', NULL, '', 'galerie', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, '  ', '2020-08-15 14:51:00', '2020-08-15 14:51:00', '[\"MIGRATED0000000012020001.jpg\",\"MIGRATED0000000012020002.jpg\",\"MIGRATED0000000012020003.jpg\",\"MIGRATED0000000012020004.jpg\",\"MIGRATED0000000012020005.jpg\"]', '1', NULL),
    ('1203', '0', '2020-08-13', 'Test Video', '', 'https://youtu.be/JVL0vgcnM6c', NULL, '', 'video', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, '  ', '2020-08-13 14:51:00', '2020-08-13 14:51:00', '[\"MIGRATED0000000012030001.jpg\"]', '1', NULL),
    ('2901', '0', '2020-01-01', 'Guets Nois! 🎉', '', 'Hoi zäme, au vo mier no *Guets Nois*! 🎉', NULL, 'Bruno 😃 Beispielmitglied', 'forum', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '21:45:37', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2020-01-01 21:45:37', '2020-01-01 21:45:37', '[]', '0', 'beispiel@olzimmerberg.ch'),
    ('2902', '0', '2020-01-03', 'Verspätete Neujahrsgrüsse', '', 'Has vergesse, aber au vo *mier* no Guets Nois!', NULL, 'Erwin Exempel', 'forum', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '18:42:01', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2020-01-03 18:42:01', '2020-01-03 18:42:01', '[]', '0', 'beispiel@olzimmerberg.ch'),
    ('2903', '0', '2020-01-06', 'Hallo', '', 'Mir hend paar **OL-Usrüschtigs-Gegeständ** us ferne Länder mitbracht.\n\nSchriibed doch es Mail wenn er öppis devoo wetted', NULL, 'Drei Könige', 'forum', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '06:07:08', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2020-01-06 06:07:08', '2020-01-06 06:07:08', '[]', '0', 'beispiel@olzimmerberg.ch'),
    ('6401', '0', '2019-01-01', 'Saisonstart 2019!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Hoi zäme, dieser Eintrag wurde noch mit dem alten System geschrieben. Hier die Anhänge:\n<BILD1>\n<BILD2>\n<DL1>\n<DL2>', NULL, 'Gold Junge', 'kaderblog', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '15:15:15', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-01-01 15:15:15', '2019-01-01 15:15:15', '[\"MIGRATED0000000064010001.jpg\",\"MIGRATED0000000064010002.jpg\"]', '0', NULL),
    ('6402', '0', '2019-08-15', 'Neuer Eintrag auf meinem externen Blog', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', 'Kleiner Teaser', 'https://www.external-blog.com/entry/1234', 'Eliteläuferin', 'kaderblog', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '15:15:15', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2019-08-15 15:15:15', '2019-08-15 15:15:15', '[]', '0', NULL),
    ('6403', '0', '2020-01-01', 'Saisonstart 2020!', '{\"file1\":null,\"file1_name\":null,\"file2\":null,\"file2_name\":null}', '<BILD1> Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: <DATEI=MIGRATED0000000064030001.pdf text=\"Extrem Harte Trainingsstrategie\">', NULL, 'Gold Junge', 'kaderblog', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '15:15:15', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '2020-01-01 15:15:15', '2020-01-01 15:15:15', '[\"MIGRATED0000000064030001.jpg\"]', '0', NULL);

-- Table anmelden_bookings

-- Table anmelden_registration_infos

-- Table anmelden_registrations

-- Table auth_requests
-- (auth_requests omitted)

-- Table blog
INSERT INTO blog
    (`id`, `counter`, `datum`, `autor`, `titel`, `text`, `bild1`, `bild2`, `on_off`, `zeit`, `dummy`, `file1`, `file1_name`, `file2`, `file2_name`, `bild1_breite`, `bild2_breite`, `linkext`, `newsletter`)
VALUES
    ('1', '0', '2019-01-01', 'Gold Junge', 'Saisonstart 2019!', 'Hoi zäme, dieser Eintrag wurde noch mit dem alten System geschrieben. Hier die Anhänge:\n<BILD1>\n<BILD2>\n<DL1>\n<DL2>', NULL, NULL, '1', '15:15:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1'),
    ('2', '0', '2019-08-15', 'Eliteläuferin', 'Neuer Eintrag auf meinem externen Blog', 'Kleiner Teaser', NULL, NULL, '1', '15:15:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://www.external-blog.com/entry/1234', '1'),
    ('3', '0', '2020-01-01', 'Gold Junge', 'Saisonstart 2020!', '<BILD1> Ich habe das erste mega harte Training im 2020 absolviert! Schaut hier: <DATEI1 text=\"Extrem Harte Trainingsstrategie\">', NULL, NULL, '1', '15:15:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1');

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
    ('DoctrineMigrations\\Version20230701122001', '2023-07-01 12:47:09', '10');

-- Table downloads

-- Table facebook_links

-- Table forum
INSERT INTO forum
    (`id`, `name`, `email`, `eintrag`, `uid`, `datum`, `zeit`, `on_off`, `allow_html`, `name2`, `newsletter`)
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

-- Table karten
INSERT INTO karten
    (`id`, `position`, `kartennr`, `name`, `center_x`, `center_y`, `jahr`, `massstab`, `ort`, `zoom`, `typ`, `vorschau`)
VALUES
    ('1', '0', '1086', 'Landforst 🗺️', '685000', '236100', '2017', '1:10\'000', NULL, '8', 'ol', 'landforst_2017_10000.jpg'),
    ('2', '2', '0', 'Eidmatt', '693379', '231463', '2020', '1:1\'000', 'Wädenswil', '2', 'scool', ''),
    ('3', '1', '0', 'Horgen Dorfkern', '687900', '234700', '2011', '1:2\'000', 'Horgen', '8', 'stadt', 'horgen_dorfkern_2011_2000.jpg');

-- Table links

-- Table messenger_messages

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

-- Table olz_text
INSERT INTO olz_text
    (`id`, `text`, `on_off`)
VALUES
    ('1', '<div><p><b>OL-Training (im Sommerhalbjahr)</b><br>\n<i>für Kartentechnik und Orientierung im Wald (ab 6 Jahren)</i><br>\njeden Dienstag gemäss Terminkalender<br>\n<a href=\"/pdf/Trainingsplan_2020.pdf\" target=\"_blank\">Trainingsplan 2020</a></p>\n<p><b>Hallentraining (im Winterhalbjahr)</b><br>\n<i>für Kondition, Kraft, Schnelligkeit mit viel Spiel &amp; Spass (ab 6 Jahren)</i><br>\nSchulhaus Schweikrüti Gattikon (Montag 18.10 - 19.45 Uhr)<br>\nSchulhaus Steinacher Au (Dienstag, 18.00-19.15-20.30 Uhr)<br>\nTurnhalle Platte Thalwil (Freitag, 20.15-22.00 Uhr, Spiel)</p>\n<!--<p><b>Lauftraining</b><br>\n<i>für Ausdauer und Kondition (Jugendliche & Erwachsene)</i><br>\njeden Donnerstag, 18.45 Uhr, 60 Min. (In den Schulferien nur nach Absprache.)</p>-->\n<p><b>Longjoggs (im Winterhalbjahr)</b><br>\n<i>für Ausdauer und Kondition (Jugendliche &amp; Erwachsene)</i><br>\nan Sonntagen gemäss Terminkalender</p></div>', '1'),
    ('22', '⚠️ Wichtige Information! ⚠️', '1'),
    ('23', '⚠️ Abgesagt! ⚠️', '1');

-- Table panini24
INSERT INTO panini24
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `line1`, `line2`, `association`, `img_src`, `img_style`, `is_landscape`, `has_top`, `on_off`, `created_at`, `last_modified_at`, `infos`)
VALUES
    ('1001', '1', NULL, '1', '1', 'Armin 😂', 'Admin 🤣', 'Thalwil', 'vptD8fzvXIhv_6X32Zkw2s5s.jpg', 'width:200%; top:0%; left:-50%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"SchlADMINg\",\"Die Registrierung der Domain olzimmerberg.ch\",\"\\u00dcber die Website\",\"2006\",\"Admins! Admins! Admins!\"]'),
    ('1002', '1', NULL, '1', '1', 'Volker', 'Vorstand', 'WTF', 'LkGdXukqgYEdnWpuFHfrJkr7.jpg', 'width:150%; top:0%; left:-33%;', '0', '0', '1', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '[\"Vorab\",\"Wahl in den Vorstand\",\"\\u00dcber die Website\",\"2006\",\"Vorstand! Vorstand! Vorstand!\"]');

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
    (`id`, `username`, `old_username`, `name`, `description`, `page`, `parent_role`, `index_within_parent`, `featured_index`, `can_have_child_roles`, `guide`, `permissions`)
VALUES
    ('1', 'anlaesse', NULL, 'Anlässe🎫, \r\nVizepräsi', 'Organisiert Anlässe', '', NULL, '0', NULL, '1', 'Anlässe organisieren:\n- 1 Jahr vorher: abklären\n- ...', ''),
    ('2', 'material', NULL, 'Material \r\n& Karten', '', '', NULL, '1', NULL, '1', '', ''),
    ('3', 'media', NULL, 'Öffentlich-\r\nkeitsarbeit', '', '', NULL, '2', NULL, '1', '', ''),
    ('4', 'finanzen', NULL, 'Finanzen', '', '', NULL, '3', NULL, '1', '', ''),
    ('5', 'praesi', NULL, 'Präsident', '', '', NULL, '4', NULL, '1', '', ''),
    ('6', 'aktuariat', NULL, 'Aktuariat & \r\nMitgliederliste', '', '', NULL, '5', NULL, '1', '', ''),
    ('7', 'nachwuchs-ausbildung', NULL, 'Nachwuchs & \r\nAusbildung', '', '', NULL, '6', NULL, '1', '', ''),
    ('8', 'nachwuchs-leistungssport', NULL, 'Nachwuchs & Leistungssport', '', '', NULL, '7', NULL, '1', '', ''),
    ('9', 'trainings', NULL, 'Training\r\n& Technik', '', '', NULL, '8', NULL, '1', '', ''),
    ('10', 'weekends', NULL, 'Weekends', '', '', '1', '0', NULL, '1', '', ''),
    ('11', 'staffeln', NULL, '5er- und Pfingststaffel', '', '', '1', '1', NULL, '1', '', ''),
    ('12', 'papiersammlung', NULL, 'Papiersammlung', '', '', '1', '2', NULL, '1', '', ''),
    ('13', 'papiersammlung-langnau', NULL, 'Langnau', '', '', '12', '0', NULL, '0', '', ''),
    ('14', 'papiersammlung-thalwil', NULL, 'Thalwil', '', '', '12', '1', NULL, '0', '', ''),
    ('15', 'flohmarkt', NULL, 'Flohmarkt', '', '', '1', '3', NULL, '0', '', ''),
    ('16', 'kartenchef', NULL, 'Kartenteam', '', '', '2', '0', NULL, '1', '', ''),
    ('17', 'kartenteam', NULL, 'Mit dabei', '', '', '16', '0', NULL, '0', '', ''),
    ('18', 'karten', 'kartenverkauf', 'Kartenverkauf', '', '', '2', '1', NULL, '0', '', ''),
    ('19', 'kleider', 'kleiderverkauf', 'Kleiderverkauf', '', '', '2', '2', NULL, '0', '', ''),
    ('20', 'material-group', NULL, 'Material', '', '', '2', '3', NULL, '1', '', ''),
    ('21', 'materiallager', NULL, 'Lager Thalwil', '', '', '20', '0', NULL, '0', '', ''),
    ('22', 'sportident', NULL, 'SportIdent', '', '', '20', '1', NULL, '0', '', ''),
    ('23', 'buessli', NULL, 'OLZ-Büssli', '', '', '2', '4', NULL, '1', '', ''),
    ('24', 'presse', NULL, 'Presse', '', '', '3', '0', NULL, '0', '', ''),
    ('25', 'website', NULL, 'Homepage', '', '', '3', '1', NULL, '0', '', ''),
    ('26', 'holz', NULL, 'Heftli \"HOLZ\"', '', '', '3', '2', NULL, '0', '', ''),
    ('27', 'revisoren', NULL, 'Revisoren', '', '', '4', '0', NULL, '0', '', ''),
    ('28', 'ersatzrevisoren', NULL, 'Ersatzrevisor', '', '', '27', '0', NULL, '0', '', ''),
    ('29', 'sektionen', NULL, 'Sektionen', '', '', '5', '0', NULL, '1', '', ''),
    ('30', 'sektion-adliswil', NULL, 'Adliswil', '', '', '29', '0', NULL, '0', '', ''),
    ('31', 'sektion-horgen', NULL, 'Horgen', '', '', '29', '1', NULL, '0', '', ''),
    ('32', 'sektion-langnau', NULL, 'Langnau', '', '', '29', '2', NULL, '0', '', ''),
    ('33', 'sektion-richterswil', NULL, 'Richterswil', '', '', '29', '3', NULL, '0', '', ''),
    ('34', 'sektion-thalwil', NULL, 'Thalwil', '', '', '29', '4', NULL, '0', '', ''),
    ('35', 'sektion-waedenswil', NULL, 'Wädenswil', '', '', '29', '5', NULL, '0', '', ''),
    ('36', 'ol-und-umwelt', NULL, 'OL und Umwelt', '', '', '5', '1', NULL, '0', '', ''),
    ('37', 'versa', 'mira', 'Prävention sexueller Ausbeutung', '', '', '5', '2', NULL, '0', '', ''),
    ('38', 'archiv', NULL, 'Chronik & Archiv', '', '', '6', '0', NULL, '0', '', ''),
    ('39', 'js-coaches', NULL, 'J+S Coach', '', '', '7', '0', NULL, '0', '', ''),
    ('40', 'js-leitende', NULL, 'J+S Leitende', '', '', '7', '1', NULL, '0', '', ''),
    ('41', 'js-kids', NULL, 'J+S Kids', '', '', '7', '2', NULL, '0', '', ''),
    ('42', 'scool', NULL, 'sCOOL', '', '', '7', '3', NULL, '0', '', ''),
    ('43', 'trainer-leistungssport', NULL, 'Trainer Leistungssport', '', '', '8', '0', NULL, '0', '', ''),
    ('44', 'team-gold', NULL, 'Team Gold', '', '', '8', '1', NULL, '1', '', ''),
    ('45', 'team-gold-leiter', NULL, 'Leiterteam', '', '', '44', '0', NULL, '0', '', ''),
    ('46', 'kartentrainings', NULL, 'Kartentraining', '', '', '9', '0', NULL, '0', '', ''),
    ('47', 'hallentrainings', NULL, 'Hallentraining', '', '', '9', '1', NULL, '0', '', ''),
    ('48', 'lauftrainings', NULL, 'Lauftraining', '', '', '9', '2', NULL, '0', '', ''),
    ('49', 'nachwuchs-kontakt', NULL, 'Kontaktperson Nachwuchs', '', '', '7', '4', NULL, '0', '', ''),
    ('50', 'gold-athleten', NULL, 'Athleten', '', '', '44', '1', NULL, '0', '', 'kaderblog');

-- Table solv_events
INSERT INTO solv_events
    (`solv_uid`, `date`, `duration`, `kind`, `day_night`, `national`, `region`, `type`, `name`, `link`, `club`, `map`, `location`, `coord_x`, `coord_y`, `deadline`, `entryportal`, `start_link`, `rank_link`, `last_modification`)
VALUES
    ('6822', '2014-06-29', '1', 'foot', 'day', '1', 'GL/GR', '**A', '6. Nationaler OL 🥶', 'http://www.olg-chur.ch', 'OLG Chur 🦶', 'Crap Sogn Gion/Curnius ⛰️', '', '735550', '188600', '2014-06-10', '1', '', '', '2014-03-05 00:38:15'),
    ('7411', '2015-06-21', '1', 'foot', 'day', '0', 'ZH/SH', '402S', '59. Schweizer 5er Staffel', 'http://www.5erstaffel.ch', 'OLC Kapreolo', 'Chomberg', '', '693700', '259450', '2015-06-01', '1', '', '', '2015-05-15 02:43:20'),
    ('12345', '2020-08-22', '1', 'foot', 'day', '1', 'ZH/SH', '402S', 'Grossanlass', 'http://www.grossanlass.ch', 'OLG Bern', 'Grosswald', '', '600000', '200000', '2020-08-17', '1', '', '', '2015-05-15 02:43:20');

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

-- Table termin_locations

-- Table termine
INSERT INTO termine
    (`id`, `datum`, `datum_end`, `datum_off`, `zeit`, `zeit_end`, `titel`, `go2ol`, `text`, `link`, `solv_event_link`, `typ`, `on_off`, `xkoord`, `ykoord`, `solv_uid`, `ical_uid`, `newsletter`, `deadline`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `created_at`, `last_modified_at`, `participants_registration_id`, `volunteers_registration_id`, `num_participants`, `min_participants`, `max_participants`, `num_volunteers`, `min_volunteers`, `max_volunteers`, `location_id`, `image_ids`)
VALUES
    ('1', '2020-01-02', NULL, NULL, '00:00:00', '00:00:00', 'Berchtoldstag 🥈', '', '', '', '', '', '1', '0', '0', '0', '', '1', NULL, NULL, NULL, NULL, NULL, '2019-02-22 01:17:09', '2020-01-01 17:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('2', '2020-06-06', NULL, NULL, '10:15:00', '12:30:00', 'Brunch OL', '', 'Dä Samschtig gits en bsunderä Läckerbissä!', '<DATEI=MIGRATED0000000000020001.pdf text=\"Infos\">', 'http://127.0.0.1:30270/', 'club', '1', '685000', '236100', '0', '', '1', NULL, NULL, NULL, NULL, NULL, '2019-12-31 07:17:09', '2019-12-31 20:17:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('3', '2020-08-18', NULL, NULL, '18:30:00', '20:00:00', 'Training 1', '', '', '', '', 'training', '1', '684376', '236945', '0', '', '0', '2020-08-17 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('4', '2020-08-25', NULL, NULL, '18:30:00', '20:00:00', 'Training 2', '', '', '', '', 'training', '1', '683498', '236660', '0', '', '0', '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('5', '2020-08-26', '2020-08-26', NULL, '18:00:00', '19:30:00', 'Milchsuppen-Cup, OLZ Trophy 4. Lauf', '', 'Organisation: OL Zimmerberg\r\nKarte: Chopfholz', '<a href=\"/trophy.php\" class=\"linkint\">OLZ Trophy</a>\r\n<a href=\"https://forms.gle/ixS1ZD22PmbdeYcy6\" class=\"linkext\">Anmeldung</a>\r\n<DATEI=MIGRATED0000000000050001.pdf text=\"Ausschreibung\">', NULL, 'ol', '1', '0', '0', '0', NULL, '0', NULL, NULL, NULL, NULL, NULL, '2019-11-20 09:04:26', '2020-08-24 22:40:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Ffpi3PK5wBjKfN4etpvGK3ti.jpg\"]'),
    ('6', '2020-09-01', NULL, NULL, '18:30:00', '20:00:00', 'Training 3', '', '', '', '', 'training', '1', '0', '0', '0', '', '0', '2020-08-31 00:00:00', NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('7', '2020-09-08', NULL, NULL, '18:00:00', '19:30:00', 'Training 4', '', '', '<DATEI=Kzt5p5g6cjM5k9CXdVaSsGFx.pdf text=\"Details\">', '', 'training', '1', '0', '0', '0', '', '0', '2020-09-06 23:59:59', '2', NULL, '2', '2', '2020-02-22 01:17:09', '2020-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('8', '2020-08-11', NULL, NULL, '18:30:00', '20:00:00', 'Training 0', '', '', '', '', 'training', '1', '0', '0', '0', '', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('9', '2020-08-04', NULL, NULL, '18:30:00', '20:00:00', 'Training -1', '', '', '', '', 'training', '1', '0', '0', '0', '', '0', NULL, NULL, NULL, NULL, NULL, '2020-02-22 01:17:09', '2220-02-22 01:17:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('10', '2020-08-22', NULL, NULL, '00:00:00', '00:00:00', 'Grossanlass', 'gal', 'Mit allem drum und dran!', NULL, NULL, 'programm ol', '1', NULL, NULL, '12345', NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('11', '2020-09-13', '2020-09-19', NULL, '00:00:00', '00:00:00', 'Mehrtägeler', 'sow', 'Mir werdeds schaffe!', NULL, NULL, 'programm weekend ol', '1', NULL, NULL, '123456', NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('12', '2020-08-16', '2020-08-17', NULL, '17:00:00', '17:00:00', '24h-OL', '24h', 'Dauert genau 24h', NULL, NULL, 'programm ol', '1', NULL, NULL, '1234567', NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    ('1001', '2006-01-13', NULL, NULL, '18:00:00', '18:00:00', 'Gründungsversammlung OL Zimmerberg', NULL, 'wir gründen uns!', NULL, NULL, 'programm club', '1', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '2021-03-23 18:53:06', '2021-03-23 18:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Table throttlings

-- Table users
INSERT INTO users
    (`id`, `username`, `old_username`, `password`, `email`, `first_name`, `last_name`, `permissions`, `root`, `email_is_verified`, `email_verification_token`, `gender`, `street`, `postal_code`, `city`, `region`, `country_code`, `birthdate`, `phone`, `created_at`, `last_modified_at`, `last_login_at`, `parent_user`, `member_type`, `member_last_paid`, `wants_postal_mail`, `postal_title`, `postal_name`, `joined_on`, `joined_reason`, `left_on`, `left_reason`, `solv_number`, `si_card_number`, `notes`)
VALUES
    ('1', 'admin', NULL, '$2y$10$RNMfUZk8cdW.VnuC9XZ0tuZhnhnygy9wdhVfs0kkeFN5M0XC1Abce', 'admin@staging.olzimmerberg.ch', 'Armin 😂', 'Admin 🤣', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('2', 'vorstand', NULL, '$2y$10$xD9LwSFXo5o0l02p3Jzcde.CsfqFxzLWh2jkuGF19yE0Saqq3J3Kq', '', 'Volker', 'Vorstand', 'ftp webdav olz_text_1 aktuell galerie weekly_picture', 'OLZ Dokumente/vorstand', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('3', 'karten', 'kartenverkauf', '$2y$10$0R5z1L2rbQ8rx5p5hURaje70L0CaSJxVPcnmEhz.iitKhumblmKAW', 'karten@staging.olzimmerberg.ch', 'Karen', 'Karten', 'ftp webdav', 'OLZ Dokumente/karten', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('4', 'hackerman', NULL, '$2y$10$5PZTo/AGC89BX.m637GmGekZaktFet7nno0P8deGt.ASOCHxNVwVe', 'hackerman@staging.olzimmerberg.ch', 'Hacker', 'Man', 'all', 'OLZ Dokumente', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2021-12-01 00:41:26', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('5', 'benutzer', NULL, '$2y$10$DluJUi60YHZh6LksqClkmeTX.Giyt3kLHZG3HddV6Zm1UoYXzyXqC', 'nutzer@staging.olzimmerberg.ch', 'Be', 'Nutzer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('6', 'parent', NULL, '$2y$10$iU9SqVRurO.4N1ak1j.p/OP0qT6rEst7.mLd/hM7EzyfI5rBX7nva', 'parent@staging.olzimmerberg.ch', 'Eltern', 'Teil', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('7', 'child1', NULL, NULL, 'child1@staging.olzimmerberg.ch', 'Kind', 'Eins', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('8', 'child2', NULL, '', 'child2@staging.olzimmerberg.ch', 'Kind', 'Zwei', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', '6', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('9', 'kaderlaeufer', NULL, '$2y$10$YTelsKQLm.Ps9lnXRbDIAOP3SqkE8m9Z/Uw75X4wtyBUA1xY95Lui', 'kaderlaeufer@staging.olzimmerberg.ch', 'Kader', 'Läufer', '', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
    ('42', 'monitoring', NULL, '', 'website@staging.olzimmerberg.ch', 'Monitoring', 'Bot', ' command_olz:monitor-logs command_olz:monitor-backup command_olz:db-backup command_olz:db-reset command_cache:clear ', '', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-01 00:41:26', '2020-08-15 16:51:00', '2020-08-15 16:51:00', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

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
    (`id`, `owner_user_id`, `owner_role_id`, `created_by_user_id`, `last_modified_by_user_id`, `datum`, `image_id`, `alternative_image_id`, `text`, `on_off`, `created_at`, `last_modified_at`)
VALUES
    ('1', NULL, NULL, NULL, NULL, '2020-01-01', '001.jpg', NULL, 'Neujahrs-Impression vom Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17'),
    ('2', NULL, NULL, NULL, NULL, '2020-01-02', '001.jpg', NULL, 'Berchtoldstag im Sihlwald 🌳🌲🌴', '1', '2022-10-24 16:52:17', '2022-10-24 16:52:17');

COMMIT;
