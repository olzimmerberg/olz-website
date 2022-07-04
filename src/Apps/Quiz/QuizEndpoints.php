<?php

namespace Olz\Apps\Quiz;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Quiz\Endpoints\GetMySkillLevelsEndpoint;
use Olz\Apps\Quiz\Endpoints\RegisterSkillCategoriesEndpoint;
use Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint;
use Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint;
use PhpTypeScriptApi\Api;

class QuizEndpoints extends BaseAppEndpoints {
    public function register(Api $api): void {
        $api->registerEndpoint('getMySkillLevels', function () {
            return new GetMySkillLevelsEndpoint();
        });
        $api->registerEndpoint('updateMySkillLevels', function () {
            return new UpdateMySkillLevelsEndpoint();
        });
        $api->registerEndpoint('registerSkillCategories', function () {
            return new RegisterSkillCategoriesEndpoint();
        });
        $api->registerEndpoint('registerSkills', function () {
            return new RegisterSkillsEndpoint();
        });
    }
}
