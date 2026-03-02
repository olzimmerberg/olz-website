<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\ForwardedEmail;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<ForwardedEmail>
 */
class FakeForwardedEmailRepository extends FakeOlzRepository {
    public string $olzEntityClass = ForwardedEmail::class;
    public string $fakeOlzEntityClass = FakeForwardedEmail::class;
}
