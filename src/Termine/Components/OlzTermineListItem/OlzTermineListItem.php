<?php

namespace Olz\Termine\Components\OlzTermineListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Utils\FileUtils;

class OlzTermineListItem extends OlzComponent {
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    public function getHtml($args = []): string {
        $file_utils = FileUtils::fromEnv();
        $code_href = $this->envUtils()->getCodeHref();

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

        $link = "{$code_href}termine/{$id}?filter={$enc_current_filter}";
        $type_imgs = implode('', array_map(function ($type) use ($code_href) {
            $icon_basename = self::$iconBasenameByType[$type] ?? '';
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            return "<img src='{$icon}' alt='' class='type-icon'>";
        }, $types));
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
                    <div class='title'>{$title} {$type_imgs}</div>
                    <div class='text'>{$text} {$links}</div>
                </div>
            </div>
        </div>
        ZZZZZZZZZZ;
        return $out;
    }

    protected function getTimeText($iso_time) {
        if (!$iso_time || $iso_time === '00:00:00') {
            return null;
        }
        return date("H:i", strtotime($iso_time));
    }
}
