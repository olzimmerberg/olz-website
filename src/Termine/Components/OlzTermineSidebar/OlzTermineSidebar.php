<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineSidebar;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;

class OlzTermineSidebar extends OlzComponent {
    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $today = $this->dateUtils()->getIsoToday();
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';

        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        if ($has_termine_permissions) {
            $out .= "<div><b><a href='{$code_href}termine/orte' class='linkmap'>Termin-Orte</a></b></div>";
            $out .= "<div><b><a href='{$code_href}termine/vorlagen' class='linkint'>Termin-Vorlagen</a></b></div>";
        }
        $out .= "<h2>Trainings</h2>";

        // NÄCHSTES TRAINIG

        // Tabelle auslesen
        $sql = "SELECT * FROM termine WHERE ((end_date >= '{$today}') OR (end_date = '0000-00-00') OR (end_date IS NULL)) AND (start_date >= '{$today}') AND (typ LIKE '%training%') AND (on_off = '1') ORDER BY start_date ASC";
        // $out .= $sql;
        $result = $db->query($sql);

        $row = mysqli_fetch_array($result);
        if ($row) {
            $start_date = strtotime($row['start_date']);
            $title = $row['title'];
            $text = $row['text'];
            $id_training = $row['id'];

            $start_date = $this->dateUtils()->olzDate("t. MM", $start_date);
            if ($title == "") {
                $title = substr(str_replace("<br>", " ", $text), 0, 100);
            }

            $out .= "<p><b>Nächstes Training: </b>{$start_date}<br>{$title}, {$text}</p>";
        }
        $out .= OlzEditableText::render(['olz_text_id' => 1]);
        $out .= "<h2>Downloads und Links</h2>";
        $out .= OlzEditableText::render(['olz_text_id' => 2]);
        $out .= "<h2>Newsletter</h2>";
        $out .= OlzEditableText::render(['olz_text_id' => 3]);

        return $out;
    }
}
