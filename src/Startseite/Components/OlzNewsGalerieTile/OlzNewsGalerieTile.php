<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsGalerieTile;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsGalerieTile extends AbstractOlzTile {
    /** @var array<string, string> */
    protected static $iconBasenameByFormat = [
        'galerie' => 'entry_type_gallery_white_20.svg',
        'video' => 'entry_type_movie_white_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.55;
    }

    public function getHtml(mixed $args): string {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $news_filter_utils = NewsFilterUtils::fromEnv();

        $galerie_url = $news_filter_utils->getUrl(['format' => 'galerie']);
        $video_url = $news_filter_utils->getUrl(['format' => 'video']);
        $out = <<<ZZZZZZZZZZ
            <h3>
                <a href='{$galerie_url}'>
                    <img src='{$code_href}assets/icns/entry_type_gallery_20.svg' alt='Galerie' class='link-icon'>
                    Galerie
                </a>
                &nbsp;&amp;&nbsp;
                <a href='{$video_url}'>
                    <img src='{$code_href}assets/icns/entry_type_movie_20.svg' alt='Video' class='link-icon'>
                    Video
                </a>
            </h3>
            ZZZZZZZZZZ;

        $out .= "<ul class='links'>";
        $news_entry_class = NewsEntry::class;
        $query = $entity_manager->createQuery(<<<ZZZZZZZZZZ
                SELECT n
                FROM {$news_entry_class} n
                WHERE n.on_off = '1' and n.format IN ('galerie', 'video')
                ORDER BY n.published_date DESC, n.published_time DESC
            ZZZZZZZZZZ);
        $query->setMaxResults(3);
        $index = 0;
        foreach ($query->getResult() as $news_entry) {
            $id = $news_entry->getId();
            $date = $this->dateUtils()->compactDate($news_entry->getPublishedDate());
            $title = $news_entry->getTitle();
            $format = $news_entry->getFormat();
            $image_ids = $news_entry->getImageIds();

            $icon_basename = self::$iconBasenameByFormat[$format];
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            $images = "";
            for ($i = 0; $i < min(count($image_ids), 3); $i++) {
                $olz_image = $this->imageUtils()->olzImage(
                    'news',
                    $id,
                    $image_ids[$i],
                    80,
                    null,
                    ' class="noborder"'
                );
                $images .= "{$olz_image}";
            }

            $out .= <<<ZZZZZZZZZZ
                <li class='flex gallery min-two-lines'>
                    <a href='{$code_href}news/{$id}'>
                        <div class='overlay'>
                            <img src='{$icon}' alt='{$format}' class='link-icon'>
                            <span class='date'>{$date}</span>
                            <span class='title'>{$title}</span>
                        </div>
                        <div class='images'>
                            {$images}
                        </div>
                    </a>
                </li>
                ZZZZZZZZZZ;

            $index++;
        }
        $out .= "</ul>";

        return $out;
    }
}
