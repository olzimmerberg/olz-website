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

    /** @var array<string, false|string> */
    public array $last_occurrences = [];
    /** @var array<array{0: string, 1: \DateTime|string}> */
    public array $recorded_occurrences = [];

    public function getLastOccurrenceOf(string $event_name): ?\DateTime {
        $last_occurrence = $this->last_occurrences[$event_name] ?? null;
        if ($last_occurrence === null) {
            throw new \Exception("Fake throttling not set up for {$event_name}");
        }
        if ($last_occurrence === false) {
            return null;
        }
        return new \DateTime($last_occurrence);
    }

    public function recordOccurrenceOf(string $event_name, \DateTime|string $datetime): void {
        $this->recorded_occurrences[] = [$event_name, $datetime];
    }
}
