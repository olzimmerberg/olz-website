<?php

namespace Olz\News\Components\OlzNewsListItem;

use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\News\Utils\NewsFilterUtils;

class OlzNewsListItem extends OlzComponent {
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'forum' => 'entry_type_forum_20.svg',
        'galerie' => 'entry_type_gallery_20.svg',
        'kaderblog' => 'entry_type_kaderblog_20.svg',
        'video' => 'entry_type_movie_20.svg',
    ];

    public function getHtml($args = []): string {
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
        $icon_basename = self::$iconBasenameByFormat[$format];
        $icon = "{$code_href}assets/icns/{$icon_basename}";
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();
        $title = $news_entry->getTitle();
        $teaser = $news_entry->getTeaser();
        $content = $news_entry->getContent();
        $link = "news/{$id}{$filter_arg}";

        $image_ids = $news_entry->getImageIds();

        // Show images in teaser
        $teaser = $this->imageUtils()->replaceImageTags(
            $teaser,
            $id,
            $image_ids,
            'image',
            " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
        );

        // Strip images and legacy files from content
        preg_match_all("/<(bild|dl)([0-9]+)>/i", $content, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $content = str_replace($matches[0][$i], '', $content);
        }

        // Show files in teaser
        preg_match_all("/<datei([0-9]+|\\=[0-9A-Za-z_\\-]{24}\\.\\S{1,10})(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $teaser, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $new_teaser = $matches[4][$i];
            $teaser = str_replace($matches[0][$i], $new_teaser, $teaser);
        }

        // Show files in content
        preg_match_all("/<datei([0-9]+|\\=[0-9A-Za-z_\\-]{24}\\.\\S{1,10})(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $content, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $new_content = $matches[4][$i];
            $content = str_replace($matches[0][$i], $new_content, $content);
        }

        $author_badge = OlzAuthorBadge::render([
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
            $json_id = json_encode(intval($id));
            $has_blog = $this->authUtils()->hasPermission('kaderblog', $user);
            $json_mode = htmlentities(json_encode($has_blog ? 'account_with_blog' : 'account'));
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
                'text' => $this->htmlUtils()->renderMarkdown($teaser, [
                    'html_input' => 'allow', // TODO: Do NOT allow!
                ]),
                'link' => $link,
            ]);
        } elseif ($format === 'kaderblog') {
            $thumb = '';
            $size = count($image_ids);
            if ($size > 0) {
                $thumb = $this->imageUtils()->olzImage(
                    'news',
                    $id,
                    $image_ids[0] ?? null,
                    110,
                    'image',
                    " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'",
                );
            }
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $thumb.$this->htmlUtils()->renderMarkdown(
                    self::truncateText($content),
                    [
                        'html_input' => 'allow', // TODO: Do NOT allow!
                    ],
                ),
                'link' => $link,
            ]);
        } elseif ($format === 'forum') {
            $thumb = '';
            $size = count($image_ids);
            if ($size > 0) {
                $thumb = $this->imageUtils()->olzImage(
                    'news',
                    $id,
                    $image_ids[0] ?? null,
                    110,
                    'image',
                    " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'",
                );
            }
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $published_date,
                'author' => $author_badge,
                'title' => $title.$edit_admin,
                'text' => $thumb.$this->htmlUtils()->renderMarkdown(
                    self::truncateText($content),
                    [
                        'html_input' => 'allow', // TODO: Do NOT allow!
                    ],
                ),
                'link' => $link,
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
                'text' => "<table><tr class='thumbs'>{$thumbs}</tr></table>",
                'link' => $link,
            ]);
        } elseif ($format === 'video') {
            $thumbnail = $this->imageUtils()->olzImage("news", $id, $image_ids[0] ?? null, 110, 'image');
            $content = <<<ZZZZZZZZZZ
            <div href='{$link}' style='background-color:#000;padding-top:0;' class='thumb paragraf'>\n
            <span style='display:block;background-image:url({$code_href}assets/icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            <span style='display:block;text-align:center;'>{$thumbnail}</span>\n
            <span style='display:block;background-image:url({$code_href}assets/icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
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

    protected static function truncateText($text) {
        $max_length = 300;

        $text = preg_replace("/\\s*\\n\\s*/", "\n", $text);
        $text_length = mb_strlen($text);
        $num_br = preg_match_all("/\\n/", $text, $tmp);
        if ($num_br < 3) {
            $text = str_replace("\n", "<br>", $text);
        } else {
            $text = str_replace("\n", " &nbsp; ", $text);
        }

        if ($text_length <= $max_length) {
            return $text;
        }
        $text = mb_substr($text, 0, $max_length - 6);
        $last_space = mb_strrpos($text, " ");
        $last_break = mb_strrpos($text, "<br>");
        $last_whitespace = ($last_break > $last_space) ? $last_break : $last_space;
        return mb_substr($text, 0, $last_whitespace).' [...]';
    }
}
