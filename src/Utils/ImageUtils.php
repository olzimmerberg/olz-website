<?php

namespace Olz\Utils;

class ImageUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
    ];

    public const TABLES_IMG_DIRS = [
        'aktuell' => 'img/aktuell/',
        'blog' => 'img/blog/',
        'forum' => null,
        'galerie' => 'img/galerie/',
        'kaderblog' => 'img/blog/',
        'news' => 'img/news/',
        'video' => 'img/galerie/',
        'weekly_picture' => 'img/weekly_picture/',
    ];

    public function replaceImageTags(
        $text,
        $id,
        $image_ids,
        $lightview = 'image',
        $attrs = '',
    ): string {
        $is_migrated = is_array($image_ids);
        $res = preg_match_all(
            '/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i', $text ?? '', $matches);
        if (!$res) {
            return $text ?? '';
        }
        for ($i = 0; $i < count($matches[0]); $i++) {
            $size = intval($matches[3][$i]);
            if ($size < 1) {
                $size = 110;
            }
            $index = intval($matches[1][$i]);
            if ($is_migrated) {
                $new_html = $this->olzImage(
                    'news',
                    $id,
                    $image_ids[$index - 1] ?? null,
                    $size,
                    $lightview,
                    $attrs
                );
            } else {
                // TODO: Delete this monster-logic!
                $is_blog = $id >= 6400 && $id < 6700;
                $new_html = $this->olzImage(
                    $is_blog ? 'blog' : 'aktuell',
                    $is_blog ? $id - 6400 : $id,
                    $index,
                    $size,
                    $lightview,
                    $attrs
                );
            }
            $text = str_replace($matches[0][$i], $new_html, $text);
        }
        return $text;
    }

    public function olzImage(
        $db_table,
        $id,
        $index,
        $dim,
        $lightview = 'image',
        $attrs = '',
    ): string {
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();
        if (!isset($this::TABLES_IMG_DIRS[$db_table])) {
            return "Ungültige db_table: {$db_table} (in olzImage)";
        }
        $db_imgpath = $this::TABLES_IMG_DIRS[$db_table];
        $is_migrated = !(is_numeric($index) && intval($index) > 0 && intval($index) == $index);
        if ($is_migrated) {
            $imgfile = "{$db_imgpath}/{$id}/img/{$index}";
        } else {
            $padded_index = str_pad(intval($index), 3, "0", STR_PAD_LEFT);
            $imgfile = "{$db_imgpath}/{$id}/img/{$padded_index}.jpg";
        }
        if (!is_file("{$data_path}{$imgfile}")) {
            return "Bild nicht vorhanden (in olzImage): {$imgfile}";
        }
        $info = getimagesize("{$data_path}{$imgfile}");
        $swid = $info[0];
        $shei = $info[1];
        if ($shei < $swid) {
            $wid = $dim;
            $hei = intval($wid * $shei / $swid);
        } else {
            $hei = $dim;
            $wid = intval($hei * $swid / $shei);
        }
        $span_before = $lightview == 'image' ? "<span class='lightgallery'>" : "";
        $span_after = $lightview == 'image' ? "</span>" : "";
        $a_before = $lightview ? "<a href='{$data_href}{$imgfile}' aria-label='Bild vergrössern' data-src='{$data_href}{$imgfile}' onclick='event.stopPropagation()'>" : "";
        $a_after = $lightview ? "</a>" : "";
        return "{$span_before}{$a_before}<img src='image_tools.php?request=thumb&db_table={$db_table}&id={$id}&index={$index}&dim={$dim}' alt='' width='{$wid}' height='{$hei}'{$attrs}>{$a_after}{$span_after}";
    }
}
