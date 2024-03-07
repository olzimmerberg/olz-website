<?php

namespace Olz\Components\Verein\OlzVerein;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Verein\OlzOrganigramm\OlzOrganigramm;

class OlzVerein extends OlzComponent {
    public static $title = "Verein";
    public static $description = "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $db = $this->dbUtils()->getDb();
        $result = $db->query("SELECT id, name, title FROM roles WHERE featured_index IS NOT NULL AND on_off='1' ORDER BY featured_index ASC");
        $featured_out = '';
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['title'] ?? $row['name'];
            $featured_out .= "<div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-{$id}&quot;)' class='linkint'>{$title}</a></b></div>";
        }
        $out .= "<div class='content-full'><div id='organigramm'>";
        $out .= <<<'ZZZZZZZZZZ'
        <h2>Häufig gesucht</h2>
        {$featured_out}
        <div>
            <br />
            <div><b>PC-Konto</b></div>
            <div><b>IBAN: </b>CH91 0900 0000 8525 6448 8</div>
            <div><b>Empfänger: </b>OL Zimmerberg, 8800 Thalwil</div>
        </div>
        ZZZZZZZZZZ;

        $out .= OlzOrganigramm::render();

        $out .= "</div></div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
