<?php

namespace Olz\Components\OlzSitemap;

use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Common\OlzComponent;
use Olz\Components\OlzHtmlSitemap\OlzHtmlSitemap;
use Olz\Components\OtherPages\OlzDatenschutz\OlzDatenschutz;
use Olz\Components\OtherPages\OlzFragenUndAntworten\OlzFragenUndAntworten;
use Olz\Components\OtherPages\OlzFuerEinsteiger\OlzFuerEinsteiger;
use Olz\Components\OtherPages\OlzMaterial\OlzMaterial;
use Olz\Components\OtherPages\OlzTrophy\OlzTrophy;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\Termine\Termin;
use Olz\Karten\Components\OlzKarten\OlzKarten;
use Olz\News\Components\OlzNewsList\OlzNewsList;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Roles\Components\OlzVerein\OlzVerein;
use Olz\Service\Components\OlzService\OlzService;
use Olz\Startseite\Components\OlzStartseite\OlzStartseite;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;
use Olz\Termine\Utils\TermineFilterUtils;

abstract class OlzSitemap extends OlzComponent {
    abstract public function getHtml($args = []): string;

    protected function getEntries(): array {
        $entityManager = $this->dbUtils()->getEntityManager();
        $base_url = 'https://olzimmerberg.ch/';
        $entries = [];

        $entries[] = [
            'title' => OlzStartseite::$title,
            'description' => OlzStartseite::$description,
            'url' => "{$base_url}",
            'updates' => 'daily',
            'importance' => '0.8',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzFuerEinsteiger::$title,
            'description' => OlzFuerEinsteiger::$description,
            'url' => "{$base_url}fuer_einsteiger",
            'updates' => 'daily',
            'importance' => '1.0',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzFragenUndAntworten::$title,
            'description' => OlzFragenUndAntworten::$description,
            'url' => "{$base_url}fragen_und_antworten",
            'updates' => 'daily',
            'importance' => '0.8',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzNewsList::$title,
            'description' => OlzNewsList::$description,
            'url' => "{$base_url}news",
            'updates' => 'daily',
            'importance' => '0.6',
            'level' => 0,
        ];

        $news_utils = NewsFilterUtils::fromEnv();
        $news_filters = $news_utils->getAllValidFiltersForSitemap();
        foreach ($news_filters as $news_filter) {
            $enc_json_filter = urlencode(json_encode($news_filter));
            $title = $news_utils->getTitleFromFilter($news_filter);
            $description = "News-Liste \"{$title}\"";
            $entries[] = [
                'title' => $title,
                'description' => $description,
                'url' => "{$base_url}news?filter={$enc_json_filter}",
                'updates' => 'monthly',
                'importance' => '0.4',
                'level' => 1,
            ];
        }

        $news_entries = $entityManager->getRepository(NewsEntry::class)->getAllActive();
        foreach ($news_entries as $news_entry) {
            $title = $news_entry->getTitle();
            $pretty_formats = [
                'aktuell' => "Aktuell-Eintrag",
                'kaderblog' => "Kaderblog-Eintrag",
                'galerie' => "Foto-Galerie",
                'video' => "Film",
                'forum' => "Forumseintrag",
            ];
            $pretty_format = $pretty_formats[$news_entry->getFormat()] ?? 'News-Eintrag';
            $pretty_author = OlzAuthorBadge::render([
                'user' => $news_entry->getAuthorUser(),
                'role' => $news_entry->getAuthorRole(),
                'name' => $news_entry->getAuthorName(),
                'email' => $news_entry->getAuthorEmail(),
                'mode' => 'text',
            ]);
            $pretty_date = $this->dateUtils()->formatDateTimeRange(
                $news_entry->getPublishedDate()?->format('Y-m-d'),
                $news_entry->getPublishedTime()?->format('H:i:s'),
                null,
                null,
                $format = 'long',
            );
            $description = "{$pretty_format} von {$pretty_author} am {$pretty_date}";
            $entries[] = [
                'title' => $title,
                'description' => $description,
                'url' => "{$base_url}news/{$news_entry->getId()}",
                'updates' => 'monthly',
                'importance' => '0.3',
                'level' => 2,
            ];
        }

        $entries[] = [
            'title' => OlzTermineList::$title,
            'description' => OlzTermineList::$description,
            'url' => "{$base_url}termine",
            'updates' => 'daily',
            'importance' => '0.6',
            'level' => 0,
        ];

        $termine_utils = TermineFilterUtils::fromEnv();
        $termine_filters = $termine_utils->getAllValidFiltersForSitemap();
        foreach ($termine_filters as $termine_filter) {
            $enc_json_filter = urlencode(json_encode($termine_filter));
            $title = $termine_utils->getTitleFromFilter($termine_filter);
            $description = "Termine-Liste \"{$title}\"";
            $entries[] = [
                'title' => $title,
                'description' => $description,
                'url' => "{$base_url}termine?filter={$enc_json_filter}",
                'updates' => 'monthly',
                'importance' => '0.4',
                'level' => 1,
            ];
        }

        $termine = $entityManager->getRepository(Termin::class)->getAllActive();
        foreach ($termine as $termin) {
            $title = $termin->getTitle();
            $pretty_date = $this->dateUtils()->formatDateTimeRange(
                $termin->getStartDate()?->format('Y-m-d'),
                $termin->getStartTime()?->format('H:i:s'),
                $termin->getEndDate()?->format('Y-m-d'),
                $termin->getEndTime()?->format('H:i:s'),
                $format = 'long',
            );
            $pretty_location = $termin->getLocation()?->getName() ?? '&nbsp;';
            $description = "{$pretty_date}, {$pretty_location}";
            $entries[] = [
                'title' => $title,
                'description' => $description,
                'url' => "{$base_url}termine/{$termin->getId()}",
                'updates' => 'monthly',
                'importance' => '0.2',
                'level' => 2,
            ];
        }

        $entries[] = [
            'title' => OlzKarten::$title,
            'description' => OlzKarten::$description,
            'url' => "{$base_url}karten",
            'updates' => 'monthly',
            'importance' => '0.5',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzMaterial::$title,
            'description' => OlzMaterial::$description,
            'url' => "{$base_url}material",
            'updates' => 'monthly',
            'importance' => '0.5',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzService::$title,
            'description' => OlzService::$description,
            'url' => "{$base_url}service",
            'updates' => 'monthly',
            'importance' => '0.3',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzVerein::$title,
            'description' => OlzVerein::$description,
            'url' => "{$base_url}verein",
            'updates' => 'monthly',
            'importance' => '0.5',
            'level' => 0,
        ];

        $verein_ressorts = $entityManager->getRepository(Role::class)->getAllActive();
        foreach ($verein_ressorts as $verein_ressort) {
            $title = "{$verein_ressort->getName()}"; // TODO: SEO title
            $description = "{$verein_ressort->getDescription()}"; // TODO: SEO description
            $entries[] = [
                'title' => $title,
                'description' => $description,
                'url' => "{$base_url}verein/{$verein_ressort->getUsername()}",
                'updates' => 'monthly',
                'importance' => '0.5',
                'level' => 1,
            ];
        }

        $entries[] = [
            'title' => OlzDatenschutz::$title,
            'description' => OlzDatenschutz::$description,
            'url' => "{$base_url}datenschutz",
            'updates' => 'monthly',
            'importance' => '0.1',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzTrophy::$title,
            'description' => OlzTrophy::$description,
            'url' => "{$base_url}trophy",
            'updates' => 'monthly',
            'importance' => '0.5',
            'level' => 0,
        ];
        $entries[] = [
            'title' => OlzHtmlSitemap::$title,
            'description' => OlzHtmlSitemap::$description,
            'url' => "{$base_url}sitemap",
            'updates' => 'daily',
            'importance' => '1.0',
            'level' => 0,
        ];

        return $entries;
    }
}
