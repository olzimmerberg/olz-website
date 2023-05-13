<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class GetTerminEndpoint extends OlzGetEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'GetTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $entity_id = $input['id'];
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $entity_id]);

        $types_for_api = $this->getTypesForApi($termin->getTypes() ?? '');

        $file_ids = [];
        $termin_files_path = "{$data_path}files/termine/{$entity_id}/";
        $files_path_entries = is_dir($termin_files_path)
            ? scandir($termin_files_path) : [];
        foreach ($files_path_entries as $file_id) {
            if (substr($file_id, 0, 1) != '.') {
                $file_ids[] = $file_id;
            }
        }

        return [
            'id' => $entity_id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => $termin->getOnOff() ? true : false,
            ],
            'data' => [
                'startDate' => $termin->getStartsOn()->format('Y-m-d'),
                'startTime' => $termin->getStartTime() ? $termin->getStartTime()->format('H:i:s') : null,
                'endDate' => $termin->getEndsOn() ? $termin->getEndsOn()->format('Y-m-d') : null,
                'endTime' => $termin->getEndTime() ? $termin->getEndTime()->format('H:i:s') : null,
                'title' => $termin->getTitle(),
                'text' => $termin->getText() ?? '',
                'link' => $termin->getLink() ?? '',
                'deadline' => $termin->getDeadline() ? $termin->getDeadline()->format('Y-m-d H:i:s') : null,
                'newsletter' => $termin->getNewsletter(),
                'solvId' => $termin->getSolvId() ? $termin->getSolvId() : null,
                'go2olId' => $termin->getGo2olId() ? $termin->getGo2olId() : null,
                'types' => $types_for_api,
                'coordinateX' => $termin->getCoordinateX(),
                'coordinateY' => $termin->getCoordinateY(),
                'fileIds' => $file_ids,
            ],
        ];
    }
}
