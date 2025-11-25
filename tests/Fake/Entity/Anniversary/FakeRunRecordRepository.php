<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anniversary;

use Olz\Entity\Anniversary\RunRecord;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<RunRecord>
 */
class FakeRunRecordRepository extends FakeOlzRepository {
    public string $olzEntityClass = RunRecord::class;
    public string $fakeOlzEntityClass = FakeRunRecord::class;
}
