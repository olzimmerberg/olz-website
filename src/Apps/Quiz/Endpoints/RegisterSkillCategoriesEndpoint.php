<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Quiz\SkillCategory;

/**
 * @phpstan-type OlzSkillCategoryData array{
 *   name: non-empty-string,
 *   parentCategoryName?: ?non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{skillCategories: array<OlzSkillCategoryData>},
 *   array{idByName: array<non-empty-string, non-empty-string>}
 * >
 */
class RegisterSkillCategoriesEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $skill_category_repo = $this->entityManager()->getRepository(SkillCategory::class);
        $category_by_name = [];
        foreach ($input['skillCategories'] as $input_category) {
            $category_name = $input_category['name'];
            $existing_category = $skill_category_repo->findOneBy(['name' => $category_name]);
            if ($existing_category) {
                $category = $existing_category;
            } else {
                $category = new SkillCategory();
                $this->entityUtils()->createOlzEntity($category, ['onOff' => true]);
            }
            $category->setName($category_name);
            $category_by_name[$category_name] = $category;
        }
        foreach ($input['skillCategories'] as $input_category) {
            $category_name = $input_category['name'];
            $parent_name = $input_category['parentCategoryName'] ?? null;
            $category = $category_by_name[$category_name];
            if ($parent_name === null) {
                $category->setParentCategory(null);
            } else {
                $parent_category = $category_by_name[$parent_name] ?? null;
                if ($parent_category === null) {
                    throw new \Exception("No such parent category: {$parent_name}");
                }
                $category->setParentCategory($parent_category);
            }
        }

        foreach ($category_by_name as $category_name => $category) {
            $this->entityManager()->persist($category);
        }
        $this->entityManager()->flush();

        $id_by_name = [];
        foreach ($category_by_name as $category_name => $category) {
            $id_by_name[$category_name] = $this->idUtils()->toExternalId($category->getId(), 'SkillCategory') ?: '-';
        }
        return ['idByName' => $id_by_name];
    }
}
