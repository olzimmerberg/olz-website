-- Der Test-Inhalt der Datenbank der Webseite der OL Zimmerberg

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Table aktuell
INSERT INTO aktuell
    (`id`, `termin`, `datum`, `newsletter`, `newsletter_datum`, `titel`, `text`, `textlang`, `link`, `autor`, `typ`, `on_off`, `bild1`, `bild1_breite`, `bild1_text`, `bild2`, `bild2_breite`, `bild3`, `bild3_breite`, `zeit`, `counter`)
VALUES
    ('1', '0', '2020-01-01', '1', NULL, 'Frohes neues Jahr!', '<BILD1>Im Namen des Vorstands wünsche ich euch allen ein frohes neues Jahr!', 'Gratulation, du bist gerade dabei, den Neujahrseintrag des Vorstands zu lesen. Der geht auch noch weiter. Ein Bisschen. Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und hier gibts noch ein anderes Bild:\n\n<BILD2>', '', 'prä', '', '1', '', '0', '', '', '0', '', '0', '00:00:00', '0');

-- Table anm_felder

-- Table anmeldung

-- Table bild_der_woche
INSERT INTO bild_der_woche
    (`id`, `datum`, `bild1`, `bild2`, `on_off`, `text`, `titel`, `bild1_breite`, `bild2_breite`)
VALUES
    ('1', '2020-01-01', '', '', '0', 'Neujahrs-Impression vom Sihlwald', 'Titel 1', '0', '0'),
    ('2', '2020-01-02', '', '', '1', 'Berchtoldstag im Sihlwald', 'Titel 2', '0', '0');

-- Table blog

-- Table counter

-- Table downloads

-- Table event

-- Table facebook_settings

-- Table forum
INSERT INTO forum
    (`id`, `name`, `email`, `eintrag`, `newsletter`, `newsletter_datum`, `uid`, `datum`, `zeit`, `on_off`, `allowHTML`, `name2`)
VALUES
    ('1', 'Guets Nois!', 'beispiel@olzimmerberg.ch', 'Hoi zäme, au vo mier no Guets Nois!', '1', NULL, 'hd35lm6glq', '2020-01-01', '21:45:37', '1', '0', 'Bruno Beispielmitglied');

-- Table galerie
INSERT INTO galerie
    (`id`, `termin`, `titel`, `datum`, `datum_end`, `autor`, `on_off`, `typ`, `counter`, `content`)
VALUES
    ('1', '0', 'Neujahrsgalerie 2020', '2020-01-01', NULL, 'sh', '1', 'foto', '0', ''),
    ('2', '0', 'Berchtoldstagsgalerie 2020', '2020-01-02', NULL, 'sh', '1', 'foto', '0', '');

-- Table images

-- Table jwoc

-- Table karten

-- Table links

-- Table newsletter

-- Table olz_result

-- Table olz_text

-- Table rundmail

-- Table solv_events
INSERT INTO solv_events
    (`solv_uid`, `date`, `duration`, `kind`, `day_night`, `national`, `region`, `type`, `name`, `link`, `club`, `map`, `location`, `coord_x`, `coord_y`, `deadline`, `entryportal`, `start_link`, `rank_link`, `last_modification`)
VALUES
    ('6822', '2014-06-29', '1', 'foot', 'day', '1', 'GL/GR', '**A', '6. Nationaler OL', 'http://www.olg-chur.ch', 'OLG Chur', 'Crap Sogn Gion/Curnius', '', '735550', '188600', '2014-06-10', '1', '', '', '2014-03-05 00:38:15'),
    ('7411', '2015-06-21', '1', 'foot', 'day', '0', 'ZH/SH', '402S', '59. Schweizer 5er Staffel', 'http://www.5erstaffel.ch', 'OLC Kapreolo', 'Chomberg', '', '693700', '259450', '2015-06-01', '1', '', '', '2015-05-15 02:43:20');

-- Table solv_people
INSERT INTO solv_people
    (`id`, `same_as`, `name`, `birth_year`, `domicile`, `member`)
VALUES
    ('1', NULL, 'Toni Thalwiler', '00', 'Thalwil', '1'),
    ('2', NULL, 'Hanna Horgener', '70', 'Horgen', '1'),
    ('3', NULL, 'Walter Wädenswiler', '83', 'Wädenswil', '1'),
    ('4', NULL, 'Regula Richterswiler', '96', 'Richterswil', '1');

-- Table solv_results
INSERT INTO solv_results
    (`id`, `person`, `event`, `class`, `rank`, `name`, `birth_year`, `domicile`, `club`, `result`, `splits`, `finish_split`, `class_distance`, `class_elevation`, `class_control_count`, `class_competitor_count`)
VALUES
    ('1', '1', '6822', 'HAL', '79', 'Toni Thalwiler', '00', 'Thalwil', 'OL Zimmerberg', '1234', '', '12', '4500', '200', '20', '80'),
    ('2', '2', '6822', 'DAM', '3', 'Hanna Horgener', '70', 'Horgen', 'OL Zimmerberg', '4321', '', '43', '3200', '120', '15', '45'),
    ('3', '3', '6822', 'HAK', '13', 'Walter Wädenswiler', '83', 'Wädenswil', 'OL Zimmerberg', '4231', '', '32', '2300', '140', '17', '35'),
    ('4', '1', '7411', 'HAL', '79', 'Anton Thalwiler', '00', 'Thalwil', 'OL Zimmerberg', '1234', '', '12', '4500', '200', '20', '80'),
    ('5', '3', '7411', 'HAK', '13', 'Walti Wädischwiiler', '83', 'Wädenswil', 'OL Zimmerberg', '4231', '', '32', '2300', '140', '17', '35'),
    ('6', '4', '7411', 'DAK', '6', 'Regula Richterswiler', '96', 'Richterswil', 'OL Zimmerberg', '4321', '', '43', '3200', '120', '15', '45');

-- Table termine
INSERT INTO termine
    (`id`, `datum`, `datum_end`, `datum_off`, `zeit`, `zeit_end`, `teilnehmer`, `newsletter`, `newsletter_datum`, `newsletter_anmeldung`, `titel`, `go2ol`, `text`, `link`, `solv_event_link`, `typ`, `on_off`, `datum_anmeldung`, `text_anmeldung`, `email_anmeldung`, `xkoord`, `ykoord`, `solv_uid`, `ical_uid`, `modified`, `created`)
VALUES
    ('1', '2020-01-02', NULL, NULL, '00:00:00', '00:00:00', '0', '0', NULL, NULL, 'Berchtoldstag', '', '', '', '', '', '1', NULL, '', '', '0', '0', '0', '', '2020-02-22 01:17:43', '2020-02-22 01:17:09');

-- Table termine_go2ol

-- Table termine_solv

-- Table trainingsphotos

-- Table user
INSERT INTO user
    (`id`, `benutzername`, `passwort`, `zugriff`, `root`)
VALUES
    ('1', 'admin', 'adm1n', 'all', ''),
    ('2', 'vorstand', 'v0r57and', 'ftp olz_text_1 aktuell galerie bild_der_woche', 'vorstand'),
    ('3', 'karten', 'kar73n', 'ftp', 'karten');

-- Table vorstand

-- Table vorstand_funktion

COMMIT;
