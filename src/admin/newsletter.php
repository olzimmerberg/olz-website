<?php

// =============================================================================
// Versendet per E-Mail eine Zusammenfassung, was für Neuigkeiten auf der
// Webseite erschienen sind.
// =============================================================================

require_once __DIR__.'/../config/init.php';

$local = "0";
$admin_mail = "uhu1277@gmail.com";
$mail_from = "newsletter@olzimmerberg.ch";

set_include_path("../");
include_once 'olz_init.php';
include_once 'olz_functions.php';
$start = microtime(1);

//KONSTANTEN
//-----------------
$subject = [];
$mail_header = "From: OL Zimmerberg <newsletter@olzimmerberg.ch>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8 \r\nContent-Transfer-Encoding: base64";
$linie = "------------------------------------------------------------------------\r\n";
$text_nachspann = $linie."HINWEIS\r\n".$linie."Du erhältst dieses Mail, weil du dich für den Newsletter angemeldet hast. Über folgenden Link kannst du den Newsletter löschen oder die Einstellungen ändern: https://www.olzimmerberg.ch/_/service.php?status=Weiter&uid=";
$timestamp = date("Y-m-d")." ".date("H:i:s");

//Termine
//----------
$items = '';
$limit = "+3 day";
$sql = "SELECT * from termine WHERE (datum <= '{$search_date}') AND (newsletter = 1) AND (on_off = 1) AND (newsletter_datum IS NULL) ORDER BY datum DESC, id DESC";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $datum = $row['datum'];
    $titel = $row['titel'];
    $text = substr($row['text'], 0, 200);
    $id = $row['id'];
    $datum = utf8_encode(strftime("%d.%m.%y", strtotime($datum)));
    $subject['termine'] = $datum.": ".$titel;
    if ($text != "") {
        $subject['termine'] = $subject['termine']."\r\n".$text;
    }
    $items = $items.$subject['termine']." (...)\r\nDirekter Link: https://www.olzimmerberg.ch/_/termine.php#id{$id}\r\n\r\n";
    $sql = "UPDATE {$db_table} SET newsletter_datum = '{$timestamp}' WHERE (id = '{$id}')";
    $db->query($sql);
}

// Simon, 2017-03-30
$sql = "SELECT
    se.deadline AS deadline,
    t.titel AS titel,
    t.id AS id
FROM
    termine t
    JOIN solv_events se ON (se.solv_uid=t.solv_uid)
WHERE
    se.deadline <= '".date("Y-m-d", strtotime($limit))."' AND
    se.deadline > '".date("Y-m-d")."' AND
    on_off = 1 AND
    newsletter_anmeldung IS NULL
ORDER BY se.deadline DESC, t.id DESC";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $deadline = $row['deadline'];
    $titel = $row['titel'];
    $id = $row['id'];
    $deadline = utf8_encode(strftime("%d.%m.%y", strtotime($deadline)));
    $subject['termine'] = $deadline.": Meldeschluss ".$titel;
    $items = $items.$subject['termine']."\r\nDirekter Link: https://www.olzimmerberg.ch/_/termine.php#id{$id}\r\n\r\n";
    $sql = "UPDATE
    termine
SET newsletter_anmeldung = '{$timestamp}'
WHERE (id = '{$id}')";
    $db->query($sql);
}
if (isset($subject['termine'])) {
    $subject['termine'] = $linie."Terminerinnerung olzimmerberg.ch\r\n".$linie.$items;
}

