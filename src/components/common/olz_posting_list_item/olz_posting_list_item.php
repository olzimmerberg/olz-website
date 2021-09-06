<?php

function olz_posting_list_item($args = []): string {
    global $_DATE;
    $icon = $args['icon'] ?? "";
    $date = $args['date'] ?? "";
    $title = $args['title'] ?? "";
    $text = $args['text'] ?? "";
    $link = $args['link'] ?? "";

    $pretty_date = $_DATE->olzDate("tt.mm.jj", $date);

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
            <div class='text'>{$text}</div>
        </div>
    </div>
    ZZZZZZZZZZ;
}
