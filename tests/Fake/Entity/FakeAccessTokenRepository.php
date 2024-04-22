<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeAccessTokenRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeAccessToken::class;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria['purpose'] ?? null === 'WebDAV') {
            if ($criteria['user']?->getId() === 1) {
                return null;
            }
            if ($criteria['user']?->getId() === 2) {
                return FakeAccessToken::default();
            }
        }
        if ($criteria === ['token' => 'valid-token']) {
            return FakeAccessToken::valid();
        }
        if ($criteria === ['token' => 'expired-token']) {
            return FakeAccessToken::expired();
        }
        if ($criteria === ['token' => 'invalid-token']) {
            return null;
        }
        return parent::findOneBy($criteria);
    }
}
