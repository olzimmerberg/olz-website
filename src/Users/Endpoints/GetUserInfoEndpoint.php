<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzUserId from UserInfoEndpointTrait
 * @phpstan-import-type OlzUserInfoData from UserInfoEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzUserId, OlzUserInfoData, array{
 *   recaptchaToken?: ?non-empty-string,
 * }>
 */
class GetUserInfoEndpoint extends OlzGetEntityTypedEndpoint {
    use UserInfoEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureUserEndpointTrait();
        $this->phpStanUtils->registerTypeImport(UserInfoEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['custom']['recaptchaToken'] ?? null;
        $is_valid_token = $token ? $this->recaptchaUtils()->validateRecaptchaToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Recaptcha token invalid');
        }

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
