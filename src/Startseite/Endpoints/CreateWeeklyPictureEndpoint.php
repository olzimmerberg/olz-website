<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Startseite\WeeklyPicture;
use PhpTypeScriptApi\HttpError;

class CreateWeeklyPictureEndpoint extends OlzCreateEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent() {
        return 'CreateWeeklyPictureEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('weekly_picture');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $weekly_picture = new WeeklyPicture();
        $this->entityUtils()->createOlzEntity($weekly_picture, $input['meta']);
        $this->updateEntityWithData($weekly_picture, $input['data']);

        $this->entityManager()->persist($weekly_picture);
        $this->entityManager()->flush();
        $this->persistUploads($weekly_picture);

        return [
            'status' => 'OK',
            'id' => $weekly_picture->getId(),
        ];
    }
}
