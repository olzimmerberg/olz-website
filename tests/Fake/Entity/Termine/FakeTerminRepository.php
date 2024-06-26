<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Termin>
 */
class FakeTerminRepository extends FakeOlzRepository {
    public string $olzEntityClass = Termin::class;
    public string $fakeOlzEntityClass = FakeTermin::class;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria == ['solv_uid' => 12, 'on_off' => 1]) {
            return null;
        }
        if ($criteria == ['solv_uid' => 123, 'on_off' => 1]) {
            return FakeTermin::empty();
        }
        if ($criteria == ['solv_uid' => 1234, 'on_off' => 1]) {
            return FakeTermin::maximal();
        }
        return parent::findOneBy($criteria);
    }
}
