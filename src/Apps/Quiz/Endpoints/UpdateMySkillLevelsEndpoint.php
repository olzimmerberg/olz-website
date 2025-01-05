<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Apps\Quiz\QuizConstants;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use PhpTypeScriptApi\Fields\FieldTypes;

class UpdateMySkillLevelsEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'UpdateMySkillLevelsEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'updates' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                    'change' => new FieldTypes\NumberField(['min_value' => -1.0, 'max_value' => 1.0]),
                ]]),
            ]),
        ]]);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $current_user = $this->authUtils()->getCurrentUser();
        $skill_repo = $this->entityManager()->getRepository(Skill::class);
        $skill_level_repo = $this->entityManager()->getRepository(SkillLevel::class);
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());

        foreach ($input['updates'] as $external_skill_id => $update) {
            $internal_skill_id = $this->idUtils()->toInternalId($external_skill_id, 'Skill');
            $skill_level = $skill_level_repo->findOneBy([
                'skill' => $internal_skill_id,
                'user' => $current_user,
            ]);
            if ($skill_level === null) {
                $skill = $skill_repo->findOneBy(['id' => $internal_skill_id]);
                if (!$skill) {
                    throw new \Exception("No such skill: {$internal_skill_id}");
                }
                $skill_level = new SkillLevel();
                $this->entityUtils()->createOlzEntity($skill_level, ['onOff' => true]);
                $skill_level->setSkill($skill);
                $skill_level->setUser($current_user);
                $skill_level->setRecordedAt($now_datetime);
                $skill_level->setValue(QuizConstants::INITIAL_SKILL_LEVEL_VALUE);
                $this->entityManager()->persist($skill_level);
            }
            $value_change = $update['change'] ?? 0;
            $value = $skill_level->getValue();
            $value = $value + $value_change;
            $value = min(1, max(0, $value));
            $skill_level->setValue($value);
        }

        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
