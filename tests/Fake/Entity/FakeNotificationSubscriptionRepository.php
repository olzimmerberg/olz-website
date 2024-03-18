<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeNotificationSubscriptionRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeNotificationSubscription::class;
}
