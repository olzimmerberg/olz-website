<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Apps\Quiz\QuizConstants;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\GeneralUtilsTrait;
use Olz\Utils\IdUtilsTrait;

/**
 * Note: `value` must be between 0.0 and 1.0.
 *
 * @extends OlzTypedEndpoint<
 *   array{skillFilter?: ?(
 *     array{categoryIdIn: array<non-empty-string>}
 *   )},
 *   array<string, array{value: float}>,
 * >
 */
class GetMySkillLevelsEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use EntityManagerTrait;
    use GeneralUtilsTrait;
    use IdUtilsTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $current_user_id = $this->authUtils()->getCurrentUser()?->getId();
        $this->generalUtils()->checkNotNull($current_user_id, "No current user ID");
        $skill_repo = $this->entityManager()->getRepository(Skill::class);
        $skill_level_repo = $this->entityManager()->getRepository(SkillLevel::class);

        $external_category_ids = $input['skillFilter']['categoryIdIn'] ?? null;

        if ($external_category_ids === null) {
            $skills = $skill_repo->findAll();
            $skill_levels = $skill_level_repo->getSkillLevelsForUserId(
                $current_user_id
            );
        } else {
            $internal_category_ids = array_map(
                function ($id) {
                    return $this->idUtils()->toInternalId($id, 'SkillCategory');
                },
                $external_category_ids,
            );
            $skills = $skill_repo->getSkillsInCategories($internal_category_ids);
            $skill_levels = $skill_level_repo->getSkillLevelsForUserIdInCategories(
                $current_user_id,
                $internal_category_ids
            );
        }

        $skill_level_by_skill_id = [];
        foreach ($skills as $skill) {
            $internal_skill_id = $skill->getId();
            $external_skill_id = $this->idUtils()->toExternalId($internal_skill_id ?? 0, 'Skill');
            $value = QuizConstants::INITIAL_SKILL_LEVEL_VALUE;
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }
        foreach ($skill_levels as $skill_level) {
            $internal_skill_id = $skill_level->getSkill()?->getId();
            $external_skill_id = $this->idUtils()->toExternalId($internal_skill_id ?? 0, 'Skill');
            $value = $skill_level->getValue();
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }

        return $skill_level_by_skill_id;
    }
}
