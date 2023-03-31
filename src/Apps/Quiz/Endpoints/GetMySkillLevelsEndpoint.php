<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Quiz\QuizConstants;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class GetMySkillLevelsEndpoint extends OlzEndpoint {
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
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $current_user = $this->authUtils()->getCurrentUser();
        $skill_repo = $this->entityManager()->getRepository(Skill::class);
        $skill_level_repo = $this->entityManager()->getRepository(SkillLevel::class);

        $external_category_ids = $input['skillFilter']['categoryIdIn'] ?? null;

        if ($external_category_ids === null) {
            $skills = $skill_repo->findAll();
            $skill_levels = $skill_level_repo->getSkillLevelsForUserId(
                $current_user->getId());
        } else {
            $internal_category_ids = array_map(
                function ($id) {
                    return $this->idUtils()->toInternalId($id, 'SkillCategory');
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
            $external_skill_id = $this->idUtils()->toExternalId($internal_skill_id, 'Skill');
            $value = QuizConstants::INITIAL_SKILL_LEVEL_VALUE;
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }
        foreach ($skill_levels as $skill_level) {
            $internal_skill_id = $skill_level->getSkill()->getId();
            $external_skill_id = $this->idUtils()->toExternalId($internal_skill_id, 'Skill');
            $value = $skill_level->getValue();
            $skill_level_by_skill_id[$external_skill_id] = ['value' => $value];
        }

        return $skill_level_by_skill_id;
    }
}
