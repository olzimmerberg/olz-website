<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;

/**
 * @phpstan-import-type OlzRegistrationId from RegistrationEndpointTrait
 * @phpstan-import-type OlzRegistrationData from RegistrationEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzRegistrationInfo from RegistrationEndpointTrait
 * @phpstan-import-type ValidRegistrationInfoType from RegistrationEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzRegistrationId, OlzRegistrationData>
 */
class CreateRegistrationEndpoint extends OlzCreateEntityTypedEndpoint {
    use RegistrationEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $input_data = $input['data'];

        $entity = new Registration();
        $this->entityUtils()->createOlzEntity($entity, $input['meta'] ?? null);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);

        foreach ($input_data['infos'] as $index => $info_spec) {
            $title_ident = preg_replace('/[^a-zA-Z0-9]+/', '_', $info_spec['title']);
            $ident = "{$index}-{$title_ident}";

            $options_json = json_encode($info_spec['options'] ?? []) ?: '{}';

            $registration_info = new RegistrationInfo();
            $this->entityUtils()->createOlzEntity($registration_info, $input['meta'] ?? null);
            $registration_info->setRegistration($entity);
            $registration_info->setIndexWithinRegistration($index);
            $registration_info->setIdent($ident);
            $registration_info->setTitle($info_spec['title']);
            $registration_info->setDescription($info_spec['description']);
            $registration_info->setType($info_spec['type']);
            $registration_info->setIsOptional($info_spec['isOptional'] ? true : false);
            $registration_info->setOptions($options_json);

            $this->entityManager()->persist($registration_info);
        }
        $this->entityManager()->flush();

        $internal_id = $entity->getId() ?? 0;
        $external_id = $this->idUtils()->toExternalId($internal_id, 'Registration') ?: '-';

        return [
            'id' => $external_id,
        ];
    }
}
