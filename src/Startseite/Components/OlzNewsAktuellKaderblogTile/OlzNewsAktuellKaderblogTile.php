<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsAktuellKaderblogTile;

use Olz\Apps\OlzApps;
use Olz\Entity\User;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\ImageUtils;

class OlzNewsAktuellKaderblogTile extends AbstractOlzTile {
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'kaderblog' => 'entry_type_kaderblog_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.65;
    }

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $entity_manager = $this->dbUtils()->getEntityManager();
        $image_utils = ImageUtils::fromEnv();
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getCodeHref();

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
        $aktuell_url = $this->getNewsUrl('aktuell');
        $kaderblog_url = $this->getNewsUrl('kaderblog');
        $out = <<<ZZZZZZZZZZ
        <h2><a href='{$aktuell_url}'>
            <img src='{$data_href}icns/entry_type_aktuell_20.svg' alt='Aktuell' class='link-icon'>
            Aktuell
        </a>
        &nbsp;&amp;&nbsp;
        <a href='{$kaderblog_url}'>
            <img src='{$data_href}icns/entry_type_kaderblog_20.svg' alt='Kaderblog' class='link-icon'>
            Kaderblog
        </a> {$newsletter_link}</h2>
        ZZZZZZZZZZ;

        $out .= "<ul class='links'>";
        $query = $entity_manager->createQuery(<<<'ZZZZZZZZZZ'
            SELECT n
            FROM Olz:News\NewsEntry n
            WHERE n.on_off = '1' and n.typ IN ('aktuell', 'kaderblog')
            ORDER BY n.datum DESC, n.zeit DESC
        ZZZZZZZZZZ);
        $query->setMaxResults(5);
        $index = 0;
        foreach ($query->getResult() as $news_entry) {
            $id = $news_entry->getId();
            $date = $news_entry->getDate()->format('d.m.');
            $title = $news_entry->getTitle();
            $format = $news_entry->getFormat();
            $image_ids = $news_entry->getImageIds();

            $icon_basename = self::$iconBasenameByFormat[$format];
            $icon = "{$code_href}icns/{$icon_basename}";
            $image = '';
            $is_image_right = ($index % 2) === 0;
            if (count($image_ids ?? []) > 0) {
                $class = $is_image_right ? 'right' : 'left';
                $olz_image = $image_utils->olzImage(
                    'news', $id, $image_ids[0] ?? null, 55, null, ' class="noborder"');
                $image = "<div class='link-image-{$class}'>{$olz_image}</div>";
            }
            $image_left = '';
            $image_right = '';
            if ($is_image_right) {
                $image_right = $image;
            } else {
                $image_left = $image;
            }

            $out .= <<<ZZZZZZZZZZ
            <li class='flex min-two-lines'>
                {$image_left}
                <img src='{$icon}' alt='{$format}' class='link-icon'>
                <a href='{$code_href}news/{$id}' style='flex-grow:1;'>
                    {$title}
                </a>
                {$image_right}
            </li>
            ZZZZZZZZZZ;

            $index++;
        }
        $out .= "</ul>";

        return $out;
    }

    private function getNewsUrl($format = null) {
        $code_href = $this->envUtils()->getCodeHref();

        $news_filter_utils = NewsFilterUtils::fromEnv();
        $filter = $news_filter_utils->getDefaultFilter();
        if ($format) {
            $filter['format'] = $format;
        }
        $enc_json_filter = urlencode(json_encode($filter));
        return "{$code_href}news?filter={$enc_json_filter}";
    }
}
