<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit kürzlich veröffentlichten News an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsForumTile;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsForumTile extends AbstractOlzTile {
    /** @var array<string, string> */
    protected static $iconBasenameByFormat = [
        'forum' => 'entry_type_forum_20.svg',
    ];

    public function getRelevance(?User $user): float {
        return 0.6;
    }

    public function getHtml(mixed $args): string {
        $entity_manager = $this->dbUtils()->getEntityManager();
        $code_href = $this->envUtils()->getCodeHref();
        $news_filter_utils = NewsFilterUtils::fromEnv();

        $forum_url = $news_filter_utils->getUrl(['format' => 'forum']);

        $out = <<<ZZZZZZZZZZ
            <h3>
                <a href='{$forum_url}'>
                    <img src='{$code_href}assets/icns/entry_type_forum_20.svg' alt='Forum' class='link-icon'>
                    Forum
                </a>
            </h3>
            ZZZZZZZZZZ;

        $out .= "<ul class='links'>";
        $news_entry_class = NewsEntry::class;
        $query = $entity_manager->createQuery(<<<ZZZZZZZZZZ
                SELECT n
                FROM {$news_entry_class} n
                WHERE n.on_off = '1' and n.format IN ('forum')
                ORDER BY n.published_date DESC, n.published_time DESC
            ZZZZZZZZZZ);
        $query->setMaxResults(5);
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
            $is_image_right = ($index % 2) === 1;
            if (count($image_ids) > 0) {
                $olz_image = $this->imageUtils()->olzImage(
                    'news',
                    $id,
                    $image_ids[0] ?? null,
                    80,
                    'image',
                    ' class="noborder"'
                );
                $image = "{$olz_image}";
            }
            $image_left = '';
            $image_right = '';
            if ($is_image_right) {
                $image_right = "<div class='link-image-right'>{$image}</div>";
            } else {
                $image_left = "<div class='link-image-left'>{$image}</div>";
            }

            $class = $is_image_right ? 'right' : 'left';
            $out .= <<<ZZZZZZZZZZ
                <li class='{$class}'>
                    <div class='flex bubble'>
                        {$image_left}
                        <img src='{$icon}' alt='{$format}' class='link-icon'>
                        <a href='{$code_href}news/{$id}' class='title-link'>
                            <span class='title'>{$title}</span>
                        </a>
                        <span class='date'>{$date}</span>
                        {$image_right}
                    </div>
                </li>
                ZZZZZZZZZZ;

            $index++;
        }
        $out .= "</ul>";

        return $out;
    }
}
