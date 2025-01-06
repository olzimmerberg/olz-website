<?php

namespace Olz\Apps\Panini2024\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @phpstan-type OlzPanini2024PictureData array{
 *   id: int<1, max>,
 *   line1: non-empty-string,
 *   line2?: ?non-empty-string,
 *   association?: ?non-empty-string,
 *   imgSrc: non-empty-string,
 *   imgStyle: non-empty-string,
 *   isLandscape: bool,
 *   hasTop: bool,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{filter?: ?(
 *     array{idIs: int<1, max>}
 *     | array{page: int<1, max>}
 *   )},
 *   array<array{data: OlzPanini2024PictureData}>,
 * >
 */
class ListPanini2024PicturesEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $this->checkPermission('panini2024');

        // TODO: Implement

        return [];
    }
}
