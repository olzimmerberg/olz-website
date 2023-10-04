<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;
use PhpTypeScriptApi\HttpError;

class UpdateTerminTemplateEndpoint extends OlzUpdateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminTemplateEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template = $termin_template_repo->findOneBy(['id' => $input['id']]);

        if (!$termin_template) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($termin_template, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($termin_template, $input['meta'] ?? []);
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
