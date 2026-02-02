<?php

namespace Olz\Roles\Components\OlzVerein;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Roles\Components\OlzOrganigramm\OlzOrganigramm;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzVereinParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzVerein extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        return null;
    }

    public static string $title = "Verein";
    public static string $description = "Die wichtigsten Kontaktadressen und eine Liste aller Vereinsorgane der OL Zimmerberg.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzVereinParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
            'canonical_url' => "{$code_href}verein",
        ]);

        $db = $this->dbUtils()->getDb();
        $result = $db->query("SELECT id, name FROM roles WHERE featured_position IS NOT NULL ORDER BY featured_position ASC");
        $featured_out = '';
        // @phpstan-ignore-next-line
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $title = $row['name'];
            $featured_out .= "<div><b><a href='javascript:olz.highlightOrganigramm(&quot;role-{$id}&quot;)' class='linkint'>{$title}</a></b></div>";
        }
        $out .= "<div class='content-full'><h1>Verein</h1><div id='organigramm'>";
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
