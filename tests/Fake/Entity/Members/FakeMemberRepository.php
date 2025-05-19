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

    /** @return array<string> */
    public function getAllIdents(): array {
        return ['10000012', '10001234'];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['ident' => '10000012']) {
            return FakeMember::minimal();
        }
        if ($criteria === ['ident' => '10000123']) {
            return null;
        }
        if ($criteria === ['ident' => '10001234']) {
            return FakeMember::maximal();
        }
        return parent::findOneBy($criteria, $orderBy);
    }
}
