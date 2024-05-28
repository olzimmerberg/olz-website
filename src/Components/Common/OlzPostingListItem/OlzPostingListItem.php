<?php

namespace Olz\Components\Common\OlzPostingListItem;

use Olz\Components\Common\OlzComponent;

class OlzPostingListItem extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $icon = $args['icon'] ?? "";
        $date = $args['date'] ?? "";
        $author = $args['author'] ?? "";
        $title = $args['title'] ?? "";
        $text = $args['text'] ?? "";
        $link = $args['link'] ?? "";
        $class = $args['class'] ?? "";

        $pretty_date = $this->dateUtils()->olzDate("tt.mm.jj", $date);

        return <<<ZZZZZZZZZZ
            <div class='olz-posting-list-item {$class}'>
                <a class='link' href='{$link}'></a>
                <div class='content'>
                    <span class='date title'>
                        {$pretty_date}
                    </span>
                    <div class='title'>
                        <img src='{$icon}' class='icon' alt='' />
                        {$title}
                    </div>
                    <div class='text'>{$author}{$text}</div>
                </div>
            </div>
            ZZZZZZZZZZ;
    }
}
