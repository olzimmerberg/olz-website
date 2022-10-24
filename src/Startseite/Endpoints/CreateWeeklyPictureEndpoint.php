<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Role;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\User;
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

        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getSessionUser();
        $data_path = $this->envUtils()->getDataPath();
        $input_data = $input['data'];

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $valid_image_id = $this->uploadUtils()->getValidUploadId($input_data['imageId']);
        $valid_alternative_image_id = $this->uploadUtils()->getValidUploadId($input_data['alternativeImageId'] ?? null);

        $weekly_picture = new WeeklyPicture();
        $this->entityUtils()->createOlzEntity($weekly_picture, $input['meta']);
        $weekly_picture->setDate($now);
        $weekly_picture->setText($input_data['text']);
        $weekly_picture->setImageId($valid_image_id);
        $weekly_picture->setAlternativeImageId($valid_alternative_image_id);

        $this->entityManager()->persist($weekly_picture);
        $this->entityManager()->flush();

        $weekly_picture_id = $weekly_picture->getId();

        $weekly_picture_img_path = "{$data_path}img/weekly_picture/{$weekly_picture_id}/";
        mkdir("{$weekly_picture_img_path}img/", 0777, true);
        mkdir("{$weekly_picture_img_path}thumb/", 0777, true);
        $this->uploadUtils()->moveUploads([$valid_image_id], "{$weekly_picture_img_path}img/");
        if ($valid_alternative_image_id) {
            $this->uploadUtils()->moveUploads([$valid_alternative_image_id], "{$weekly_picture_img_path}img/");
        }
        // TODO: Generate default thumbnails.

        return [
            'status' => 'OK',
            'id' => $weekly_picture_id,
        ];
    }
}
