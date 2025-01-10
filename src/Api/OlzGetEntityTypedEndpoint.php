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
abstract class OlzGetEntityTypedEndpoint extends OlzTypedEndpoint {
    use OlzEntityEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(OlzEntityEndpointTrait::class);
    }
}
