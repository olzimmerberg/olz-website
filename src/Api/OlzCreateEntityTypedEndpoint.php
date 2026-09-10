<?php

namespace Olz\Api;

/**
 * @template Id
 * @template Data
 * @template CustomRequest = never
 * @template CustomResponse = never
 *
 * @phpstan-import-type OlzMetaData from OlzTypedEndpoint
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     meta?: OlzMetaData,
 *     data: Data,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     id?: ?Id,
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzCreateEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzDataStorageEndpointTrait;
}
