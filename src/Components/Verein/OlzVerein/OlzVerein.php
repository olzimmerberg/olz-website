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
        $out = '';
        $out .= OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-full'><div id='organigramm'>";
        $out .= <<<'ZZZZZZZZZZ'
        <h2>Häufig gesucht</h2>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-5&quot;)' class='linkint'>Präsident</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-6&quot;)' class='linkint'>Mitgliederverwaltung</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-18&quot;)' class='linkint'>Kartenverkauf</a></b></div>
        <div><b><a href='javascript:olz.highlight_organigramm(&quot;link-role-19&quot;)' class='linkint'>Kleiderverkauf</a></b></div>
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
