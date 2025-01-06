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
 *     meta?: ?OlzMetaData,
 *     data?: ?Data,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     status: 'OK'|'ERROR',
 *     id: Id,
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzUpdateEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;

    public function configure(): void {
        parent::configure();
        PhpStanUtils::registerTypeImport(OlzEntityEndpointTrait::class);
    }
}
