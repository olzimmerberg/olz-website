<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;

/**
 * @phpstan-type OlzSkillData array{
 *   name: non-empty-string,
 *   categoryIds: array<non-empty-string>,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{skills: array<OlzSkillData>},
 *   array{idByName: array<non-empty-string, non-empty-string>}
 * >
 */
class RegisterSkillsEndpoint extends OlzTypedEndpoint {
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
            $id_by_name[$skill_name] = $this->idUtils()->toExternalId($skill->getId() ?? 0, 'Skill') ?: '-';
        }
        return ['idByName' => $id_by_name];
    }
}
