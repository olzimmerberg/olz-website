<?php

// =============================================================================
// iCal-Datei generieren mit Terminen des aktuellen Jahres.
// Dieses Script wird immer beim Sichern und beim Löschen eines Termins
// aufgerufen.
// =============================================================================

namespace Olz\Termine\Components\OlzICal;

use Olz\Components\Common\OlzComponent;

class OlzICal extends OlzComponent {
    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $jahr = $this->dateUtils()->getCurrentDateInFormat('Y');
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $now_fmt = date('Ymd\THis\Z');

        // Termine abfragen
        $sql = "SELECT * FROM termine WHERE (start_date >= '{$jahr}-01-01') AND on_off=1";
        $result = $db->query($sql);

        // ical-Kalender
        $ical = "BEGIN:VCALENDAR".
        "\r\nPRODID:OL Zimmerberg Termine".
        "\r\nVERSION:2.0".
        "\r\nMETHOD:PUBLISH".
        "\r\nCALSCALE:GREGORIAN".
        "\r\nX-WR-CALNAME:OL Zimmerberg Termine".
        "\r\nX-WR-TIMEZONE:Europe/Zurich";

        // Termine
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            $duration_days = (strtotime($end_date) - strtotime($start_date)) / 86400;
            $should_split = $duration_days > 8;

            // Links extrahieren
            $links = $row['link'];
            $dom = new \DOMDocument();
            $dom->loadHTML($links ? $links : ' ');
            $_links = "OLZ-Termin: {$base_href}{$code_href}termine/{$id}";
            $_attach = "\r\nATTACH;FMTTYPE=text/html:{$base_href}{$code_href}termine/{$id}";
            foreach ($dom->getElementsByTagName("a") as $a) {
                $text = $a->textContent;
                $url = $a->getAttribute("href");
                $_links .= "\\n".$text.": ".$url;
                $_attach .= "\r\nATTACH;FMTTYPE=text/html:".$url;
            }
            $_links .= ($row['solv_uid'] > 0) ? "\\nSOLV-Termin: https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";
            $_attach .= ($row['solv_uid'] > 0) ? "\r\nATTACH;FMTTYPE=text/html:https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";

            $start_date_fmt = $this->dateUtils()->olzDate('jjjjmmtt', $start_date);
            $end_date_fmt = ($end_date > "0000-00-00") ? $this->dateUtils()->olzDate('jjjjmmtt', $end_date) : $start_date_fmt;
            $modified_fmt = date('Ymd\THis\Z', strtotime($row['last_modified_at']));
            $created_fmt = date('Ymd\THis\Z', strtotime($row['created_at']));
            $description_fmt = str_replace("\r\n", "\\n", $row['text'])."\\n".$_links;
            if ($should_split) {
                $ical .=
                "\r\nBEGIN:VEVENT\nDTSTART;VALUE=DATE:".$start_date_fmt.
                "\r\nDTEND;VALUE=DATE:".$start_date_fmt.
                "\r\nDTSTAMP:".$now_fmt.
                "\r\nLAST-MODIFIED:".$modified_fmt.
                "\r\nCREATED:".$created_fmt.
                "\r\nSUMMARY:".$row['title']." (Beginn)".
                "\r\nDESCRIPTION:".$description_fmt.
                "\r\nCATEGORIES:".$row['typ'].
                $_attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}_start@olzimmerberg.ch".
                "\r\nEND:VEVENT".
                "\r\nBEGIN:VEVENT\nDTSTART;VALUE=DATE:".$end_date_fmt.
                "\r\nDTEND;VALUE=DATE:".$end_date_fmt.
                "\r\nDTSTAMP:".$now_fmt.
                "\r\nLAST-MODIFIED:".$modified_fmt.
                "\r\nCREATED:".$created_fmt.
                "\r\nSUMMARY:".$row['title']." (Ende)".
                "\r\nDESCRIPTION:".$description_fmt.
                "\r\nCATEGORIES:".$row['typ'].
                $_attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}_end@olzimmerberg.ch".
                "\r\nEND:VEVENT";
            } else {
                $ical .=
                "\r\nBEGIN:VEVENT\nDTSTART;VALUE=DATE:".$start_date_fmt.
                "\r\nDTEND;VALUE=DATE:".$end_date_fmt.
                "\r\nDTSTAMP:".$now_fmt.
                "\r\nLAST-MODIFIED:".$modified_fmt.
                "\r\nCREATED:".$created_fmt.
                "\r\nSUMMARY:".$row['title'].
                "\r\nDESCRIPTION:".$description_fmt.
                "\r\nCATEGORIES:".$row['typ'].
                $_attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}@olzimmerberg.ch".
                "\r\nEND:VEVENT";
            }
        }

        $ical .= "\r\nEND:VCALENDAR";

        return $ical;
    }
}
