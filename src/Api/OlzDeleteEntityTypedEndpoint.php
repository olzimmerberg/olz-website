<?php

namespace Olz\Api;

/**
 * @template Id
 * @template Data
 * @template CustomRequest = never
 * @template CustomResponse = never
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     id: Id,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzDeleteEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzDataStorageEndpointTrait;
}
