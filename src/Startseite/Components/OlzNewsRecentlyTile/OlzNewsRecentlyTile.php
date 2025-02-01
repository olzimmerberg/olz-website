<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsRecentlyTile;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsRecentlyTile extends AbstractOlzTile {
    /** @var array<string, string> */
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'forum' => 'entry_type_forum_20.svg',
        'galerie' => 'entry_type_gallery_20.svg',
        'kaderblog' => 'entry_type_kaderblog_20.svg',
        'video' => 'entry_type_movie_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.7;
    }

    public function getHtml(mixed $args): string {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();

        $out = "<h2>Letzte News</h2>";

        $out .= "<ul class='links'>";
        $news_entry_class = NewsEntry::class;
        $query = $entity_manager->createQuery(<<<ZZZZZZZZZZ
                SELECT n
                FROM {$news_entry_class} n
                WHERE n.on_off = '1'
                ORDER BY n.published_date DESC, n.published_time DESC
            ZZZZZZZZZZ);
        $query->setMaxResults(7);
        foreach ($query->getResult() as $news_entry) {
            $id = $news_entry->getId();
            $date = $news_entry->getPublishedDate()->format('d.m.');
            $title = $news_entry->getTitle();
            $format = $news_entry->getFormat();
            $icon_basename = self::$iconBasenameByFormat[$format];
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            $out .= <<<ZZZZZZZZZZ
                <li><a href='{$code_href}news/{$id}'>
                    <img src='{$icon}' alt='{$format}' class='link-icon'>
                    {$title}
                </a></li>
                ZZZZZZZZZZ;
        }
        $out .= "</ul>";

        return $out;
    }
}
