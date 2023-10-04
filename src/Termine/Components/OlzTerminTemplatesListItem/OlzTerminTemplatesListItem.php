<?php

namespace Olz\Termine\Components\OlzTerminTemplatesListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;
use Olz\Utils\FileUtils;

class OlzTerminTemplatesListItem extends OlzComponent {
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    public function getHtml($args = []): string {
        $db = $this->dbUtils()->getDb();
        $file_utils = FileUtils::fromEnv();
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $enc_current_filter = urlencode($_GET['filter'] ?? '{}');

        $termin_template = $args['termin_template'];
        $id = $termin_template->getId();
        $start_time = $termin_template->getStartTime();
        $duration_seconds = $termin_template->getDurationSeconds();
        $title = $termin_template->getTitle();
        $text = $termin_template->getText();
        $links = $termin_template->getLink();
        $types = explode(' ', $termin_template->getTypes());
        $termin_location = $termin_template->getLocation();

        $link = "{$code_href}termine/vorlagen/{$id}?filter={$enc_current_filter}";
        $type_imgs = implode('', array_map(function ($type) use ($code_href) {
            $icon_basename = self::$iconBasenameByType[$type] ?? '';
            $icon = "{$code_href}assets/icns/{$icon_basename}";
            return "<img src='{$icon}' alt='' class='type-icon'>";
        }, $types));

        $duration_interval = \DateInterval::createFromDateString("+{$duration_seconds} seconds");
        $end_time = (clone $start_time)->add($duration_interval);
        $end_time_text = $this->getTimeText($end_time);
        $day_diff = intval($end_time->format('d')) - intval($start_time->format('d'));
        $start_icon = OlzDateCalendar::render([
            'weekday' => '',
            'day' => 'X',
            'month' => '',
            'size' => 'S',
        ]);
        $end_icon = ($day_diff > 0)
            ? ' &ndash; '.OlzDateCalendar::render([
                'weekday' => '',
                'day' => "X+{$day_diff}",
                'month' => '',
                'size' => 'S',
            ])
            : null;
        $start_time_text = $this->getTimeText($start_time);

        $time_text = $start_time_text ? (
            $end_time_text
                ? "{$start_time_text} &ndash; {$end_time_text}"
                : "{$start_time_text}"
        ) : null;
        $text = $this->htmlUtils()->renderMarkdown($text ?? '');
        $links = $file_utils->replaceFileTags($links, 'termin_templates', $id);
        if ($termin_location) {
            $sane_termin_location_id = intval($termin_location->getId());
            $result_location = $db->query("SELECT name FROM termin_locations WHERE id='{$sane_termin_location_id}'");
            $row_location = $result_location->fetch_assoc();
            $location_name = $row_location['name'];
            $links = "<a href='{$code_href}termine/orte/{$sane_termin_location_id}?filter={$enc_current_filter}' class='linkmap'>{$location_name}</a> {$links}";
        }

        $out .= <<<ZZZZZZZZZZ
        <div class='olz-termin-templates-list-item'>
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

    protected function getTimeText($datetime_time) {
        if (!$datetime_time) {
            return null;
        }
        return $datetime_time->format('H:i');
    }
}
