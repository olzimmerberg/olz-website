<?php

namespace Olz\Repository\Quiz;

use Olz\Entity\Quiz\Skill;
use Olz\Repository\Common\OlzRepository;

class SkillLevelRepository extends OlzRepository {
    public function getSkillLevelsForUserId($user_id) {
        $sane_user_id = intval($user_id);
        $dql = "
            SELECT sl
            FROM SkillLevel sl
            WHERE sl.user = '{$sane_user_id}'";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    public function getSkillLevelsForUserIdInCategories($user_id, $category_ids) {
        $sane_user_id = intval($user_id);
        $skill_repo = $this->getEntityManager()->getRepository(Skill::class);
        $skills = $skill_repo->getSkillsInCategories($category_ids);
        $skill_ids = array_map(
            function ($skill) {
                return intval($skill->getId());
            },
            $skills,
        );
        $skill_ids_sql = implode("','", $skill_ids);
        $dql = "
            SELECT sl
            FROM SkillLevel sl
            WHERE sl.user = '{$sane_user_id}' AND sl.skill IN ('{$skill_ids_sql}')";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
