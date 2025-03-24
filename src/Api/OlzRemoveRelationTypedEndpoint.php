<?php

namespace Olz\Api;

/**
 * @template Ids
 * @template CustomRequest = never
 * @template CustomResponse = never
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     ids: Ids,
 *     custom?: CustomRequest,
 *   },
 *   array{
 *     custom?: CustomResponse,
 *   }
 * >
 */
abstract class OlzRemoveRelationTypedEndpoint extends OlzTypedEndpoint {
}
