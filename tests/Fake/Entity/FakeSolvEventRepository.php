<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<SolvEvent>
 */
class FakeSolvEventRepository extends FakeOlzRepository {
    public string $olzEntityClass = SolvEvent::class;
    public string $fakeOlzEntityClass = FakeSolvEvent::class;

    /** @var array<int, string> */
    public array $updatedRankIdBySolvUid = [];
    /** @var array<int> */
    public array $deletedBySolvUid = [];

    /** @return array<SolvEvent> */
    public function getSolvEventsForYear(int|string $year): array {
        switch ($year) {
            case 2020:
            case '2020':
                return [
                    FakeSolvEvent::withResults(),
                    FakeSolvEvent::withoutResults(),
                ];
            default:
                return [];
        }
    }

    public function setResultForSolvEvent(int $solv_uid, string $rank_id): void {
        $this->updatedRankIdBySolvUid[$solv_uid] = $rank_id;
    }

    public function deleteBySolvUid(int $solv_uid): void {
        $this->deletedBySolvUid[] = $solv_uid;
    }
}
