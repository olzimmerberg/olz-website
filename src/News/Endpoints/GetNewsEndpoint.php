<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetNewsEndpoint extends OlzGetEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent() {
        return 'GetNewsEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $news_entry = $this->getEntityById($input['id']);

        return [
            'id' => $news_entry->getId(),
            'meta' => $news_entry->getMetaData(),
            'data' => $this->getEntityData($news_entry),
        ];
    }
}
