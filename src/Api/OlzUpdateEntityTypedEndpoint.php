<?php

namespace Olz\Api;

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
 *     meta: OlzMetaData,
 *     data: Data,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     id: Id,
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzUpdateEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;
}
