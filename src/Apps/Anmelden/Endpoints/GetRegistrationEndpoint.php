<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;

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

        $infos = [];
        $registration_info_repo = $this->entityManager()->getRepository(RegistrationInfo::class);
        $registration_infos = $registration_info_repo->findBy(
            ['registration' => $registration, 'onOff' => true],
            ['indexWithinRegistration' => 'ASC'],
        );
        foreach ($registration_infos as $registration_info) {
            $options = json_decode($registration_info->getOptions() ?? 'null', true);
            $infos[] = [
                'type' => $registration_info->getType(),
                'isOptional' => $registration_info->getIsOptional(),
                'title' => $registration_info->getTitle(),
                'description' => $registration_info->getDescription(),
                'options' => $options,
            ];
        }

        $owner_user = $registration->getOwnerUser();
        $owner_role = $registration->getOwnerRole();

        return [
            'id' => $external_id,
            'meta' => [
                'ownerUserId' => $owner_user ? $owner_user->getId() : null,
                'ownerRoleId' => $owner_role ? $owner_role->getId() : null,
                'onOff' => $registration->getOnOff(),
            ],
            'data' => [
                'title' => $registration->getTitle(),
                'description' => $registration->getDescription(),
                'infos' => $infos,
                'opensAt' => $registration->getOpensAt(),
                'closesAt' => $registration->getClosesAt(),
            ],
        ];
    }
}
