<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich geänderten Terminen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineUpdatesTile;

use Olz\Apps\OlzApps;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineUpdatesTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.5;
    }

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
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
        $out = "<h2>Aktualisierte Termine {$newsletter_link}</h2>";

        $out .= "<ul class='links'>";
        $res = $db->query(<<<'ZZZZZZZZZZ'
            SELECT t.id, t.start_date as date, t.title as title, t.last_modified_at
            FROM termine t
            WHERE t.on_off = '1' AND t.newsletter = '1'
            ORDER BY t.last_modified_at DESC
            LIMIT 5
        ZZZZZZZZZZ);
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $modified = date('d.m.', strtotime($row['last_modified_at']));
            $date = date('d.m.', strtotime($row['date']));
            $title = $row['title'];
            $out .= "<li><a href='{$code_href}termine/{$id}' class='linkint'><b>{$modified}</b>: Änderung an {$title} vom {$date}</a></li>";
        }
        $out .= "</ul>";

        return $out;
    }
}
