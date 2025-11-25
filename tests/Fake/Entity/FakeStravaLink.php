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
    public static function minimal(bool $fresh = false): StravaLink {
        return self::getFake(
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setId(12);
                $strava_link->setAccessToken('');
                $strava_link->setExpiresAt(new \DateTime('1992-08-05 13:27:00'));
                $strava_link->setRefreshToken('');
                $strava_link->setStravaUser('');
                $strava_link->setUser(FakeUser::minimal());
                return $strava_link;
            }
        );
    }

    public static function empty(bool $fresh = false): StravaLink {
        return self::getFake(
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setId(123);
                $strava_link->setAccessToken('');
                $strava_link->setExpiresAt(new \DateTime('0000-00-00 00:00:00'));
                $strava_link->setRefreshToken('');
                $strava_link->setStravaUser('');
                $strava_link->setUser(FakeUser::empty());
                return $strava_link;
            }
        );
    }

    public static function maximal(bool $fresh = false): StravaLink {
        return self::getFake(
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setId(1234);
                $strava_link->setAccessToken('fake-access-token');
                $strava_link->setExpiresAt(new \DateTime('1992-08-05 13:27:00'));
                $strava_link->setRefreshToken('fake-access-token');
                $strava_link->setStravaUser('fake-strava-user-id');
                $strava_link->setUser(FakeUser::maximal());
                return $strava_link;
            }
        );
    }

    public static function defaultStravaLink(bool $fresh = false): StravaLink {
        return self::getFake(
            $fresh,
            function () {
                $strava_link = new StravaLink();
                $strava_link->setId(1);
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
