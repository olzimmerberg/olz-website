<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Throttling;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Throttling>
 */
class FakeThrottlingRepository extends FakeOlzRepository {
    public string $olzEntityClass = Throttling::class;

    public ?string $expected_event_name = null;
    public ?string $last_daily_notifications = '2020-03-12 19:30:00';
    /** @var array<array{0: string, 1: \DateTime|string}> */
    public array $recorded_occurrences = [];

    public function getLastOccurrenceOf(string $event_name): ?\DateTime {
        if ($event_name == $this->expected_event_name) {
            if (!$this->last_daily_notifications) {
                return null;
            }
            return new \DateTime($this->last_daily_notifications);
        }
        throw new \Exception("this should never happen");
    }

    public function recordOccurrenceOf(string $event_name, \DateTime|string $datetime): void {
        $this->recorded_occurrences[] = [$event_name, $datetime];
    }
}
