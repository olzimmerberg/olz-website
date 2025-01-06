<?php

namespace Olz\Api;

use PhpTypeScriptApi\PhpStan\PhpStanUtils;

/**
 * @template Id
 * @template Data
 * @template CustomRequest = never
 * @template CustomResponse = never
 * @template CustomItem = never
 *
 * @phpstan-import-type OlzMetaData from OlzEntityEndpointTrait
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     items: array{
 *       id: Id,
 *       meta: OlzMetaData,
 *       data: Data,
 *       custom?: CustomItem
 *     },
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzListEntitiesTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;

    public function configure(): void {
        parent::configure();
        PhpStanUtils::registerTypeImport(OlzEntityEndpointTrait::class);
    }
}
