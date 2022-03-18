<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

class RegisterSkillsEndpoint extends OlzEndpoint {
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
        return 'RegisterSkillsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'idByName' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\StringField([]),
            ]),
        ]]);
    }

    public function getRequestField() {
        $skill_field = new FieldTypes\ObjectField(['field_structure' => [
            'name' => new FieldTypes\StringField([]),
            'categoryIds' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\StringField([]),
            ]),
        ]]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'skills' => new FieldTypes\ArrayField([
                'item_field' => $skill_field,
            ]),
        ]]);
    }

    protected function handle($input) {
        $skill_repo = $this->entityManager->getRepository(Skill::class);
        $skill_category_repo = $this->entityManager->getRepository(SkillCategory::class);
        $skill_by_name = [];
        foreach ($input['skills'] as $input_skill) {
            $skill_name = $input_skill['name'];
            $skill_category_ids = $input_skill['categoryIds'];
            $existing_skill = $skill_repo->findOneBy(['name' => $skill_name]);
            if ($existing_skill) {
                $skill = $existing_skill;
            } else {
                $skill = new Skill();
                $this->entityUtils->createOlzEntity($skill, ['onOff' => 1]);
            }
            $skill->setName($skill_name);
            $skill->clearCategories();
            foreach ($skill_category_ids as $external_category_id) {
                $internal_category_id = $this->idUtils->toInternalId($external_category_id, 'SkillCategory');
                $category = $skill_category_repo->findOneBy(['id' => $internal_category_id]);
                if (!$category) {
                    throw new Exception("No such category: {$internal_category_id}");
                }
                $skill->addCategory($category);
            }
            $skill_by_name[$skill_name] = $skill;
        }

        foreach ($skill_by_name as $skill_name => $skill) {
            $this->entityManager->persist($skill);
        }
        $this->entityManager->flush();

        $id_by_name = [];
        foreach ($skill_by_name as $skill_name => $skill) {
            $id_by_name[$skill_name] = $this->idUtils->toExternalId($skill->getId(), 'Skill');
        }
        return ['idByName' => $id_by_name];
    }
}
