<?php

namespace Olz\Termine\Components\OlzTerminTemplatesListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Termine\TerminLabel;
use Olz\Termine\Components\OlzDateCalendar\OlzDateCalendar;

/** @extends OlzComponent<array<string, mixed>> */
class OlzTerminTemplatesListItem extends OlzComponent {
    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();

        $out = '';

        $termin_template = $args['termin_template'];
        $id = $termin_template->getId();
        $start_time = $termin_template->getStartTime();
        $duration_seconds = $termin_template->getDurationSeconds();
        $title = $termin_template->getTitle();
        $text = $termin_template->getText();
        $labels = [...$termin_template->getLabels()];
        $termin_location = $termin_template->getLocation();

        $link = "{$code_href}termine/vorlagen/{$id}";
        $type_imgs = implode('', array_map(function (TerminLabel $label) use ($code_path, $code_href) {
            $ident = $label->getIdent();
            // TODO: Remove fallback mechanism?
            $fallback_path = "{$code_path}assets/icns/termine_type_{$ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$ident}_20.svg" : null;
            $icon_href = $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            return $icon_href ? "<img src='{$icon_href}' alt='' class='type-icon'>" : '';
        }, $labels));

        $duration_seconds_or_zero = $duration_seconds ?? 0;
        $duration_string = "+{$duration_seconds_or_zero} seconds";
        $duration_interval = \DateInterval::createFromDateString($duration_string);
        $this->generalUtils()->checkNotFalse($duration_interval, "Invalid duration: {$duration_string}");
        $start_time_or_midnight = new \DateTime($start_time?->format('Y-m-d H:i:s') ?? '00:00:00');
        $end_time = $duration_seconds
            ? $start_time_or_midnight->add($duration_interval) : null;
        $end_time_text = $this->getTimeText($end_time);
        $day_diff = ($start_time && $end_time)
            ? intval($end_time->format('d')) - intval($start_time->format('d')) : null;
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
        ) : (
            $end_time_text
                ? "? +{$end_time_text}"
                : ''
        );
        $text = $this->htmlUtils()->renderMarkdown($text ?? '');
        $text = $termin_template->replaceImagePaths($text);
        $text = $termin_template->replaceFilePaths($text);
        if ($termin_location) {
            $sane_termin_location_id = intval($termin_location->getId());
            $result_location = $db->query("SELECT name FROM termin_locations WHERE id='{$sane_termin_location_id}'");
            // @phpstan-ignore-next-line
            $row_location = $result_location->fetch_assoc();
            $location_name = $row_location['name'] ?? null;
            $text = "<a href='{$code_href}termine/orte/{$sane_termin_location_id}' class='linkmap'>{$location_name}</a> {$text}";
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
                        <div class='text'>{$text}</div>
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;
        return $out;
    }

    protected function getTimeText(?\DateTime $datetime_time): ?string {
        if (!$datetime_time) {
            return null;
        }
        return $datetime_time->format('H:i');
    }
}
