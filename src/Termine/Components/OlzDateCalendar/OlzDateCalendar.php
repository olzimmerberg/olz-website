<?php

namespace Olz\Termine\Components\OlzDateCalendar;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzDateCalendar extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $date = $args['date'] ?? null;
        $size = strtolower($args['size'] ?? 'M');

        $weekday = $args['weekday'] ?? $this->dateUtils()->olzDate("W", $date);
        $day = $args['day'] ?? $this->dateUtils()->olzDate("t", $date);
        $month = $args['month'] ?? $this->dateUtils()->olzDate("MM", $date);

        return <<<ZZZZZZZZZZ
            <div class='olz-date-calendar size-{$size}'>
                <img src='{$code_href}assets/icns/date_calendar.svg' alt='' class='date-img'>
                <div class='weekday'>{$weekday}</div>
                <div class='day'>{$day}</div>
                <div class='month'>{$month}</div>
            </div>
            ZZZZZZZZZZ;
    }
}
