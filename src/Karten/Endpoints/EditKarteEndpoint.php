<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditKarteEndpoint extends OlzEditEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'EditKarteEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $karte = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($karte, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_id = $karte->getPreviewImageId();
        if ($image_id) {
            $karte_img_path = "{$data_path}img/karten/{$karte->getId()}/";
            $image_path = "{$karte_img_path}img/{$image_id}";
            $temp_path = "{$data_path}temp/{$image_id}";
            copy($image_path, $temp_path);
        }

        return [
            'id' => $karte->getId(),
            'meta' => $karte->getMetaData(),
            'data' => $this->getEntityData($karte),
        ];
    }
}
