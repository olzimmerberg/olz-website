<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Startseite\WeeklyPicture;

class CreateWeeklyPictureEndpoint extends OlzCreateEntityEndpoint {
    use WeeklyPictureEndpointTrait;

    public static function getIdent() {
        return 'CreateWeeklyPictureEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('weekly_picture');

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
