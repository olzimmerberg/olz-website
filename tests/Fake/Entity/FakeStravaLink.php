<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\StravaLink;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeStravaLink extends FakeEntity {
    public static function defaultStravaLink($fresh = false) {
        return self::getFake(
            'default_strava_link',
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setAccessToken('fake-access-token');
                $strava_link->setExpiresAt('1992-08-05 13:27:00');
                $strava_link->setRefreshToken('fake-access-token');
                $strava_link->setStravaUser('fake-strava-user-id');
                return $strava_link;
            }
        );
    }
}
