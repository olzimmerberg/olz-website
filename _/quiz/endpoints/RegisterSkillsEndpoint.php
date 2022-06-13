<?php

use Olz\Api\OlzEndpoint;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use PhpTypeScriptApi\Fields\FieldTypes;

class RegisterSkillsEndpoint extends OlzEndpoint {
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
                    throw new \Exception("No such category: {$internal_category_id}");
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
