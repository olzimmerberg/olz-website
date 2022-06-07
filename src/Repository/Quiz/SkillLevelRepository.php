<?php

namespace Olz\Repository\Quiz;

use Doctrine\ORM\EntityRepository;
use Olz\Entity\Skill;

class SkillLevelRepository extends EntityRepository {
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
        require_once __DIR__.'/Skill.php';
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
