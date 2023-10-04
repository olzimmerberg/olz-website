<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;
use PhpTypeScriptApi\HttpError;

class GetTerminTemplateEndpoint extends OlzGetEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'GetTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template = $termin_template_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $termin_template->getId(),
            'meta' => $termin_template->getMetaData(),
            'data' => $this->getEntityData($termin_template),
        ];
    }
}
