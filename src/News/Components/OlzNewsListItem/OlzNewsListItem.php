<?php

namespace Olz\News\Components\OlzNewsListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\News\Components\OlzAuthorBadge\OlzAuthorBadge;
use Olz\News\Utils\NewsFilterUtils;

/** @extends OlzComponent<array<string, mixed>> */
class OlzNewsListItem extends OlzComponent {
    public function getHtml(mixed $args): string {
        $news_utils = NewsFilterUtils::fromEnv();
        $code_href = $this->envUtils()->getCodeHref();

        $news_entry = $args['news_entry'];
        $out = '';
        $current_filter = json_decode($_GET['filter'] ?? '{}', true);
        $filter_arg = '';
        if ($current_filter !== $news_utils->getDefaultFilter()) {
            $enc_current_filter = urlencode($_GET['filter'] ?? '{}');
            $filter_arg = "?filter={$enc_current_filter}";
        }

        $id = $news_entry->getId();
        $published_date = $news_entry->getPublishedDate();
        $format = $news_entry->getFormat();
        $icon = $this->newsUtils()->getNewsFormatIcon($format);
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();
        $title = $news_entry->getTitle();
        $teaser = $news_entry->getTeaser();
        $content = $news_entry->getContent();
        $link = "news/{$id}{$filter_arg}";

        $image_ids = $news_entry->getImageIds();
        $thumb = '';
        $size = count($image_ids);
        if ($size > 0) {
            $thumb = $this->imageUtils()->olzImage(
                'news',
                $id,
                $image_ids[0] ?? null,
                110,
                'image',
            );
        }

        $author_badge = OlzAuthorBadge::render([
            'news_id' => $news_entry->getId(),
            'user' => $author_user,
            'role' => $author_role,
            'name' => $author_name,
            'email' => $author_email,
        ]);

        $user = $this->authUtils()->getCurrentUser();
        $owner_user = $news_entry->getOwnerUser();
        $is_owner = $user && $owner_user && intval($owner_user->getId() ?? 0) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode($id);
            $json_mode = $args['json_mode'];
            $edit_admin = <<<ZZZZZZZZZZ
                <button
                    class='btn btn-secondary-outline btn-sm edit-news-list-button'
                    onclick='return olz.newsListItemEditNews({$json_id}, {$json_mode})'
                >
                    <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                </button>
                ZZZZZZZZZZ;
        }

        if ($format === 'aktuell') {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $thumb.strip_tags($this->htmlUtils()->renderMarkdown($teaser, [])),
                'link' => $link,
                'class' => 'has-thumb',
            ]);
        } elseif ($format === 'kaderblog') {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $thumb.strip_tags($this->htmlUtils()->renderMarkdown(
                    self::truncateText($content),
                )),
                'link' => $link,
                'class' => 'has-thumb',
            ]);
        } elseif ($format === 'forum') {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $thumb.strip_tags($this->htmlUtils()->renderMarkdown(
                    self::truncateText($content),
                )),
                'link' => $link,
                'class' => 'has-thumb',
            ]);
        } elseif ($format === 'galerie') {
            $thumbs = '';
            $used_thumb_indexes = [];
            $size = count($image_ids);
            for ($i = 0; $i < (($size > 4) ? 4 : $size); $i++) {
                $random_index = rand(1, $size);
                while (array_search($random_index, $used_thumb_indexes) !== false) {
                    $random_index = rand(1, $size);
                }
                array_push($used_thumb_indexes, $random_index);
                $thumbs .= "<td class='test-flaky'>".$this->imageUtils()->olzImage("news", $id, $image_ids[$random_index - 1], 110, 'image')."</td>";
            }
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => "<table><tr class='galerie-thumbs'>{$thumbs}</tr></table>",
                'link' => $link,
            ]);
        } elseif ($format === 'video') {
            $thumbnail = $this->imageUtils()->olzImage("news", $id, $image_ids[0] ?? null, 110, 'image');
            $content = <<<ZZZZZZZZZZ
                <div href='{$link}' style='background-color:#000;padding-top:0;' class='video-thumb'>\n
                <span style='display:block;background-image:url({$code_href}assets/icns/movie_dot.svg);background-repeat:repeat-x;height:24px;'></span>\n
                <span style='display:block;text-align:center;'>{$thumbnail}</span>\n
                <span style='display:block;background-image:url({$code_href}assets/icns/movie_dot.svg);background-repeat:repeat-x;height:24px;'></span>\n
                </div>
                ZZZZZZZZZZ;
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $content,
                'link' => $link,
            ]);
        } else {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'link' => $link,
            ]);
        }
        return $out;
    }

    protected static function truncateText(string $text): string {
        $max_length = 300;

        $text = preg_replace("/\\s*\\n\\s*/", "\n", $text) ?? $text;
        $text_length = mb_strlen($text);

        if ($text_length <= $max_length) {
            return $text;
        }
        $text = mb_substr($text, 0, $max_length - 6);
        $last_space = mb_strrpos($text, " ") ?: 0;
        $last_break = mb_strrpos($text, "<br>") ?: 0;
        $last_whitespace = ($last_break > $last_space) ? $last_break : $last_space;
        return mb_substr($text, 0, $last_whitespace).' [...]';
    }
}