//Aktuell
//---------
$db_table = "aktuell";
$items = '';
$sql = "select * from {$db_table} WHERE ((newsletter_datum IS NULL) OR (newsletter_datum='0000-00-00')) AND (newsletter = 1) AND (on_off = 1) ORDER BY datum DESC, id DESC";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $datum = $row['datum'];
    $zeit = $row['zeit'];
    $typ = $row['typ'];
    $link = $row['link'];
    $titel = strip_tags($row['titel']);
    $text = substr(strip_tags($row['text'], '<br>'), 0, 200);
    $text = trim(str_replace(['/BILD1/', '/BILD2/', '/BILD3/', '<BILD1>', '<BILD2>', '<BILD3>', chr(13), chr(10), '<br>'], ['', '', '', '', '', '', '', '', ' / '], $text)); //Umbrüche umwandeln
    $id = $row['id'];
    $datum = utf8_encode(strftime("%d.%m.%y", strtotime($datum)));
    $zeit = date("G:i", strtotime($zeit));

    if ($typ == "aktuell") {
        if ($link == "") {
            $link = "id={$id}";
        }
        $link = "aktuell.php?{$link}";
    } elseif ($typ == "termin") {
        $link = "termine.php#{$link}";
    } elseif ($typ == "galerie") {
        $link = "galerie.php?datum={$link}";
    } elseif ($typ == "forum") {
        $link = "forum.php#{$link}";
    } elseif ($typ == "jwoc") {
        $link = "blog.php";
    } else {
        $link = "";
    }

    $subject['aktuell'] = $datum.", ".$zeit.": ".$titel."\r\n".$text;
    $items = $items.$subject['aktuell']." (...)\r\nDirekter Link: https://www.olzimmerberg.ch/_/{$link}\r\n\r\n";
    $sql = "UPDATE {$db_table} SET newsletter_datum = '{$timestamp}' WHERE (id = '{$id}')";
    $db->query($sql);
}
if (isset($subject['aktuell'])) {
    $subject['aktuell'] = $linie."Neue Nachrichten auf olzimmerberg.ch\r\n".$linie.$items;
}

//Kaderblog
//---------
$db_table = "blog";
$items = '';
$sql = "select * from {$db_table} WHERE ((newsletter_datum IS NULL) OR (newsletter_datum='0000-00-00')) AND (newsletter = 1) AND (on_off = 1) AND (titel!='') AND (text!='') ORDER BY datum DESC, id DESC";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $datum = $row['datum'];
    $zeit = $row['zeit'];
    $titel = $row['titel'];
    $text = substr(strip_tags($row['text'], '<br>'), 0, 200);
    $text = trim(str_replace(['<BILD1>', '<BILD2>', chr(13), chr(10), '<br>'], ['', '', '', '', ' / '], $text)); //Umbrüche umwandeln
    $id = $row['id'];
    $datum = utf8_encode(strftime("%d.%m.%y", strtotime($datum)));
    $zeit = date("G:i", strtotime($zeit));

    $link = "blog.php";

    $subject['blog'] = $datum.", ".$zeit.": ".$titel."\r\n".$text;
    $items = $items.$subject['blog']." (...)\r\nDirekter Link: https://www.olzimmerberg.ch/_/{$link}\r\n\r\n";
    echo "*".$items."<br>";
    $sql = "UPDATE {$db_table} SET newsletter_datum = '{$timestamp}' WHERE (id = '{$id}')";
    $db->query($sql);
}
if (isset($subject['blog'])) {
    $subject['blog'] = $linie."Neuer Kaderblogeintrag auf olzimmerberg.ch\r\n".$linie.$items;
}

//Forum
//--------
$db_table = "forum";
$items = '';
$sql = "select * from {$db_table} WHERE ((newsletter_datum IS NULL) OR (newsletter_datum='0000-00-00')) AND (on_off = 1) AND (newsletter = 1 ) AND (email > '') AND (name > '') AND (eintrag != '') ORDER BY datum DESC, id DESC";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $datum = $row['datum'];
    $zeit = $row['zeit'];
    $name = $row['name'];
    $eintrag = substr($row['eintrag'], 0, 200);
    $eintrag = str_replace([chr(10), chr(13)], ['', ' '], $eintrag);
    $id = $row['id'];
    $datum = utf8_encode(strftime("%d.%m.%y", strtotime($datum)));
    $zeit = date("G:i", strtotime($zeit));
    $subject['forum'] = $datum.", ".$zeit."/".$name.":\r\n".$eintrag;
    $items = $items.$subject['forum']." (...)\r\nDirekter Link: https://www.olzimmerberg.ch/_/forum.php#id{$id}\r\n\r\n";
    $sql = "UPDATE {$db_table} SET newsletter_datum = '{$timestamp}' WHERE (id = '{$id}')";
    $db->query($sql);
}
if (isset($subject['forum'])) {
    $subject['forum'] = $linie."Neue Forumbeiträge auf olzimmerberg.ch\r\n".$linie.$items;
}

