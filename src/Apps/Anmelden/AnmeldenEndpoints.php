<?php

namespace Olz\Apps\Anmelden;

use Olz\Apps\Anmelden\Endpoints\CreateBookingEndpoint;
use Olz\Apps\Anmelden\Endpoints\CreateRegistrationEndpoint;
use Olz\Apps\Anmelden\Endpoints\GetManagedUsersEndpoint;
use Olz\Apps\Anmelden\Endpoints\GetPrefillValuesEndpoint;
use Olz\Apps\Anmelden\Endpoints\GetRegistrationEndpoint;
use Olz\Apps\BaseAppEndpoints;
use PhpTypeScriptApi\Api;

class AnmeldenEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('createBooking', function () {
            return new CreateBookingEndpoint();
        });
        $api->registerEndpoint('createRegistration', function () {
            return new CreateRegistrationEndpoint();
        });
        $api->registerEndpoint('getManagedUsers', function () {
            return new GetManagedUsersEndpoint();
        });
        $api->registerEndpoint('getPrefillValues', function () {
            return new GetPrefillValuesEndpoint();
        });
        $api->registerEndpoint('getRegistration', function () {
            return new GetRegistrationEndpoint();
        });
    }
}
