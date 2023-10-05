<?php

namespace Olz\Utils;

class ImageUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'log',
    ];

    public const TABLES_IMG_DIRS = [
        'aktuell' => 'img/aktuell/',
        'blog' => 'img/blog/',
        'forum' => null,
        'galerie' => 'img/galerie/',
        'kaderblog' => 'img/blog/',
        'news' => 'img/news/',
        'termine' => 'img/termine/',
        'termin_locations' => 'img/termin_locations/',
        'termin_templates' => 'img/termin_templates/',
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
            $new_html = $this->olzImage(
                'news',
                $id,
                $image_ids[$index - 1] ?? null,
                $size,
                $lightview,
                $attrs
            );
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
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();
        if (!isset($this::TABLES_IMG_DIRS[$db_table])) {
            $message = "Ungültige db_table: {$db_table} (in olzImage)";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
        }
        $db_imgpath = $this::TABLES_IMG_DIRS[$db_table];
        $imgfile = "{$db_imgpath}/{$id}/img/{$index}";
        if (!is_file("{$data_path}{$imgfile}")) {
            $message = "Bild nicht vorhanden (in olzImage): {$imgfile}";
            $this->log()->error($message);
            return "<span style='color:#ff0000; font-style:italic;'>{$message}</span>";
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

        $url_without_dim = "{$code_href}image_tools.php?request=thumb&db_table={$db_table}&id={$id}&index={$index}";
        $dim2x = $dim * 2;
        return <<<ZZZZZZZZZZ
        {$span_before}{$a_before}
        <img
            src='{$url_without_dim}&dim={$dim}'
            srcset='{$url_without_dim}&dim={$dim2x} 2x, {$url_without_dim}&dim={$dim} 1x'
            alt=''
            width='{$wid}'
            height='{$hei}'
            {$attrs}
        />
        {$a_after}{$span_after}
        ZZZZZZZZZZ;
    }
}
