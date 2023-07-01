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
        if (!$this->entityUtils()->canUpdateOlzEntity($termin, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $current_user = $this->authUtils()->getCurrentUser();
        $data_path = $this->envUtils()->getDataPath();
        $input_data = $input['data'];

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $types_for_db = $this->getTypesForDb($input_data['types']);
        $valid_image_ids = $this->uploadUtils()->getValidUploadIds($input_data['imageIds']);

        $this->entityUtils()->updateOlzEntity($termin, $input['meta'] ?? []);
        $termin->setStartsOn(new \DateTime($input_data['startDate']));
        $termin->setStartTime($input_data['startTime'] ? new \DateTime($input_data['startTime']) : null);
        $termin->setEndsOn($input_data['endDate'] ? new \DateTime($input_data['endDate']) : null);
        $termin->setEndTime($input_data['endTime'] ? new \DateTime($input_data['endTime']) : null);
        $termin->setTitle($input_data['title']);
        $termin->setText($input_data['text']);
        $termin->setLink($input_data['link']);
        $termin->setDeadline($input_data['deadline'] ? new \DateTime($input_data['deadline']) : null);
        $termin->setNewsletter($input_data['newsletter']);
        $termin->setSolvId($input_data['solvId']);
        $termin->setGo2olId($input_data['go2olId']);
        $termin->setTypes($types_for_db);
        $termin->setCoordinateX($input_data['coordinateX']);
        $termin->setCoordinateY($input_data['coordinateY']);
        $termin->setImageIds($valid_image_ids);

        $this->entityManager()->persist($termin);
        $this->entityManager()->flush();

        $termin_id = $termin->getId();

        $termin_img_path = "{$data_path}img/termine/{$termin_id}/";
        $this->uploadUtils()->moveUploads($valid_image_ids, "{$termin_img_path}img/");
        // TODO: Generate default thumbnails.

        $termin_files_path = "{$data_path}files/termine/{$termin_id}/";
        $this->uploadUtils()->moveUploads($input_data['fileIds'], $termin_files_path);

        return [
            'status' => 'OK',
            'id' => $termin_id,
        ];
    }
}
