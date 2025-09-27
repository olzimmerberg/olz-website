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
    public function __construct(
        protected CreateBookingEndpoint $createBookingEndpoint,
        protected CreateRegistrationEndpoint $createRegistrationEndpoint,
        protected GetManagedUsersEndpoint $getManagedUsersEndpoint,
        protected GetPrefillValuesEndpoint $getPrefillValuesEndpoint,
        protected GetRegistrationEndpoint $getRegistrationEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('createBooking', $this->createBookingEndpoint);
        $api->registerEndpoint('createRegistration', $this->createRegistrationEndpoint);
        $api->registerEndpoint('getManagedUsers', $this->getManagedUsersEndpoint);
        $api->registerEndpoint('getPrefillValues', $this->getPrefillValuesEndpoint);
        $api->registerEndpoint('getRegistration', $this->getRegistrationEndpoint);
    }
}
