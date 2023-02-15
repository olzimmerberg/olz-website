<?php

namespace Olz\News\Components\OlzNewsListItem;

use Olz\Components\Common\OlzAuthorBadge\OlzAuthorBadge;
use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\Utils\EnvUtils;
use Olz\Utils\ImageUtils;

class OlzNewsListItem {
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'forum' => 'entry_type_forum_20.svg',
        'galerie' => 'entry_type_gallery_20.svg',
        'movie' => 'entry_type_movie_20.svg',
        'video' => 'entry_type_movie_20.svg',
    ];

    public static function render($args = []) {
        $env_utils = EnvUtils::fromEnv();
        $image_utils = ImageUtils::fromEnv();

        $code_href = $env_utils->getCodeHref();
        $data_path = $env_utils->getDataPath();

        $news_entry = $args['news_entry'];
        $out = "";

        $id = $news_entry->getId();
        // TODO: Remove once migrated
        $galerie_id = $id - 1200;
        $datum = $news_entry->getDate();
        $format = $news_entry->getFormat();
        $icon_basename = self::$iconBasenameByFormat[$format];
        $icon = "{$code_href}icns/{$icon_basename}";
        $author_user = $news_entry->getAuthorUser();
        $author_role = $news_entry->getAuthorRole();
        $author_name = $news_entry->getAuthorName();
        $author_email = $news_entry->getAuthorEmail();
        $title = $news_entry->getTitle();
        $text = $news_entry->getTeaser();
        $content = $news_entry->getContent();
        $link = "aktuell.php?id=".$id;

        $image_ids = $news_entry->getImageIds();
        $is_migrated = is_array($image_ids);

        // Bildercode einfügen
        $text = $image_utils->replaceImageTags(
            $text,
            $id,
            $image_ids,
            'image',
            " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
        );

        // Dateicode einfügen
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $new_text = $matches[4][$i];
            $text = str_replace($matches[0][$i], $new_text, $text);
        }

        $author_badge = OlzAuthorBadge::render([
            'user' => $author_user,
            'role' => $author_role,
            'name' => $author_name,
            'email' => $author_email,
        ]);

        if ($format === 'aktuell') {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $datum,
                'author' => $author_badge,
                'title' => $title,
                'text' => $text,
                'link' => $link,
            ]);
        } elseif ($format === 'forum') {
            $thumb = '';
            if ($is_migrated) {
                $size = count($image_ids);
                if ($size > 0) {
                    $thumb = $image_utils->olzImage(
                        'news',
                        $id,
                        $image_ids[0] ?? null,
                        110,
                        'image',
                        " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'",
                    );
                }
            }
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $datum,
                'author' => $author_badge,
                'title' => $title,
                'text' => $thumb.self::truncateText($content),
                'link' => $link,
            ]);
        } elseif ($format === 'galerie') {
            $thumbs = '';
            $used_thumb_indexes = [];
            if ($is_migrated) {
                $size = count($image_ids);
            } else {
                $img_path = "{$data_path}img/galerie/{$galerie_id}/img/";
                for ($size = 1; is_file($img_path.str_pad($size, 3, '0', STR_PAD_LEFT).".jpg"); $size++) {
                }
                $size--;
            }
            for ($i = 0; $i < (($size > 4) ? 4 : $size); $i++) {
                $random_index = rand(1, $size);
                while (array_search($random_index, $used_thumb_indexes) !== false) {
                    $random_index = rand(1, $size);
                }
                array_push($used_thumb_indexes, $random_index);
                if ($is_migrated) {
                    $thumbs .= "<td class='test-flaky'>".$image_utils->olzImage("news", $id, $image_ids[$random_index - 1], 110, 'image')."</td>";
                } else {
                    $thumbs .= "<td class='test-flaky'>".$image_utils->olzImage("galerie", $galerie_id, $random_index, 110, 'image')."</td>";
                }
            }
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $datum,
                'author' => $author_badge,
                'title' => $title,
                'text' => "<table><tr class='thumbs'>{$thumbs}</tr></table>",
                'link' => $link,
            ]);
        } elseif ($format === 'video') {
            if ($is_migrated) {
                $thumbnail = $image_utils->olzImage("news", $id, $image_ids[0] ?? null, 110, 'image');
            } else {
                $thumbnail = $image_utils->olzImage("galerie", $galerie_id, 1, 110, 'image');
            }
            $content = <<<ZZZZZZZZZZ
            <div href='{$link}' style='background-color:#000;padding-top:0;' class='thumb paragraf'>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            <span style='display:block;text-align:center;'>{$thumbnail}</span>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            </div>
            ZZZZZZZZZZ;
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $datum,
                'author' => $author_badge,
                'title' => $title,
                'text' => $content,
                'link' => $link,
            ]);
        } else {
            $out .= OlzPostingListItem::render([
                'icon' => $icon,
                'date' => $datum,
                'author' => $author_badge,
                'title' => $title,
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
