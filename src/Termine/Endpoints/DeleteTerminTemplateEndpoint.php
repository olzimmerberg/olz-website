<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;
use PhpTypeScriptApi\HttpError;

class DeleteTerminTemplateEndpoint extends OlzDeleteEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template = $termin_template_repo->findOneBy(['id' => $entity_id]);

        if (!$termin_template) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_template->setOnOff(0);
        $this->entityManager()->persist($termin_template);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
