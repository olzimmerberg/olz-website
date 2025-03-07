<?php

namespace Olz\Suche\Components\OlzSuche;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{anfrage: string}> */
class OlzSucheParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzSuche extends OlzComponent {
    public function getHtml(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzSucheParams::class);
        $search_key = $params['anfrage'];
        $date_utils = $this->dateUtils();
        $db = $this->dbUtils()->getDb();
        $env_utils = $this->envUtils();
        $code_href = $env_utils->getCodeHref();

        $out = OlzHeader::render([
            'title' => "Suche",
            'description' => "Stichwort-Suche auf der Website der OL Zimmerberg.",
            'norobots' => true,
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-right'>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $search_key = trim(str_replace([",", ".", ";", "   ", "  "], [" ", " ", " ", " ", " "], $search_key));
        $search_words = explode(" ", $search_key, 4);
        $sql = "";

        $sql_termine = '';
        $sql_news = '';
        for ($n = 0; $n < 3; $n++) {
            $search_key = $search_words[$n] ?? '';
            $search_key = $db->real_escape_string($search_key);
            if ($search_key > "") {
                $sql_termine .= <<<ZZZZZZZZZZ
                    (
                        (title LIKE '%{$search_key}%')
                        OR (text LIKE '%{$search_key}%')
                    )
                    AND
                    ZZZZZZZZZZ;
            }
            if ($search_key > "") {
                $sql_news .= <<<ZZZZZZZZZZ
                    (
                        (title LIKE '%{$search_key}%')
                        OR (teaser LIKE '%{$search_key}%')
                        OR (content LIKE '%{$search_key}%')
                    )
                    AND
                    ZZZZZZZZZZ;
            }
        }

        $pretty_search_words = implode(', ', $search_words);
        $out .= "<h2>Suchresultate (Suche nach: {$pretty_search_words})</h2>";

        $result_termine = '';
        $result_news = '';

        // TERMINE
        $sql = "SELECT * FROM termine WHERE {$sql_termine}(on_off = 1) ORDER BY start_date DESC";
        $result = $db->query($sql);
        // @phpstan-ignore-next-line
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $result_termine .= "<tr><td colspan='2'><h3 class='bar green'>Termine...</h3></td></tr>";
        }

        for ($i = 0; $i < $num; $i++) {
            // @phpstan-ignore-next-line
            $row = mysqli_fetch_array($result);
            // @phpstan-ignore-next-line
            $title = strip_tags($row['title']);
            // @phpstan-ignore-next-line
            $text = strip_tags($row['text']);
            // @phpstan-ignore-next-line
            $id = $row['id'];
            // @phpstan-ignore-next-line
            $start_date = $date_utils->olzDate("t. MM jjjj", $row['start_date']);
            $cutout = $this->cutout($text, $search_words);
            $result_termine .= <<<ZZZZZZZZZZ
                <tr>
                    <td>
                        <a href="{$code_href}termine/{$id}" class="linkint">
                            <b>{$start_date}</b>
                        </a>
                    </td>
                    <td>
                        <a href="{$code_href}termine/{$id}" class="linkint">
                            <b>{$this->highlight($title, $search_words)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $search_words)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // NEWS
        $result = $db->query("SELECT * FROM news WHERE {$sql_news}(on_off = 1) ORDER BY published_date DESC");
        // @phpstan-ignore-next-line
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $result_news = "<tr><td colspan='2'><h3 class='bar green'>News...</h3></td></tr>";
        }

        for ($i = 0; $i < $num; $i++) {
            // @phpstan-ignore-next-line
            $row = mysqli_fetch_array($result);
            // @phpstan-ignore-next-line
            $title = strip_tags($row['title']);
            // @phpstan-ignore-next-line
            $text = strip_tags($row['teaser']).strip_tags($row['content']);
            // @phpstan-ignore-next-line
            $id = $row['id'];
            // @phpstan-ignore-next-line
            $published_date = $date_utils->olzDate("t. MM jjjj", $row['published_date']);
            $cutout = $this->cutout($text, $search_words);
            $result_news .= <<<ZZZZZZZZZZ
                <tr>
                    <td>
                        <a href="{$code_href}news/{$id}" class="linkint">
                            <b>{$published_date}</b>
                        </a>
                    </td>
                    <td>
                        <a href="{$code_href}news/{$id}" class="linkint">
                            <b>{$this->highlight($title, $search_words)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $search_words)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        $text = $result_termine.$result_news;

        if ($text != '') {
            $out .= "<table>".$text."</table>";
        }

        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }

    /** @param array<string> $search_words */
    protected function cutout(string $text, array $search_words): string {
        $length_a = 40;
        $length_b = 40;

        for ($m = 0; $m < 3; $m++) {
            $prefix = "...";
            $suffix = "...";
            $search_key = $search_words[$m] ?? '';
            $start = strpos(strtolower($text), $search_key);
            if ($start > 0) {
                $m = 3;
            }
        }
        if (($start - $length_a) < 0) {
            $start = $length_a;
            $prefix = "";
        }
        if (strlen($text) < ($length_a + $length_b)) {
            $suffix = "";
        }
        $text = substr($text, $start - $length_a, $length_a + $length_b);
        return "{$prefix}{$text}{$suffix}";
    }

    /** @param array<string> $search_words */
    protected function highlight(string $text, array $search_words): string {
        for ($n = 0; $n < 3; $n++) {
            $search_key = $search_words[$n] ?? '';
            $search_variants = [
                $search_key,
                strtoupper($search_key),
                ucfirst($search_key), ];
            $replace_variants = [
                '<span style="color:red">'.$search_key.'</span>',
                '<span style="color:red">'.strtoupper($search_key).'</span>',
                '<span style="color:red">'.ucfirst($search_key).'</span>', ];
            $text = str_replace($search_variants, $replace_variants, $text);
        }
        return $text;
    }
}
