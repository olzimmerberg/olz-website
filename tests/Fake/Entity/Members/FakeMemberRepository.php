<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Members;

use Olz\Entity\Members\Member;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Member>
 */
class FakeMemberRepository extends FakeOlzRepository {
    public string $olzEntityClass = Member::class;
    public string $fakeOlzEntityClass = FakeMember::class;

    /** @var array<string> */
    public array $activeIdents = ['10000012', '10001234'];
    /** @var array<string> */
    public array $deletedIdents = [];

    /** @return array<string> */
    public function getActiveIdents(): array {
        return $this->activeIdents;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['ident' => '10000012']) {
            return $this->isDeleted('10000012') ? FakeMember::empty() : FakeMember::minimal();
        }
        if ($criteria === ['ident' => '10000123']) {
            return null;
        }
        if ($criteria === ['ident' => '10001234']) {
            return $this->isDeleted('10001234') ? FakeMember::empty() : FakeMember::maximal();
        }
        return parent::findOneBy($criteria, $orderBy);
    }

    protected function isDeleted(string $ident): bool {
        return in_array($ident, $this->deletedIdents, true);
    }
}
