<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsEntriesTile;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsEntriesTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.55;
    }

    public function getHtml(mixed $args): string {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $news_utils = $this->newsUtils();

        $year = $this->dateUtils()->getCurrentDateInFormat('Y');
        $news_url = $news_utils->getUrl(['format' => 'alle', 'datum' => $year]);
        $aktuell_url = $news_utils->getUrl(['format' => 'aktuell', 'datum' => $year]);
        $galerie_url = $news_utils->getUrl(['format' => 'galerie', 'datum' => $year]);
        $video_url = $news_utils->getUrl(['format' => 'video', 'datum' => $year]);
        $kaderblog_url = $news_utils->getUrl(['format' => 'kaderblog', 'datum' => $year]);
        $forum_url = $news_utils->getUrl(['format' => 'forum', 'datum' => $year]);
        $out = <<<ZZZZZZZZZZ
            <h3>
                <a href='{$news_url}&von=startseite' class='header-link'>
                    <img src='{$code_href}assets/icns/entry_type_galerie_20.svg' alt='News' class='header-link-icon'>
                    News
                </a>
            </h3>
            ZZZZZZZZZZ;
        $out .= <<<ZZZZZZZZZZ
            <ul class='filters'>
                <li><a href='{$aktuell_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_aktuell_20.svg' alt='Aktuell' class='header-link-icon'>
                    Aktuell
                </a></li>
                <li><a href='{$galerie_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_galerie_20.svg' alt='Galerie' class='header-link-icon'>
                    Galerie
                </a></li>
                <li><a href='{$video_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_video_20.svg' alt='Video' class='header-link-icon'>
                    Video
                </a></li>
                <li><a href='{$kaderblog_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_kaderblog_20.svg' alt='Kaderblog' class='header-link-icon'>
                    Kaderblog
                </a></li>
                <li><a href='{$forum_url}&von=startseite'>
                    <img src='{$code_href}assets/icns/entry_type_forum_20.svg' alt='Forum' class='header-link-icon'>
                    Forum
                </a></li>
            </ul>
            ZZZZZZZZZZ;

        $out .= "<ul class='links'>";
        $news_entry_class = NewsEntry::class;
        $query = $entity_manager->createQuery(<<<ZZZZZZZZZZ
                SELECT n
                FROM {$news_entry_class} n
                WHERE n.on_off = '1'
                ORDER BY n.published_date DESC, n.published_time DESC
            ZZZZZZZZZZ);
        $is_prod = $this->envUtils()->getAppEnv() === 'prod';
        $query->setMaxResults($is_prod ? 5 : 100);
        $index = 0;
        foreach ($query->getResult() as $news_entry) {
            $id = $news_entry->getId();
            $date = $this->dateUtils()->compactDate($news_entry->getPublishedDate());
            $title = $news_entry->getTitle();
            $format = $news_entry->getFormat();
            $image_ids = $news_entry->getImageIds();
            $icon = $this->newsUtils()->getNewsFormatIcon($format);

            if ($format === 'aktuell' || $format === 'kaderblog') {
                $image = '';
                $is_image_right = ($index % 2) === 0;
                if (count($image_ids) > 0) {
                    $class = $is_image_right ? 'right' : 'left';
                    $olz_image = $this->imageUtils()->olzImage(
                        'news',
                        $id,
                        $image_ids[0] ?? null,
                        80,
                        null,
                        ' class="noborder"'
                    );
                    $image = "<div class='link-image-{$class}'>{$olz_image}</div>";
                }

                $out .= <<<ZZZZZZZZZZ
                    <li>
                        <a href='{$code_href}news/{$id}?von=startseite' class='flex min-two-lines aktuell-kaderblog-tile'>
                            <img src='{$icon}' alt='{$format}' class='link-icon'>
                            <div style='flex-grow:1;'>
                                <span class='title'>{$title}</span>
                                <span class='secondary'>({$date})</span>
                            </div>
                            {$image}
                        </a>
                    </li>
                    ZZZZZZZZZZ;
            }

            if ($format === 'galerie' || $format === 'video') {
                $icon = $this->newsUtils()->getNewsFormatIcon($format, 'white');
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
                        <a href='{$code_href}news/{$id}?von=startseite'>
                            <div class='images'>
                                <img src='{$icon}' alt='{$format}' class='link-icon'>
                                {$images}
                            </div>
                            <div class='overlay'>
                                <span>{$title}</span>
                                <span class='secondary'>({$date})</span>
                            </div>
                        </a>
                    </li>
                    ZZZZZZZZZZ;
            }

            if ($format === 'forum') {
                $image = '';
                if (count($image_ids) > 0) {
                    $olz_image = $this->imageUtils()->olzImage(
                        'news',
                        $id,
                        $image_ids[0] ?? null,
                        80,
                        null,
                        ' class="image"'
                    );
                    $image = "{$olz_image}";
                }

                $out .= <<<ZZZZZZZZZZ
                    <li class='forum'>
                        <img src='{$icon}' alt='{$format}' class='link-icon'>
                        <a href='{$code_href}news/{$id}?von=startseite'>
                            <div class='bubble'>
                                <span class='title'>{$title}</span>
                                <span class='date'>{$date}</span>
                                {$image}
                            </div>
                        </a>
                    </li>
                    ZZZZZZZZZZ;
            }

            $index++;
        }
        $out .= "</ul>";

        return $out;
    }
}
