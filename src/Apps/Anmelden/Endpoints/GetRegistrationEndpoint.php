<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;
use Olz\Utils\IdUtilsTrait;

/**
 * @phpstan-import-type OlzRegistrationId from RegistrationEndpointTrait
 * @phpstan-import-type OlzRegistrationData from RegistrationEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzRegistrationInfo from RegistrationEndpointTrait
 * @phpstan-import-type ValidRegistrationInfoType from RegistrationEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzRegistrationId, OlzRegistrationData>
 */
class GetRegistrationEndpoint extends OlzGetEntityTypedEndpoint {
    use IdUtilsTrait;
    use RegistrationEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureRegistrationEndpointTrait();
        $this->phpStanUtils->registerTypeImport(RegistrationEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $external_id = $input['id'];
        $internal_id = $this->idUtils()->toInternalId($external_id, 'Registration');

        $entity = $this->getEntityById($internal_id);

        return [
            'id' => $external_id,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
