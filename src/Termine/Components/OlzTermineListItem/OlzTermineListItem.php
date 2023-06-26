<?php

namespace Olz\Termine\Components\OlzTermineListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Utils\FileUtils;
use Olz\Utils\HtmlUtils;

class OlzTermineListItem extends OlzComponent {
    public function getHtml($args = []): string {
        $html_utils = HtmlUtils::fromEnv();
        $file_utils = FileUtils::fromEnv();

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();

        $out = '';
        $enc_current_filter = urlencode($_GET['filter'] ?? '{}');

        $item_type = $args['item_type'];
        $id = $args['id'];
        $start_date = $args['start_date'];
        $start_time = $args['start_time'];
        $end_date = $args['end_date'];
        $end_time = $args['end_time'];
        $title = $args['title'];
        $text = $args['text'];
        $links = $args['link'];
        $solv_uid = $args['solv_uid'];
        $types = $args['types'];
        $image_ids = $args['image_ids'];

        $link = "termine.php?filter={$enc_current_filter}&id={$id}";
        $start_icon = OlzDateCalendar::render([
            'date' => $start_date,
            'size' => 'S',
        ]);
        $end_icon = ($end_date && $end_date !== $start_date)
            ? ' &ndash; '.OlzDateCalendar::render([
                'date' => $end_date,
                'size' => 'S',
            ])
            : null;
        $start_time_text = $this->getTimeText($start_time);
        $end_time_text = $this->getTimeText($end_time);
        $time_text = $start_time_text ? (
            $end_time_text
                ? "{$start_time_text} &ndash; {$end_time_text}"
                : "{$start_time_text}"
        ) : null;
        $links = $file_utils->replaceFileTags($links, 'termine', $id);

        $out .= <<<ZZZZZZZZZZ
        <div class='olz-termine-list-item'>
            <a class='link' href='{$link}'></a>
            <div class='content'>
                <div class='date'>
                    <div class='date-calendars'>{$start_icon}{$end_icon}</div>
                    <div class='time-text'>{$time_text}</div>
                </div>
                <div class='title-text-container'>
                    <div class='title'>{$title}</div>
                    <div class='text'>{$text} {$links}</div>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
        // OlzPostingListItem::render([
        //     'icon' => $icon,
        //     'date' => $start_date,
        //     'author' => null,
        //     'title' => $title,
        //     'link' => $link,
        // ]);
        return $out;
    }

    protected function getTimeText($iso_time) {
        if (!$iso_time || $iso_time === '00:00:00') {
            return null;
        }
        return date("H:i", strtotime($iso_time));
    }
}
