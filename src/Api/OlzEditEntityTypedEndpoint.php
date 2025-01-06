<?php

namespace Olz\Api;

use PhpTypeScriptApi\PhpStan\PhpStanUtils;

/**
 * @template Id
 * @template Data
 * @template CustomRequest = never
 * @template CustomResponse = never
 *
 * @phpstan-import-type OlzMetaData from OlzEntityEndpointTrait
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     id: Id,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     id: Id,
 *     meta: OlzMetaData,
 *     data: Data,
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzEditEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;

    public function configure(): void {
        parent::configure();
        PhpStanUtils::registerTypeImport(OlzEntityEndpointTrait::class);
    }
}
