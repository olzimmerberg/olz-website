<?php

namespace Olz\Suche\Components\OlzSuche;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Faq\Question;
use Olz\Entity\Karten\Karte;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Entity\Termine\Termin;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{anfrage: string}> */
class OlzSucheParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzSuche extends OlzComponent {
    public function getHtml(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzSucheParams::class);
        $date_utils = $this->dateUtils();
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

        $terms = preg_split('/[\s,\.;]+/', $params['anfrage']);
        $this->generalUtils()->checkNotFalse($terms, "Could not split search terms '{$params['anfrage']}'");
        $pretty_terms = implode(', ', $terms);
        $out .= "<h2>Suchresultate (Suche nach: {$pretty_terms})</h2>";

        $start_time = microtime(true);

        $questions_out = '';
        $karten_out = '';
        $news_out = '';
        $roles_out = '';
        $downloads_out = '';
        $links_out = '';
        $termine_out = '';

        // FAQ
        $question_repo = $this->entityManager()->getRepository(Question::class);
        $questions = $question_repo->search($terms);
        if (!$questions->isEmpty()) {
            $questions_out = "<tr><td colspan='2'><h3 class='bar green'>Fragen & Antworten...</h3></td></tr>";
        }
        foreach ($questions as $question) {
            $ident = $question->getIdent();
            $cutout = $this->cutout($question->getIdent()." ".$question->getAnswer(), $terms);
            $questions_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td></td>
                    <td>
                        <a href="{$code_href}fragen_und_antworten/{$ident}" class="linkint">
                            <b>{$this->highlight($question->getQuestion(), $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // KARTEN
        $karte_repo = $this->entityManager()->getRepository(Karte::class);
        $karten = $karte_repo->search($terms);
        if (!$karten->isEmpty()) {
            $karten_out = "<tr><td colspan='2'><h3 class='bar green'>Karten...</h3></td></tr>";
        }
        foreach ($karten as $karte) {
            $id = $karte->getId();
            $cutout = $this->cutout("{$karte->getPlace()}", $terms);
            $karten_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td></td>
                    <td>
                        <a href="{$code_href}karten/{$id}" class="linkint">
                            <b>{$this->highlight($karte->getName(), $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // NEWS
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news = $news_repo->search($terms);
        if (!$news->isEmpty()) {
            $news_out = "<tr><td colspan='2'><h3 class='bar green'>News...</h3></td></tr>";
        }
        foreach ($news as $news_entry) {
            $id = $news_entry->getId();
            $cutout = $this->cutout($news_entry->getTeaser()." ".$news_entry->getContent(), $terms);
            $news_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>
                        <a href="{$code_href}news/{$id}" class="linkint">
                            <b>{$date_utils->olzDate("t. MM jjjj", $news_entry->getPublishedDate())}</b>
                        </a>
                    </td>
                    <td>
                        <a href="{$code_href}news/{$id}" class="linkint">
                            <b>{$this->highlight($news_entry->getTitle(), $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // ROLES
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $roles = $role_repo->search($terms);
        if (!$roles->isEmpty()) {
            $roles_out = "<tr><td colspan='2'><h3 class='bar green'>Ressorts...</h3></td></tr>";
        }
        foreach ($roles as $role) {
            $id = $role->getId();
            $cutout = $this->cutout("{$role->getUsername()} {$role->getOldUsername()} {$role->getDescription()} {$role->getGuide()}", $terms);
            $roles_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td></td>
                    <td>
                        <a href="{$code_href}karten/{$id}" class="linkint">
                            <b>{$this->highlight($role->getName(), $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // SERVICE
        $download_repo = $this->entityManager()->getRepository(Download::class);
        $downloads = $download_repo->search($terms);
        if (!$downloads->isEmpty()) {
            $downloads_out = "<tr><td colspan='2'><h3 class='bar green'>Downloads...</h3></td></tr>";
        }
        foreach ($downloads as $download) {
            $downloads_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td></td>
                    <td>
                        <a href="{$code_href}service" class="linkint">
                            <b>{$this->highlight($download->getName() ?? '', $terms)}</b>
                        </a>
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }
        $link_repo = $this->entityManager()->getRepository(Link::class);
        $links = $link_repo->search($terms);
        if (!$links->isEmpty()) {
            $links_out = "<tr><td colspan='2'><h3 class='bar green'>Links...</h3></td></tr>";
        }
        foreach ($links as $link) {
            $cutout = $this->cutout("{$link->getUrl()}}", $terms);
            $links_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td></td>
                    <td>
                        <a href="{$code_href}service" class="linkint">
                            <b>{$this->highlight($link->getName() ?? '', $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        // TODO: Snippets
        // TODO: Weekly picture

        // TERMINE
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termine = $termin_repo->search($terms);
        if (!$termine->isEmpty()) {
            $termine_out .= "<tr><td colspan='2'><h3 class='bar green'>Termine...</h3></td></tr>";
        }
        foreach ($termine as $termin) {
            $id = $termin->getId();
            $cutout = $this->cutout($termin->getText() ?? '', $terms);
            $termine_out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>
                        <a href="{$code_href}termine/{$id}" class="linkint">
                            <b>{$date_utils->olzDate("t. MM jjjj", $termin->getStartDate())}</b>
                        </a>
                    </td>
                    <td>
                        <a href="{$code_href}termine/{$id}" class="linkint">
                            <b>{$this->highlight($termin->getTitle() ?? '', $terms)}</b>
                        </a>
                        <br>
                        {$this->highlight($cutout, $terms)}
                    </td>
                </tr>
                ZZZZZZZZZZ;
        }

        $duration = microtime(true) - $start_time;
        $pretty_duration = number_format($duration, 3, '.', '\'');
        $this->log()->info("Search for '{$pretty_terms}' took {$pretty_duration}s.");

        $out .= "<table>
            {$questions_out}
            {$karten_out}
            {$news_out}
            {$roles_out}
            {$downloads_out}
            {$links_out}
            {$termine_out}
        </table>";
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
