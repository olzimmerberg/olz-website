<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Role;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use PhpTypeScriptApi\HttpError;

class CreateTerminEndpoint extends OlzCreateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'CreateTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getSessionUser();
        $data_path = $this->envUtils()->getDataPath();

        $input_data = $input['data'];

        $types_for_db = $this->getTypesForDb($input_data['types']);

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $termin = new Termin();
        // TODO: Enable when Termine is migrated to OlzEntity
        // $this->entityUtils()->createOlzEntity($termin, $input['meta']);
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
        if (!is_dir("{$termin_files_path}")) {
            mkdir("{$termin_files_path}", 0777, true);
        }
        $this->uploadUtils()->moveUploads($input_data['fileIds'], $termin_files_path);

        return [
            'status' => 'OK',
            'id' => $termin_id,
        ];
    }
}
