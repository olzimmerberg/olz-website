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
 *     status: 'OK'|'ERROR',
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzDeleteEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;
}
