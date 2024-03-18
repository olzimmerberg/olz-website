<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeSolvEventRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeSolvEvent::class;

    public $eventWithResults;
    public $eventWithoutResults;
    public $updatedRankIdBySolvUid = [];
    public $deletedBySolvUid = [];

    public function getSolvEventsForYear($year) {
        switch ($year) {
            case '2020':
                return [
                    FakeSolvEvent::withResults(),
                    FakeSolvEvent::withoutResults(),
                ];
            default:
                return [];
        }
    }

    public function setResultForSolvEvent($solv_uid, $rank_id) {
        $this->updatedRankIdBySolvUid[$solv_uid] = $rank_id;
    }

    public function deleteBySolvUid($solv_uid) {
        $this->deletedBySolvUid[] = $solv_uid;
    }
}
