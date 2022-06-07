<?php

use App\Entity\Quiz\Skill;
use App\Entity\Quiz\SkillLevel;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../QuizConstants.php';

class UpdateMySkillLevelsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateMySkillLevelsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'updates' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\ObjectField(['field_structure' => [
                    'change' => new FieldTypes\NumberField(['min_value' => -1.0, 'max_value' => 1.0]),
                ]]),
            ]),
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
        $now_datetime = new \DateTime($this->dateUtils->getIsoNow());

        foreach ($input['updates'] as $external_skill_id => $update) {
            $internal_skill_id = $this->idUtils->toInternalId($external_skill_id, 'Skill');
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
                $this->entityUtils->createOlzEntity($skill_level, ['onOff' => 1]);
                $skill_level->setSkill($skill);
                $skill_level->setUser($current_user);
                $skill_level->setRecordedAt($now_datetime);
                $skill_level->setValue(QuizConstants::INITIAL_SKILL_LEVEL_VALUE);
                $this->entityManager->persist($skill_level);
            }
            $value_change = $update['change'] ?? 0;
            $value = $skill_level->getValue();
            $value = $value + $value_change;
            $value = min(1, max(0, $value));
            $skill_level->setValue($value);
        }

        $this->entityManager->flush();

        return ['status' => 'OK'];
    }
}