// Mail verschicken
if (isset($subject)) {
    $sql = "select * from newsletter WHERE (on_off = '1') AND (email > '') ORDER BY email DESC";
    $result = $db->query($sql);
    $num_rows = $result->num_rows;
    $mail_to = [];
    $mailtext = "";
    $betreff = "Newsletter OL Zimmerberg - ".date("d.m.y", strtotime('-1 day'));

    if ($local) {
        $mail_to = array_push($mail_to, $admin_mail);
        $mailtext = "\r\n".$subject['termine']."\r\n".$subject['aktuell']."\r\n".$subject['blog']."\r\n".$subject['forum']."\r\n".$text_nachspann;
        mail($mail_to, "=?UTF-8?B?".base64_encode($betreff)."?=", base64_encode($mailtext), $mail_header, $mail_from);
    } else {
        while ($row = $result->fetch_assoc()) {
            $mailtext1 = '';
            $mailtext2 = '';
            $mailtext3 = '';
            $mailtext4 = '';
            $email = $row['email'];
            $name = $row['name'];
            $kategorie = explode(' ', $row['kategorie']);
            $uid = $row['uid'];
            if (in_array('termine', $kategorie) and (isset($subject['termine']))) {
                $mailtext1 = $subject['termine']."\r\n";
            }
            if (in_array('aktuell', $kategorie) and (isset($subject['aktuell']))) {
                $mailtext2 = $subject['aktuell']."\r\n";
            }
            if (in_array('aktuell', $kategorie) and (isset($subject['blog']))) {
                $mailtext4 = $subject['blog']."\r\n";
            }
            if (in_array('forum', $kategorie) and (isset($subject['forum']))) {
                $mailtext3 = $subject['forum']."\r\n";
            }
            if (!empty($mailtext1) or !empty($mailtext2) or !empty($mailtext3) or !empty($mailtext4)) {
                $mailtext = "\r\n".$mailtext1.$mailtext2.$mailtext3.$mailtext4.$text_nachspann.$uid;
                mail($email, "=?UTF-8?B?".base64_encode($betreff)."?=", base64_encode($mailtext), $mail_header, $mail_from);
                //echo "email:".$email."<br>betreff:".$betreff."<br>mailtext:".$mailtext."<br>mailheader:".$mail_header;
                array_push($mail_to, $email);
                sleep(1);
            }
        }
    }
}
//mail("u.utzinger@sunrise.ch","=?UTF-8?B?".base64_encode($betreff)."?=",base64_encode($mailtext),$mail_header,$mail_from);
//REPORT-MAIL
if (is_array($mail_to)) {
    $mail_to = implode(', ', $mail_to);
}
mail("newsletter@olzimmerberg.ch", "Newsletter OL Zimmerberg - Report", base64_encode("Datum: ".date("Y-m-d")."/".date("H:i:s")."\r\nAdressen: ".$mail_to."\r\nStart: ".date("d.m.y, G:i:s", $start)."\r\nDauer: ".(microtime(1) - $start)."\r\nServer: ".$_SERVER['REMOTE_ADDR']), $mail_header, $mail_from);

echo "Datum: ".date("Y-m-d")."/".date("H:i:s")."\nAdressen: ".implode(', ', $mail_to)."\nStart: ".date("d.m.y, G:i:s", $start)."\nDauer: ".(microtime(1) - $start)."\nServer: ".$_SERVER['REMOTE_ADDR'];
