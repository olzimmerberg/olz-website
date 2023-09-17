<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Anmelden\Registration;

class GetRegistrationEndpoint extends OlzGetEntityEndpoint {
    use RegistrationEndpointTrait;

    public static function getIdent() {
        return 'GetRegistrationEndpoint';
    }

    protected function handle($input) {
        $external_id = $input['id'];
        $internal_id = $this->idUtils()->toInternalId($external_id, 'Registration');
        $registration_repo = $this->entityManager()->getRepository(Registration::class);
        $registration = $registration_repo->findOneBy(['id' => $internal_id]);

        return [
            'id' => $external_id,
            'meta' => $registration->getMetaData(),
            'data' => $this->getEntityData($registration),
        ];
    }
}
