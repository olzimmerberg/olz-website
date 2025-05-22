<?php

namespace Olz\Apps\Quiz\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Apps\Quiz\QuizConstants;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Utils\AuthUtilsTrait;
use Olz\Utils\DateUtilsTrait;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use Olz\Utils\IdUtilsTrait;

/**
 * Note: `change` must be between -1.0 and 1.0.
 *
 * @extends OlzTypedEndpoint<
 *   array{updates: array<non-empty-string, array{change: float}>},
 *   array{status: 'OK'|'ERROR'}
 * >
 */
class UpdateMySkillLevelsEndpoint extends OlzTypedEndpoint {
    use AuthUtilsTrait;
    use DateUtilsTrait;
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use IdUtilsTrait;

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
            $value_change = $update['change'];
            $value = $skill_level->getValue();
            $value = $value + $value_change;
            $value = min(1, max(0, $value));
            $skill_level->setValue($value);
        }

        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
