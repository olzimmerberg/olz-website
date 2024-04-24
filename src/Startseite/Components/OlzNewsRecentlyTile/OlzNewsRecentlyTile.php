<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsRecentlyTile;

use Olz\Apps\OlzApps;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsRecentlyTile extends AbstractOlzTile {
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

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();

        $newsletter_link = '';
        $newsletter_app = OlzApps::getApp('Newsletter');
        if ($newsletter_app) {
            $newsletter_link = <<<ZZZZZZZZZZ
                <a href='{$code_href}{$newsletter_app->getHref()}' class='newsletter-link'>
                    <img
                        src='{$newsletter_app->getIcon()}'
                        alt='newsletter'
                        class='newsletter-link-icon'
                        title='Newsletter abonnieren!'
                    />
                </a>
                ZZZZZZZZZZ;
        } else {
            $this->log()->error('Newsletter App does not exist!');
        }
        $out = "<h2>Letzte News {$newsletter_link}</h2>";

        $out .= "<ul class='links'>";
        $query = $entity_manager->createQuery(<<<'ZZZZZZZZZZ'
                SELECT n
                FROM Olz:News\NewsEntry n
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
