<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsAktuellKaderblogTile;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsAktuellKaderblogTile extends AbstractOlzTile {
    /** @var array<string, string> */
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'kaderblog' => 'entry_type_kaderblog_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.65;
    }

    public function getHtml(mixed $args): string {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $news_filter_utils = NewsFilterUtils::fromEnv();

        $aktuell_url = $news_filter_utils->getUrl(['format' => 'aktuell']);
        $kaderblog_url = $news_filter_utils->getUrl(['format' => 'kaderblog']);
        $out = <<<ZZZZZZZZZZ
            <h3>
                <a href='{$aktuell_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_aktuell_20.svg' alt='Aktuell' class='link-icon'>
                    Aktuell
                </a>
                &nbsp;&amp;&nbsp;
                <a href='{$kaderblog_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_kaderblog_20.svg' alt='Kaderblog' class='link-icon'>
                    Kaderblog
                </a>
            </h3>
            ZZZZZZZZZZ;

        $out .= "<ul class='links'>";
        $news_entry_class = NewsEntry::class;
        $query = $entity_manager->createQuery(<<<ZZZZZZZZZZ
                SELECT n
                FROM {$news_entry_class} n
                WHERE n.on_off = '1' and n.format IN ('aktuell', 'kaderblog')
                ORDER BY n.published_date DESC, n.published_time DESC
            ZZZZZZZZZZ);
        $query->setMaxResults(4);
        $index = 0;
        foreach ($query->getResult() as $news_entry) {
            $id = $news_entry->getId();
            $date = $this->dateUtils()->compactDate($news_entry->getPublishedDate());
            $title = $news_entry->getTitle();
            $format = $news_entry->getFormat();
            $image_ids = $news_entry->getImageIds();

            $icon_basename = self::$iconBasenameByFormat[$format];
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            $image = '';
            $is_image_right = ($index % 2) === 0;
            if (count($image_ids) > 0) {
                $class = $is_image_right ? 'right' : 'left';
                $olz_image = $this->imageUtils()->olzImage(
                    'news',
                    $id,
                    $image_ids[0] ?? null,
                    80,
                    'image',
                    ' class="noborder"'
                );
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
                <li class='flex min-two-lines aktuell-kaderblog-tile'>
                    {$image_left}
                    <img src='{$icon}' alt='{$format}' class='link-icon'>
                    <a href='{$code_href}news/{$id}?von=startseite' style='flex-grow:1;'>
                        <span class='title'>{$title}</span>
                        <span class='secondary'>({$date})</span>
                    </a>
                    {$image_right}
                </li>
                ZZZZZZZZZZ;

            $index++;
        }
        $out .= "</ul>";

        return $out;
    }
}
