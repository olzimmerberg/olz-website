<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Counter;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Counter>
 */
class FakeCounterRepository extends FakeOlzRepository {
    public string $olzEntityClass = Counter::class;
    public string $fakeOlzEntityClass = FakeCounter::class;

    /** @var array<mixed> */
    public array $visit_records = [];

    /** @var array<mixed> */
    public array $latency_records = [];

    public function recordVisit(string $page): void {
        $this->visit_records[] = $page;
    }

    public function recordLatency(string $page, float $latency_ms): void {
        $this->latency_records[] = [$page, $latency_ms];
    }
}
