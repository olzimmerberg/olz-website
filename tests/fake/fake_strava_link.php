<?php

require_once __DIR__.'/../../src/model/StravaLink.php';

function get_fake_strava_link() {
    $strava_link = new StravaLink();
    $strava_link->setAccessToken('fake-access-token');
    $strava_link->setExpiresAt('1992-08-05 13:27:00');
    $strava_link->setRefreshToken('fake-access-token');
    $strava_link->setStravaUser('fake-strava-user-id');
    return $strava_link;
}
