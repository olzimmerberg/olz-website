<?php

namespace Olz\News\Components\OlzNewsArticle;

use Olz\Utils\DbUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\ImageUtils;

class OlzNewsArticle {
    public static function render($args = []) {
        global $_DATE;

        $db = DbUtils::fromEnv()->getDb();
        $image_utils = ImageUtils::fromEnv();
        $file_utils = FileUtils::fromEnv();
        $db_table = 'aktuell';
        $id = $args['id'];
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
                        onclick='return editNewsArticle({$json_id})'
                    >
                        <img src='icns/edit_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                    <button
                        id='delete-news-button'
                        class='btn btn-danger'
                        onclick='return deleteNewsArticle({$json_id})'
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

            $out .= "<h2>".$edit_admin.$titel." (".$datum."/".$autor.")</h2>";
            $out .= "<div class='lightgallery'><p><b>".$text."</b><p>".$textlang."</p></div>\n";
        }
        return $out;
    }
}
