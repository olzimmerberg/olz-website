<?php

namespace Olz\Apps\Quiz;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Quiz\Endpoints\GetMySkillLevelsEndpoint;
use Olz\Apps\Quiz\Endpoints\RegisterSkillCategoriesEndpoint;
use Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint;
use Olz\Apps\Quiz\Endpoints\UpdateMySkillLevelsEndpoint;
use PhpTypeScriptApi\Api;

class QuizEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected GetMySkillLevelsEndpoint $getMySkillLevelsEndpoint,
        protected UpdateMySkillLevelsEndpoint $updateMySkillLevelsEndpoint,
        protected RegisterSkillCategoriesEndpoint $registerSkillCategoriesEndpoint,
        protected RegisterSkillsEndpoint $registerSkillsEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('getMySkillLevels', $this->getMySkillLevelsEndpoint);
        $api->registerEndpoint('updateMySkillLevels', $this->updateMySkillLevelsEndpoint);
        $api->registerEndpoint('registerSkillCategories', $this->registerSkillCategoriesEndpoint);
        $api->registerEndpoint('registerSkills', $this->registerSkillsEndpoint);
    }
}
