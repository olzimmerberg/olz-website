<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\StravaLink;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<StravaLink>
 */
class FakeStravaLink extends FakeEntity {
    public static function defaultStravaLink(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setAccessToken('fake-access-token');
                $strava_link->setExpiresAt(new \DateTime('1992-08-05 13:27:00'));
                $strava_link->setRefreshToken('fake-access-token');
                $strava_link->setStravaUser('fake-strava-user-id');
                $strava_link->setUser(FakeUser::defaultUser());
                return $strava_link;
            }
        );
    }
}
