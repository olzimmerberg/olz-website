<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Panini2024\Panini2024Picture;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class UpdateMyPanini2024Endpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateMyPanini2024Endpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        $panini_2024_picture_field = new FieldTypes\ObjectField([
            'field_structure' => [
                'id' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
                'line1' => new FieldTypes\StringField(['allow_null' => false]),
                'line2' => new FieldTypes\StringField(['allow_null' => false]),
                'residence' => new FieldTypes\StringField(['allow_null' => false]),
                'uploadId' => new FieldTypes\StringField(['allow_null' => false]),
                'onOff' => new FieldTypes\BooleanField(['allow_null' => false]),
                'info1' => new FieldTypes\StringField(['allow_null' => false]),
                'info2' => new FieldTypes\StringField(['allow_null' => false]),
                'info3' => new FieldTypes\StringField(['allow_null' => false]),
                'info4' => new FieldTypes\StringField(['allow_null' => false]),
                'info5' => new FieldTypes\StringField(['allow_null' => false]),
            ],
        ]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'data' => $panini_2024_picture_field,
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $current_user = $this->authUtils()->getSessionUser();
        $panini_repo = $this->entityManager()->getRepository(Panini2024Picture::class);
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $data_path = $this->envUtils()->getDataPath();

        $input_data = $input['data'];
        $id = $input_data['id'];
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
        $picture->setOnOff($input_data['onOff']);
        $this->entityManager()->persist($picture);
        $this->entityManager()->flush();

        $portraits_path = "{$data_path}panini_data/portraits/";
        $valid_upload_id = $this->uploadUtils()->getValidUploadId($input_data['uploadId']);
        if ($valid_upload_id) {
            $this->uploadUtils()->moveUploads([$valid_upload_id], $portraits_path);
        }

        return ['status' => 'OK'];
    }
}