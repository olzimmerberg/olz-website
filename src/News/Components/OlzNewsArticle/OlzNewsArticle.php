<?php

namespace Olz\News\Components\OlzNewsArticle;

use Olz\Components\Common\OlzComponent;
use Olz\Utils\FileUtils;
use Olz\Utils\HtmlUtils;
use Olz\Utils\ImageUtils;

class OlzNewsArticle extends OlzComponent {
    public function getHtml($args = []): string {
        global $_DATE;

        $db = $this->dbUtils()->getDb();
        $image_utils = ImageUtils::fromEnv();
        $file_utils = FileUtils::fromEnv();
        $html_utils = HtmlUtils::fromEnv();

        $data_path = $this->envUtils()->getDataPath();
        $db_table = 'aktuell';
        $id = $args['id'];
        // TODO: Remove once migrated
        $galerie_id = $id - 1200;
        $arg_row = $args['row'] ?? null;
        $is_preview = $args['is_preview'] ?? false;
        $out = "";
        $user = $this->authUtils()->getCurrentUser();

        $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = mysqli_fetch_array($result);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row["datum"]));
        }
        $jahr = $_SESSION[$db_table.'jahr_'];

        $result = $db->query($sql);

        // Aktuelle Nachricht
        while ($row = mysqli_fetch_array($result)) {
            if ($is_preview) {
                $row = $arg_row;
            } else {
                $id_tmp = intval($row['id']);
                $db->query("UPDATE `aktuell` SET `counter`=`counter` + 1 WHERE `id`='{$id_tmp}'");
            }
            if (!$row) {
                continue;
            }
            $id_tmp = $row['id'];
            $format = $row['typ'];
            $titel = $row['titel'];
            $text = olz_amp($row['text']);
            $textlang = olz_br($row['textlang']);
            // $textlang = str_replace(array("\n\n","\n"),array("<p>","<br>"),$row['textlang']);
            $autor = ($row['autor'] > '') ? $row['autor'] : "..";
            $datum = $row['datum'];

            $image_ids = json_decode($row['image_ids'] ?? 'null', true);

            $datum = $_DATE->olzDate("tt.mm.jj", $datum);

            $can_edit = $user && intval($row['owner_user_id']) === intval($user->getId());
            $edit_admin = '';
            if ($can_edit && !$is_preview) {
                $json_id = json_encode(intval($id_tmp));
                $has_blog = $this->authUtils()->hasPermission('kaderblog', $user);
                $json_mode = htmlentities(json_encode($has_blog ? 'account_with_blog' : 'account'));
                $edit_admin = <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-news-button'
                        class='btn btn-primary'
                        onclick='return olz.editNewsArticle({$json_id}, {$json_mode})'
                    >
                        <img src='icns/edit_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                    <button
                        id='delete-news-button'
                        class='btn btn-danger'
                        onclick='return olz.deleteNewsArticle({$json_id})'
                    >
                        <img src='icns/delete_white_16.svg' class='noborder' />
                        Löschen
                    </button>
                </div>
                ZZZZZZZZZZ;
            }

            // Bildercode einfügen
            if ($is_preview) {
                $text = $image_utils->replaceImageTags(
                    $text,
                    $id,
                    $image_ids,
                    "gallery[myset]",
                    " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
                );
            } else {
                preg_match_all('/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i', $text, $matches);
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $text = str_replace($matches[0][$i], '', $text);
                }
            }
            $textlang = $image_utils->replaceImageTags(
                $textlang,
                $id,
                $image_ids,
                "gallery[myset]",
                " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
            );

            // Dateicode einfügen
            $text = $file_utils->replaceFileTags($text, 'aktuell', $id);
            $textlang = $file_utils->replaceFileTags($textlang, 'aktuell', $id);

            // Markdown
            $text = $html_utils->renderMarkdown($text, [
                'html_input' => 'allow', // TODO: Do NOT allow!
            ]);
            $textlang = $html_utils->renderMarkdown($textlang, [
                'html_input' => 'allow', // TODO: Do NOT allow!
            ]);

            $out .= "<h2>{$edit_admin}{$titel}</h2>";

            if ($format === 'aktuell') {
                $out .= "<div class='lightgallery'><p><b>{$text}</b><p>{$textlang}</p></div>\n";
            } elseif ($format === 'kaderblog') {
                $gallery = '';
                $num_images = count($image_ids);
                if ($num_images > 0) {
                    $gallery .= "<br/><br/><div class='lightgallery gallery-container'>";
                    foreach ($image_ids as $image_id) {
                        $gallery .= "<div class='gallery-image'>";
                        $gallery .= $image_utils->olzImage(
                            'news', $id, $image_id, 110, 'gallery[myset]');
                        $gallery .= "</div>";
                    }
                    $gallery .= "</div>";
                }
                $out .= "<p>{$textlang}</p>{$gallery}\n";
            } elseif ($format === 'forum') {
                $gallery = '';
                $num_images = count($image_ids);
                if ($num_images > 0) {
                    $gallery .= "<br/><br/><div class='lightgallery gallery-container'>";
                    foreach ($image_ids as $image_id) {
                        $gallery .= "<div class='gallery-image'>";
                        $gallery .= $image_utils->olzImage(
                            'news', $id, $image_id, 110, 'gallery[myset]');
                        $gallery .= "</div>";
                    }
                    $gallery .= "</div>";
                }
                $out .= "<p><b>{$text}</b><p>{$textlang}</p>{$gallery}\n";
            } elseif ($format === 'galerie') {
                $out .= "<div class='lightgallery gallery-container'>";
                $size = count($image_ids);
                for ($index = 0; $index < $size; $index++) {
                    $out .= "<div class='gallery-image'>";
                    $out .= $image_utils->olzImage("news", $id, $image_ids[$index], 110, 'gallery[myset]');
                    $out .= "</div>";
                }
                $out .= "</div>\n";
            } elseif ($format === 'video') {
                $youtube_url = $row['textlang'];
                $res0 = preg_match("/^https\\:\\/\\/(www\\.)?youtu\\.be\\/([a-zA-Z0-9]{6,})/", $youtube_url, $matches0);
                $res1 = preg_match("/^https\\:\\/\\/(www\\.)?youtube\\.com\\/watch\\?v\\=([a-zA-Z0-9]{6,})/", $youtube_url, $matches1);
                $youtube_match = null;
                if ($res0) {
                    $youtube_match = $matches0[2];
                }
                if ($res1) {
                    $youtube_match = $matches1[2];
                }

                $content_to_show = $youtube_match ? "<a href='{$textlang}'>Link zu YouTube, falls das Video nicht abgespielt werden kann</a>" : $textlang;
                $out .= "<div class='video-container'>";
                $out .= "<div style='background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>\n";
                if ($youtube_match != null) {
                    $out .= "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$youtube_match}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
                } else {
                    $this->log()->error("Invalid YouTube link (ID:{$id}): {$youtube_url}");
                    $out .= "Fehlerhafter YouTube-Link!";
                }
                $out .= "<div style='background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;margin:0px;padding:0px;height:24px;'></div>";
                $out .= "</div>";
            } else {
                $out .= "<div class='lightgallery'><p><b>{$text}</b><p>{$textlang}</p></div>\n";
            }
        }
        return $out;
    }
}
