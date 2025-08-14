<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AccessToken;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<AccessToken>
 */
class FakeAccessTokenRepository extends FakeOlzRepository {
    public string $olzEntityClass = AccessToken::class;
    public string $fakeOlzEntityClass = FakeAccessToken::class;

    /** @return array<AccessToken> */
    public function findAll(): array {
        return [
            FakeAccessToken::default(),
            FakeAccessToken::valid(),
            FakeAccessToken::expired(),
            FakeAccessToken::webDav(),
        ];
    }
}
