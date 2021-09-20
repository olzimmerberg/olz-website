<?php

function olz_news_list_item($args = []): string {
    global $code_href;

    require_once __DIR__.'/../../../components/common/olz_posting_list_item/olz_posting_list_item.php';

    $news_entry = $args['news_entry'];
    $out = "";

    $icon = "{$code_href}icns/entry_type_aktuell_20.svg";
    $datum = $news_entry->getDate();
    $title = $news_entry->getTitle();
    $text = $news_entry->getTeaser();
    $link = "?id=".$news_entry->getId();

    // Bildercode einfügen
    preg_match_all("/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i", $text, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $size = intval($matches[3][$i]);
        if ($size < 1) {
            $size = 110;
        }
        $new_html = olz_image(
            'aktuell',
            $news_entry->getId(),
            intval($matches[1][$i]),
            $size,
            null,
            " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'"
        );
        $text = str_replace($matches[0][$i], $new_html, $text);
    }

    // Dateicode einfügen
    preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        $new_text = $matches[4][$i];
        $text = str_replace($matches[0][$i], $new_text, $text);
    }

    $out .= olz_posting_list_item([
        'icon' => $icon,
        'date' => $datum,
        'title' => $title,
        'text' => $text,
        'link' => $link,
    ]);
    return $out;
}
