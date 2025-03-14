<?php

namespace Olz\Roles\Components\OlzVerein;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Roles\Components\OlzOrganigramm\OlzOrganigramm;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzVereinParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzVerein extends OlzComponent {
    public static string $title = "Verein";
    public static string $description = "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.";

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzVereinParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
            'canonical_url' => "{$code_href}verein",
        ]);

        $db = $this->dbUtils()->getDb();
        $result = $db->query("SELECT id, name, title FROM roles WHERE featured_index IS NOT NULL ORDER BY featured_index ASC");
        $featured_out = '';
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['title'] ?? $row['name'];
            $featured_out .= "<div><b><a href='javascript:olz.highlightOrganigramm(&quot;role-{$id}&quot;)' class='linkint'>{$title}</a></b></div>";
        }
        $out .= "<div class='content-full'><div id='organigramm'>";
        $out .= <<<ZZZZZZZZZZ
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
