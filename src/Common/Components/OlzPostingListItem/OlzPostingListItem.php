<?php

namespace Olz\Common\Components\OlzPostingListItem;

use Olz\Common\Components\OlzComponent;

/**
 * @extends OlzComponent<array{
 *   meta?: ?string,
 *   date?: ?string,
 *   author?: ?string,
 *   title?: ?string,
 *   text?: ?string,
 *   link?: ?string,
 *   class?: ?string,
 * }> */
class OlzPostingListItem extends OlzComponent {
    public function getHtml(mixed $args): string {
        $meta = $args['meta'] ?? '';
        $date = $args['date'] ?? '';
        $author = $args['author'] ?? "";
        $title = $args['title'] ?? "";
        $text = $args['text'] ?? "";
        $link = $args['link'] ?? "";
        $class = $args['class'] ?? "";

        return <<<ZZZZZZZZZZ
            <div class='olz-posting-list-item {$class}'>
                <a class='link' href='{$link}'></a>
                <div class='content'>
                    <div class='meta'>
                        {$meta}
                    </div>
                    <div class='date-title-container'>
                        <div class='title'>
                            {$title}
                        </div>
                        <span class='date'>
                            {$date}
                        </span>
                    </div>
                    <div class='text'>{$author}{$text}</div>
                </div>
            </div>
            ZZZZZZZZZZ;
    }
}
