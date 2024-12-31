<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @extends TypedEndpoint<
 *   array{skills: array<array{name: non-empty-string, categoryIds: array<non-empty-string>}>},
 *   array{idByName: array<non-empty-string, non-empty-string>}
 * >
 */
class RegisterSkillsEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'RegisterSkillsEndpoint';
    }

    // public function getResponseField(): FieldTypes\Field {
    //     return new FieldTypes\ObjectField(['field_structure' => [
    //         'idByName' => new FieldTypes\DictField([
    //             'item_field' => new FieldTypes\StringField([]),
    //         ]),
    //     ]]);
    // }

    // public function getRequestField(): FieldTypes\Field {
    //     $skill_field = new FieldTypes\ObjectField(['field_structure' => [
    //         'name' => new FieldTypes\StringField([]),
    //         'categoryIds' => new FieldTypes\ArrayField([
    //             'item_field' => new FieldTypes\StringField([]),
    //         ]),
    //     ]]);
    //     return new FieldTypes\ObjectField(['field_structure' => [
    //         'skills' => new FieldTypes\ArrayField([
    //             'item_field' => $skill_field,
    //         ]),
    //     ]]);
    // }

    protected function handle(mixed $input): mixed {
        $skill_repo = $this->entityManager()->getRepository(Skill::class);
        $skill_category_repo = $this->entityManager()->getRepository(SkillCategory::class);
        $skill_by_name = [];
        foreach ($input['skills'] as $input_skill) {
            $skill_name = $input_skill['name'];
            $skill_category_ids = $input_skill['categoryIds'];
            $existing_skill = $skill_repo->findOneBy(['name' => $skill_name]);
            if ($existing_skill) {
                $skill = $existing_skill;
            } else {
                $skill = new Skill();
                $this->entityUtils()->createOlzEntity($skill, ['onOff' => true]);
            }
            $skill->setName($skill_name);
            $skill->clearCategories();
            foreach ($skill_category_ids as $external_category_id) {
                $internal_category_id = $this->idUtils()->toInternalId($external_category_id, 'SkillCategory');
                $category = $skill_category_repo->findOneBy(['id' => $internal_category_id]);
                if (!$category) {
                    throw new \Exception("No such category: {$internal_category_id}");
                }
                $skill->addCategory($category);
            }
            $skill_by_name[$skill_name] = $skill;
        }

        foreach ($skill_by_name as $skill_name => $skill) {
            $this->entityManager()->persist($skill);
        }
        $this->entityManager()->flush();

        $id_by_name = [];
        foreach ($skill_by_name as $skill_name => $skill) {
            $id_by_name[$skill_name] = $this->idUtils()->toExternalId($skill->getId(), 'Skill');
        }
        return ['idByName' => $id_by_name];
    }
}
