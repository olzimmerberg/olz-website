<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeThrottlingRepository {
    public $expected_event_name;
    public $last_daily_notifications = '2020-03-12 19:30:00';
    public $recorded_occurrences = [];

    public function getLastOccurrenceOf($event_name) {
        if ($event_name == $this->expected_event_name) {
            if (!$this->last_daily_notifications) {
                return null;
            }
            return new \DateTime($this->last_daily_notifications);
        }
        throw new \Exception("this should never happen");
    }

    public function recordOccurrenceOf($event_name, $datetime) {
        $this->recorded_occurrences[] = [$event_name, $datetime];
    }
}
