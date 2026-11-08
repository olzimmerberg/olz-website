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

    /** @return array<Termin> */
    public function findAll(): array {
        $class = $this->fakeOlzEntityClass;
        return [
            $class::default(),
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria == ['solv_uid' => 12, 'deleted_at' => null]) {
            return null;
        }
        if ($criteria == ['solv_uid' => 123, 'deleted_at' => null]) {
            return FakeTermin::empty();
        }
        if ($criteria == ['solv_uid' => 1234, 'deleted_at' => null]) {
            return FakeTermin::maximal();
        }
        return parent::findOneBy($criteria);
    }
}
