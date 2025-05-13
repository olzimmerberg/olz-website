<?php

namespace Olz\Repository\Quiz;

use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Skill>
 */
class SkillRepository extends OlzRepository {
    protected string $entityClass = Skill::class;

    public const ITERATION_LIMIT = 1000;

    /**
     * @param array<int> $category_ids
     *
     * @return array<Skill>
     */
    public function getSkillsInCategories(array $category_ids): array {
        $skill_category_class = SkillCategory::class;
        $transitive_category_ids = array_map(
            function ($id) {
                return intval($id);
            },
            $category_ids,
        );
        $skills = [];
        for ($i = 0; $i < count($transitive_category_ids); $i++) {
            if ($i > self::ITERATION_LIMIT) {
                throw new \Exception("Too many transitive child categories. Is there a loop?");
            }
            $sane_category_id = intval($transitive_category_ids[$i]);
            $category = $this->findOneBy(['id' => $sane_category_id]);
            $dql = "
                SELECT sc
                FROM {$skill_category_class} sc
                WHERE sc.id = '{$sane_category_id}'";
            $query = $this->getEntityManager()->createQuery($dql);
            foreach ($query->getResult() as $category) {
                foreach ($category->getSkills() as $skill) {
                    $skills[] = $skill;
                }
            }
            $dql = "
                SELECT sc
                FROM {$skill_category_class} sc
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
