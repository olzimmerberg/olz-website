<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

class RegisterSkillCategoriesEndpoint extends OlzEndpoint {
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
        return 'RegisterSkillCategoriesEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'idByName' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\StringField([]),
            ]),
        ]]);
    }

    public function getRequestField() {
        $skill_category_field = new FieldTypes\ObjectField(['field_structure' => [
            'name' => new FieldTypes\StringField([]),
            'parentCategoryName' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'skillCategories' => new FieldTypes\ArrayField([
                'item_field' => $skill_category_field,
            ]),
        ]]);
    }

    protected function handle($input) {
        $skill_category_repo = $this->entityManager->getRepository(SkillCategory::class);
        $category_by_name = [];
        foreach ($input['skillCategories'] as $input_category) {
            $category_name = $input_category['name'];
            $existing_category = $skill_category_repo->findOneBy(['name' => $category_name]);
            if ($existing_category) {
                $category = $existing_category;
            } else {
                $category = new SkillCategory();
                $this->entityUtils->createOlzEntity($category, ['onOff' => 1]);
            }
            $category->setName($category_name);
            $category_by_name[$category_name] = $category;
        }
        foreach ($input['skillCategories'] as $input_category) {
            $category_name = $input_category['name'];
            $parent_name = $input_category['parentCategoryName'];
            $category = $category_by_name[$category_name];
            if ($parent_name === null) {
                $category->setParentCategory(null);
            } else {
                $parent_category = $category_by_name[$parent_name] ?? null;
                if ($parent_category === null) {
                    throw new Exception("No such parent category: {$parent_name}");
                }
                $category->setParentCategory($parent_category);
            }
        }

        foreach ($category_by_name as $category_name => $category) {
            $this->entityManager->persist($category);
        }
        $this->entityManager->flush();

        $id_by_name = [];
        foreach ($category_by_name as $category_name => $category) {
            $id_by_name[$category_name] = $this->idUtils->toExternalId($category->getId(), 'SkillCategory');
        }
        return ['idByName' => $id_by_name];
    }
}
