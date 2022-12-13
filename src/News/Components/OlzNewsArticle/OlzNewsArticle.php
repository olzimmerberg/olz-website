<?php

namespace Olz\News\Components\OlzNewsArticle;

use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\ImageUtils;

class OlzNewsArticle {
    public static function render($args = []) {
        global $_DATE;

        $db = DbUtils::fromEnv()->getDb();
        $image_utils = ImageUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $file_utils = FileUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $db_table = 'aktuell';
        $id = $args['id'];
        // TODO: Remove once migrated
        $galerie_id = $id - 1200;
        $arg_row = $args['row'] ?? null;
        $can_edit = $args['can_edit'] ?? false;
        $is_preview = $args['is_preview'] ?? false;
        $out = "";

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
            $is_migrated = is_array($image_ids);

            $datum = $_DATE->olzDate("tt.mm.jj", $datum);

            $edit_admin = '';
            if ($can_edit && !$is_preview) {
                $json_id = json_encode(intval($id_tmp));
                $edit_admin = $is_migrated ? <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-news-button'
                        class='btn btn-primary'
                        onclick='return olz.editNewsArticle({$json_id})'
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
                ZZZZZZZZZZ : "<a href='aktuell.php?id={$id_tmp}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>";
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

            $out .= "<h2>{$edit_admin}{$titel}</h2>";

            if ($format === 'galerie') {
                $out .= "<div class='lightgallery gallery-container'>";
                if ($is_migrated) {
                    $size = count($image_ids);
                } else {
                    $img_path = "{$data_path}img/galerie/{$galerie_id}/img/";
                    for ($size = 1; is_file($img_path.str_pad($size, 3, '0', STR_PAD_LEFT).".jpg"); $size++) {
                    }
                    $size--;
                }
                for ($index = 0; $index < $size; $index++) {
                    $out .= "<div class='gallery-image'>";
                    if ($is_migrated) {
                        $out .= $image_utils->olzImage("news", $id, $image_ids[$index], 110, 'gallery[myset]');
                    } else {
                        $out .= $image_utils->olzImage("galerie", $galerie_id, $index + 1, 110, 'gallery[myset]');
                    }
                    $out .= "</div>";
                }
                $out .= "</div>\n";
            } elseif ($format === 'video') {
                $res0 = preg_match("/^https\\:\\/\\/(www\\.)?youtu\\.be\\/([a-zA-Z0-9]{6,})/", $textlang, $matches0);
                $res1 = preg_match("/^https\\:\\/\\/(www\\.)?youtube\\.com\\/watch\\?v\\=([a-zA-Z0-9]{6,})/", $textlang, $matches1);
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
