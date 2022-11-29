<?php

namespace Olz\Components\Common\OlzPostingListItem;

class OlzPostingListItem {
    public static function render($args = []) {
        global $_DATE;
        $icon = $args['icon'] ?? "";
        $date = $args['date'] ?? "";
        $author = $args['author'] ?? null;
        $title = $args['title'] ?? "";
        $text = $args['text'] ?? "";
        $link = $args['link'] ?? "";

        $pretty_date = $_DATE->olzDate("tt.mm.jj", $date);
        $pretty_author = $author ? "<span class='author'>{$author}</span> " : "";

        return <<<ZZZZZZZZZZ
        <div class='olz-posting-list-item'>
            <a class='link' href='{$link}'></a>
            <div class='content'>
                <span class='date title'>
                    {$pretty_date}
                </span>
                <div class='title'>
                    <img src='{$icon}' class='icon' alt='' />
                    {$title}
                </div>
                <div class='text'>{$pretty_author}{$text}</div>
            </div>
        </div>
        ZZZZZZZZZZ;
    }
}
