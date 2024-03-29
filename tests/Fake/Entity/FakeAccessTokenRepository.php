<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeAccessTokenRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeAccessToken::class;

    public function findOneBy($where) {
        if ($where['purpose'] ?? null === 'WebDAV') {
            if ($where['user']?->getId() === 1) {
                return null;
            }
            if ($where['user']?->getId() === 2) {
                return FakeAccessToken::default();
            }
        }
        if ($where === ['token' => 'valid-token']) {
            return FakeAccessToken::valid();
        }
        if ($where === ['token' => 'expired-token']) {
            return FakeAccessToken::expired();
        }
        if ($where === ['token' => 'invalid-token']) {
            return null;
        }
        return parent::findOneBy($where);
    }
}
