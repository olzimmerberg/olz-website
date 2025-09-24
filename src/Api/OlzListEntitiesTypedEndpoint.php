<?php

namespace Olz\Api;

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
 *     items: array<array{
 *       id: Id,
 *       meta: OlzMetaData,
 *       data: Data,
 *       custom?: CustomItem
 *     }>,
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzListEntitiesTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;
}
