<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use Olz\Utils\IdUtilsTrait;

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
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use IdUtilsTrait;
    use RegistrationEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureRegistrationEndpointTrait();
        $this->phpStanUtils->registerTypeImport(RegistrationEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $input_data = $input['data'];

        $registration = new Registration();
        $this->entityUtils()->createOlzEntity($registration, $input['meta']);
        $this->updateEntityWithData($registration, $input['data']);

        $this->entityManager()->persist($registration);

        foreach ($input_data['infos'] as $index => $info_spec) {
            $title_ident = preg_replace('/[^a-zA-Z0-9]+/', '_', $info_spec['title']);
            $ident = "{$index}-{$title_ident}";

            $options_json = json_encode($info_spec['options'] ?? []) ?: '{}';

            $registration_info = new RegistrationInfo();
            $this->entityUtils()->createOlzEntity($registration_info, $input['meta']);
            $registration_info->setRegistration($registration);
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

        $internal_id = $registration->getId() ?? 0;
        $external_id = $this->idUtils()->toExternalId($internal_id, 'Registration') ?: '-';

        return [
            'id' => $external_id,
        ];
    }
}
