<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;

class CreateTerminTemplateEndpoint extends OlzCreateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

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
