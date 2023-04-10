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
        global $base_href, $code_href, $_DATE;

        require_once __DIR__.'/../../../../_/config/init.php';
        require_once __DIR__.'/../../../../_/config/paths.php';
        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $db = $this->dbUtils()->getDb();
        $jahr = olz_current_date('Y');

        // Termine abfragen
        $sql = "SELECT * FROM termine WHERE (datum >= '{$jahr}-01-01') AND on_off=1";
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
        while ($row = mysqli_fetch_array($result)) {// Links extrahieren
            $links = $row['link'];
            $dom = new \DOMDocument();
            $dom->loadHTML($links || ' ');
            $_links = "OLZ-Termin: {$base_href}{$code_href}termine.php?id=".$row['id'];
            $_attach = "\r\nATTACH;FMTTYPE=text/html:{$base_href}{$code_href}termine.php?id=".$row['id'];
            foreach ($dom->getElementsByTagName("a") as $a) {
                $text = $a->textContent;
                $url = $a->getAttribute("href");
                $_links .= "\\n".$text.": ".$url;
                $_attach .= "\r\nATTACH;FMTTYPE=text/html:".$url;
            }
            $_links .= ($row['solv_uid'] > 0) ? "\\nSOLV-Termin: https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";
            $_attach .= ($row['solv_uid'] > 0) ? "\r\nATTACH;FMTTYPE=text/html:https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=".$row['solv_uid'] : "";

            $datum = $row['datum'];
            $datum_end = ($row['datum_end'] > "0000-00-00") ? $row['datum_end'] : $datum;
            $ical .=
        "\r\nBEGIN:VEVENT\nDTSTART;VALUE=DATE:".$_DATE->olzDate('jjjjmmtt', $datum).
        "\r\nDTEND;VALUE=DATE:".$_DATE->olzDate('jjjjmmtt', $datum_end).
        "\r\nDTSTAMP:".date('Ymd\THis\Z').
        "\r\nLAST-MODIFIED:".date('Ymd\THis\Z', strtotime($row['modified'])).
        "\r\nCREATED:".date('Ymd\THis\Z', strtotime($row['created'])).
        "\r\nSUMMARY:".$row['titel'].
        "\r\nDESCRIPTION:".str_replace("\r\n", "\\n", $row['text']).
        "\\n".$_links;
            $ical .=
        "\r\nCATEGORIES:".$row['typ'].
        $_attach.
        "\r\nCLASS:PUBLIC".
        "\r\nUID:olz_termin_".$row['id']."@olzimmerberg.ch".
        "\r\nEND:VEVENT";
        }

        $ical .= "\r\nEND:VCALENDAR";

        return $ical;
    }
}
