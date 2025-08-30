<?php

namespace Olz\Suche\Components\OlzSuche;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
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
        $code_href = $this->envUtils()->getCodeHref();

        $terms = preg_split('/[\s,\.;]+/', $params['anfrage']);
        $this->generalUtils()->checkNotFalse($terms, "Could not split search terms '{$params['anfrage']}'");
        $pretty_terms = implode(', ', $terms);
        $esc_pretty_terms = htmlspecialchars($pretty_terms);

        $out = OlzHeader::render([
            'title' => "\"{$pretty_terms}\" - Suche",
            'description' => "Stichwort-Suche nach \"{$pretty_terms}\" auf der Website der OL Zimmerberg.",
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <div class='content-right'>
            </div>
            <div class='content-middle olz-suche'>
            ZZZZZZZZZZ;

        $out .= "<h1>Suchresultate für \"{$esc_pretty_terms}\"</h1>";

        if (($terms[0] ?? '') === '') {
            $out .= "<p><i>Keine Resultate</i></p>";
            $out .= OlzFooter::render();
            return $out;
        }

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
            $questions_out = "<h2 class='bar green'>Fragen & Antworten</h2>";
        }
        foreach ($questions as $question) {
            $ident = $question->getIdent();
            $cutout = $this->searchUtils()->getCutout($question->getIdent()." ".$question->getAnswer(), $terms);
            $questions_out .= OlzPostingListItem::render([
                'link' => "{$code_href}fragen_und_antworten/{$ident}",
                'icon' => "{$code_href}assets/icns/question_mark_20.svg",
                'title' => $this->searchUtils()->highlight($question->getQuestion(), $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        // KARTEN
        $karte_repo = $this->entityManager()->getRepository(Karte::class);
        $karten = $karte_repo->search($terms);
        if (!$karten->isEmpty()) {
            $karten_out = "<h2 class='bar green'>Karten</h2>";
        }
        foreach ($karten as $karte) {
            $id = $karte->getId();
            $cutout = $this->searchUtils()->getCutout("{$karte->getPlace()}", $terms);
            $karten_out .= OlzPostingListItem::render([
                'link' => "{$code_href}karten/{$id}",
                'icon' => "{$code_href}assets/icns/link_map_16.svg",
                'title' => $this->searchUtils()->highlight($karte->getName(), $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        // NEWS
        $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
        $news = $news_repo->search($terms);
        if (!$news->isEmpty()) {
            $news_out = "<h2 class='bar green'>News</h2>";
        }
        foreach ($news as $news_entry) {
            $id = $news_entry->getId();
            $cutout = $this->searchUtils()->getCutout($news_entry->getTeaser()." ".$news_entry->getContent(), $terms);
            $news_out .= OlzPostingListItem::render([
                'link' => "{$code_href}news/{$id}",
                'icon' => $this->newsUtils()->getNewsFormatIcon($news_entry),
                'date' => $news_entry->getPublishedDate(),
                'title' => $this->searchUtils()->highlight($news_entry->getTitle(), $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        // ROLES
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $roles = $role_repo->search($terms);
        if (!$roles->isEmpty()) {
            $roles_out = "<h2 class='bar green'>Ressorts</h2>";
        }
        foreach ($roles as $role) {
            $ident = $role->getUsername();
            $cutout = $this->searchUtils()->getCutout("{$role->getUsername()} {$role->getOldUsername()} {$role->getDescription()} {$role->getGuide()}", $terms);
            $roles_out .= OlzPostingListItem::render([
                'link' => "{$code_href}verein/{$ident}",
                'icon' => "{$code_href}assets/icns/link_role_16.svg",
                'title' => $this->searchUtils()->highlight($role->getName(), $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        // SERVICE
        $download_repo = $this->entityManager()->getRepository(Download::class);
        $downloads = $download_repo->search($terms);
        if (!$downloads->isEmpty()) {
            $downloads_out = "<h2 class='bar green'>Downloads</h2>";
        }
        foreach ($downloads as $download) {
            $downloads_out .= OlzPostingListItem::render([
                'link' => "{$code_href}service",
                'icon' => "{$code_href}assets/icns/link_internal_16.svg", // TODO better icon
                'title' => $this->searchUtils()->highlight($download->getName() ?? '', $terms),
                'text' => '',
            ]);
        }
        $link_repo = $this->entityManager()->getRepository(Link::class);
        $links = $link_repo->search($terms);
        if (!$links->isEmpty()) {
            $links_out = "<h2 class='bar green'>Links</h2>";
        }
        foreach ($links as $link) {
            $cutout = $this->searchUtils()->getCutout("{$link->getUrl()}}", $terms);
            $links_out .= OlzPostingListItem::render([
                'link' => "{$code_href}service",
                'icon' => "{$code_href}assets/icns/link_internal_16.svg", // TODO better icon
                'title' => $this->searchUtils()->highlight($link->getName() ?? '', $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        // TODO: Snippets
        // TODO: Weekly picture

        // TERMINE
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termine = $termin_repo->search($terms);
        if (!$termine->isEmpty()) {
            $termine_out .= "<h2 class='bar green'>Termine</h2>";
        }
        foreach ($termine as $termin) {
            $id = $termin->getId();
            $cutout = $this->searchUtils()->getCutout($termin->getText() ?? '', $terms);
            $termine_out .= OlzPostingListItem::render([
                'link' => "{$code_href}termine/{$id}",
                'icon' => "{$code_href}assets/icns/termine_type_all_20.svg",
                'date' => $termin->getStartDate(),
                'title' => $this->searchUtils()->highlight($termin->getTitle() ?? '', $terms),
                'text' => $this->searchUtils()->highlight($cutout, $terms),
            ]);
        }

        $duration = microtime(true) - $start_time;
        $pretty_duration = number_format($duration, 3, '.', '\'');
        $this->log()->info("Search for '{$pretty_terms}' took {$pretty_duration}s.");

        $results = implode('', [
            $questions_out,
            $karten_out,
            $news_out,
            $roles_out,
            $downloads_out,
            $links_out,
            $termine_out,
        ]);
        $out .= $results ?: "<p><i>Keine Resultate</i></p>";
        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
