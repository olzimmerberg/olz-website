<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineSidebar;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Utils\DbUtils;

class OlzTermineSidebar extends OlzComponent {
    public function getHtml($args = []): string {
        global $heute, $_DATE;

        require_once __DIR__.'/../../../../_/config/date.php';

        $db = DbUtils::fromEnv()->getDb();

        $out = "<h2>Trainings</h2>";

        // NÄCHSTES TRAINIG
        // Konstanten
        $db_table = "termine";

        // Tabelle auslesen
        $sql = "select * from {$db_table} WHERE ((datum_end >= '{$heute}') OR (datum_end = '0000-00-00') OR (datum_end IS NULL)) AND (datum >= '{$heute}') AND (typ LIKE '%training%') AND (on_off = '1') ORDER BY datum ASC";
        // $out .= $sql;
        $result = $db->query($sql);

        $row = mysqli_fetch_array($result);
        if ($row) {
            $datum = strtotime($row['datum']);
            $titel = $row['titel'];
            $text = $row['text'];
            $id_training = $row['id'];

            $datum = $_DATE->olzDate("t. MM", $datum);
            if ($titel == "") {
                $titel = substr(str_replace("<br>", " ", $text), 0, $textlaenge);
            }

            $out .= "<p><b>Nächstes Training: </b>{$datum}<br>{$titel}, {$text}</p>";
        }
        $out .= OlzEditableText::render(['olz_text_id' => 1]);
        $out .= "<h2>Downloads und Links</h2>";
        $out .= OlzEditableText::render(['olz_text_id' => 2]);
        $out .= "<h2>Newsletter</h2>";
        $out .= OlzEditableText::render(['olz_text_id' => 3]);

        return $out;
    }
}
