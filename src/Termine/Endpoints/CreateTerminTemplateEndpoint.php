<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;
use PhpTypeScriptApi\HttpError;

class CreateTerminTemplateEndpoint extends OlzCreateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_template = new TerminTemplate();
        $this->entityUtils()->createOlzEntity($termin_template, $input['meta']);
        $this->updateEntityWithData($termin_template, $input['data']);

        $this->entityManager()->persist($termin_template);
        $this->entityManager()->flush();
        $this->persistUploads($termin_template, $input['data']);

        return [
            'status' => 'OK',
            'id' => $termin_template->getId(),
        ];
    }
}
