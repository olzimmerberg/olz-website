<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class UpdateTerminEndpoint extends OlzUpdateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $entity_id]);

        if (!$termin) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        // TODO: Enable when Termine is migrated to OlzEntity
        // if (!$this->entityUtils()->canUpdateOlzEntity($termin, $input['meta'])) {
        //     throw new HttpError(403, "Kein Zugriff!");
        // }

        $current_user = $this->authUtils()->getCurrentUser();
        $data_path = $this->envUtils()->getDataPath();
        $input_data = $input['data'];

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $types_for_db = $this->getTypesForDb($input_data['types']);

        // TODO: Enable when Termine is migrated to OlzEntity
        // $this->entityUtils()->updateOlzEntity($termin, $input['meta'] ?? []);
        $termin->setStartsOn(new \DateTime($input_data['startDate']));
        $termin->setStartTime($input_data['startTime']);
        $termin->setEndsOn($input_data['endDate'] ? new \DateTime($input_data['endDate']) : null);
        $termin->setEndTime($input_data['endTime']);
        $termin->setTitle($input_data['title']);
        $termin->setText($input_data['text']);
        $termin->setLink($input_data['link']);
        $termin->setDeadline($input_data['deadline']);
        $termin->setNewsletter($input_data['newsletter']);
        $termin->setSolvId($input_data['solvId']);
        $termin->setGo2olId($input_data['go2olId']);
        $termin->setTypes($types_for_db);
        $termin->setOnOff($input_data['onOff']);
        $termin->setCoordinateX($input_data['coordinateX']);
        $termin->setCoordinateY($input_data['coordinateY']);

        $this->entityManager()->persist($termin);
        $this->entityManager()->flush();

        $termin_id = $termin->getId();

        $termin_files_path = "{$data_path}files/termine/{$termin_id}/";
        $this->uploadUtils()->moveUploads($input_data['fileIds'], $termin_files_path);

        return [
            'status' => 'OK',
            'id' => $termin_id,
        ];
    }
}
