<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/../../config/doctrine.php';

class SkillRepository extends EntityRepository {
    public const ITERATION_LIMIT = 1000;

    public function getSkillsInCategories($category_ids) {
        $transitive_category_ids = array_map(
            function ($id) {
                return intval($id);
            },
            $category_ids,
        );
        $skills = [];
        for ($i = 0; $i < count($transitive_category_ids); $i++) {
            if ($i > self::ITERATION_LIMIT) {
                throw new Exception("Too many transitive child categories. Is there a loop?");
            }
            $sane_category_id = intval($transitive_category_ids[$i]);
            $category = $this->findOneBy(['id' => $sane_category_id]);
            $dql = "
                SELECT sc
                FROM SkillCategory sc
                WHERE sc.id = '{$sane_category_id}'";
            $query = $this->getEntityManager()->createQuery($dql);
            foreach ($query->getResult() as $category) {
                foreach ($category->getSkills() as $skill) {
                    $skills[] = $skill;
                }
            }
            $dql = "
                SELECT sc
                FROM SkillCategory sc
                WHERE sc.parent_category = '{$sane_category_id}'";
            $query = $this->getEntityManager()->createQuery($dql);
            $categories = $query->getResult();
            foreach ($categories as $category) {
                $transitive_category_ids[] = intval($category->getId());
            }
        }
        return $skills;
    }
}
