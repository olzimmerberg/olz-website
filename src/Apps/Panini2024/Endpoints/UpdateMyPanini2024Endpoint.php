<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Apps\Panini2024\Panini2024Constants;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\DateUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EnvUtilsTrait;
use Olz\Utils\UploadUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzPanini2024PictureData array{
 *   id?: ?int<1, max>,
 *   line1: non-empty-string,
 *   line2: non-empty-string,
 *   residence: non-empty-string,
 *   uploadId: non-empty-string,
 *   onOff: bool,
 *   info1: non-empty-string,
 *   info2: non-empty-string,
 *   info3: non-empty-string,
 *   info4: non-empty-string,
 *   info5: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{data: OlzPanini2024PictureData},
 *   array{status: 'OK'|'ERROR'}
 * >
 */
class UpdateMyPanini2024Endpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use DateUtilsTrait;
    use EntityManagerTrait;
    use EnvUtilsTrait;
    use UploadUtilsTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $current_user = $this->authUtils()->getCurrentUser();
        $panini_repo = $this->entityManager()->getRepository(Panini2024Picture::class);
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $data_path = $this->envUtils()->getDataPath();
        $deadline_datetime = new \DateTime(Panini2024Constants::UPDATE_DEADLINE);

        $has_admin_access = $this->authUtils()->hasPermission('all');
        if ($now_datetime > $deadline_datetime && !$has_admin_access) {
            throw new HttpError(400, "Zu spät!");
        }

        $input_data = $input['data'];
        $id = $input_data['id'] ?? null;
        if ($id) {
            $picture = $panini_repo->findOneBy(['id' => $id]);
            if (!$picture) {
                throw new HttpError(404, "No such panini picture.");
            }
        } else {
            $picture = new Panini2024Picture();
            $picture->setOwnerUser($current_user);
            $picture->setOwnerRole(null);
            $picture->setCreatedAt($now_datetime);
            $picture->setCreatedByUser($current_user);
            $picture->setLastModifiedAt($now_datetime);
            $picture->setLastModifiedByUser($current_user);
        }
        $picture->setLine1($input_data['line1']);
        $picture->setLine2($input_data['line2']);
        $picture->setAssociation($input_data['residence']);
        $picture->setImgSrc($input_data['uploadId']);
        $picture->setImgStyle('width:100%; top:0%; left:0%;');
        $picture->setIsLandscape(false);
        $picture->setHasTop(false);
        $picture->setInfos([
            $input_data['info1'],
            $input_data['info2'],
            $input_data['info3'],
            $input_data['info4'],
            $input_data['info5'],
        ]);
        $picture->setOnOff($input_data['onOff'] ? 1 : 0);
        $this->entityManager()->persist($picture);
        $this->entityManager()->flush();

        $panini_id = $picture->getId();

        $portraits_path = "{$data_path}panini_data/portraits/{$panini_id}/";
        $valid_upload_id = $this->uploadUtils()->getValidUploadId($input_data['uploadId']);
        if ($valid_upload_id) {
            $this->uploadUtils()->overwriteUploads([$valid_upload_id], $portraits_path);
        }

        return ['status' => 'OK'];
    }
}
