<?php

namespace Olz\Captcha\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Captcha\Utils\CaptchaUtils;
use Olz\Captcha\Utils\CaptchaUtilsTrait;

/**
 * @phpstan-import-type OlzCaptchaConfig from CaptchaUtils
 *
 * @extends OlzTypedEndpoint<
 *   array{},
 *   array{
 *     config: OlzCaptchaConfig,
 *   }
 * >
 */
class StartCaptchaEndpoint extends OlzTypedEndpoint {
    use CaptchaUtilsTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(CaptchaUtils::class);
    }

    protected function handle(mixed $input): mixed {
        $config = $this->captchaUtils()->generateOlzCaptchaConfig(3);
        return [
            'config' => $config,
        ];
    }
}
