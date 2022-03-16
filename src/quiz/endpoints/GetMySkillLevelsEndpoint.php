<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../QuizConstants.php';

class GetMySkillLevelsEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/env/EnvUtils.php';
        require_once __DIR__.'/../../utils/EntityUtils.php';
        require_once __DIR__.'/../../utils/IdUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $entity_utils = EntityUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $id_utils = IdUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setEntityManager($entityManager);
        $this->setEntityUtils($entity_utils);
        $this->setEnvUtils($env_utils);
        $this->setIdUtils($id_utils);
    }

    public function setAuthUtils($authUtils) {
        $this->authUtils = $authUtils;
    }

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setEntityUtils($entityUtils) {
        $this->entityUtils = $entityUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function setIdUtils($idUtils) {
        $this->idUtils = $idUtils;
    }

    public static function getIdent() {
        return 'GetMySkillLevelsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\DictField([
            'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                'value' => new FieldTypes\NumberField(['min_value' => 0.0, 'max_value' => 1.0]),
            ]]),
        ]);
    }

    public function getRequestField() {
        $skill_filter = new FieldTypes\ChoiceField([
            'field_map' => [
                'categoryIdIn' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => true,
        ]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'skillFilter' => $skill_filter,
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $current_user = $this->authUtils->getSessionUser();
        $skill_repo = $this->entityManager->getRepository(Skill::class);
        $skill_level_repo = $this->entityManager->getRepository(SkillLevel::class);

        $external_category_ids = $input['skillFilter']['categoryIdIn'] ?? null;

        if ($external_category_ids === null) {
            $skills = $skill_repo->findAll();
            $skill_levels = $skill_level_repo->getSkillLevelsForUserId(
                $current_user->getId());
        } else {
            $internal_category_ids = array_map(
                function ($id) {
                    return $this->idUtils->toInternalId($id, 'SkillCategory');
                },
                $external_category_ids,
            );
            $skills = $skill_repo->getSkillsInCategories($internal_category_ids);
            $skill_levels = $skill_level_repo->getSkillLevelsForUserIdInCategories(
                $current_user->getId(), $internal_category_ids);
        }

        $skill_level_by_skill_id = [];
        foreach ($skills as $skill) {
            $internal_skill_id = $skill->getId();
            $external_skill_id = $this->idUtils->toExternalId($internal_skill_id, 'Skill');
            $value = QuizConstants::INITIAL_SKILL_LEVEL_VALUE;
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }
        foreach ($skill_levels as $skill_level) {
            $internal_skill_id = $skill_level->getSkill()->getId();
            $external_skill_id = $this->idUtils->toExternalId($internal_skill_id, 'Skill');
            $value = $skill_level->getValue();
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }

        return $skill_level_by_skill_id;
    }
}
