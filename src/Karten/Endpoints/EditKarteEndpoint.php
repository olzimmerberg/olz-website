<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use Olz\Entity\Karten\Karte;
use PhpTypeScriptApi\HttpError;

class EditKarteEndpoint extends OlzEditEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent() {
        return 'EditKarteEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karte = $karten_repo->findOneBy(['id' => $entity_id]);

        if (!$karte) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($karte, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();

        $image_id = $karte->getPreviewImageId();
        if ($image_id) {
            $karte_img_path = "{$data_path}img/karten/{$entity_id}/";
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
