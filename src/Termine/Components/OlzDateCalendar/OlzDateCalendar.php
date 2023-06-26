<?php

namespace Olz\Termine\Components\OlzDateCalendar;

use Olz\Components\Common\OlzComponent;

class OlzDateCalendar extends OlzComponent {
    public function getHtml($args = []): string {
        $data_href = $this->envUtils()->getDataHref();

        $date = $args['date'];
        $size = strtolower($args['size'] ?? 'M');

        $weekday = $this->dateUtils()->olzDate("W", $date);
        $day = $this->dateUtils()->olzDate("t", $date);
        $month = $this->dateUtils()->olzDate("MM", $date);

        return <<<ZZZZZZZZZZ
        <div class='olz-date-calendar size-{$size}'>
            <img src='{$data_href}assets/icns/date_calendar.svg' alt='' class='date-img'>
            <div class='weekday'>{$weekday}</div>
            <div class='day'>{$day}</div>
            <div class='month'>{$month}</div>
        </div>
        ZZZZZZZZZZ;
    }
}
