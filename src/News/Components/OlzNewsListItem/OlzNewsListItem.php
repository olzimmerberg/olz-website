<?php

namespace Olz\News\Components\OlzNewsListItem;

use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;

class OlzNewsListItem {
    public static function render($args = []) {
        global $code_href;

        $news_entry = $args['news_entry'];
        $out = "";

        $icon = "{$code_href}icns/entry_type_aktuell_20.svg";
        $datum = $news_entry->getDate();
        $title = $news_entry->getTitle();
        $text = $news_entry->getTeaser();
        $link = "aktuell.php?id=".$news_entry->getId();

        $image_ids = $news_entry->getImageIds();
        $is_migrated = (bool) $image_ids;

        // Bildercode einfügen
        $text = replace_image_tags(
            $text,
            $news_entry->getId(),
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

        $out .= OlzPostingListItem::render([
            'icon' => $icon,
            'date' => $datum,
            'title' => $title,
            'text' => $text,
            'link' => $link,
        ]);
        return $out;
    }
}
