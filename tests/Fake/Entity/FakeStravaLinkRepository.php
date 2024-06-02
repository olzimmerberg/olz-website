<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\StravaLink;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<StravaLink>
 */
class FakeStravaLinkRepository extends FakeOlzRepository {
    public string $olzEntityClass = StravaLink::class;
    public string $fakeOlzEntityClass = FakeStravaLink::class;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['strava_user' => 'fake_existing_id']) {
            $strava_link = FakeStravaLink::defaultStravaLink(true);
            $strava_link->setUser(FakeUser::defaultUser());
            return $strava_link;
        }
        if ($criteria === ['strava_user' => 'fake_inexistent_id']) {
            return null;
        }
        return parent::findOneBy($criteria);
    }
}
