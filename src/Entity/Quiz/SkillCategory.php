<?php

namespace Olz\Entity\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Quiz\SkillCategoryRepository;

#[ORM\Table(name: 'quiz_categories')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Index(name: 'parent_category_index', columns: ['parent_category_id'])]
#[ORM\Entity(repositoryClass: SkillCategoryRepository::class)]
class SkillCategory extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: SkillCategory::class)]
    #[ORM\JoinColumn(name: 'parent_category_id', referencedColumnName: 'id', nullable: true)]
    private ?SkillCategory $parent_category;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    /** @var Collection<int|string, Skill>&iterable<Skill> */
    #[ORM\ManyToMany(targetEntity: Skill::class, mappedBy: 'categories')]
    private Collection $skills;

    public function __construct() {
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getParentCategory(): ?SkillCategory {
        return $this->parent_category;
    }

    public function setParentCategory(?SkillCategory $new_parent_category): void {
        $this->parent_category = $new_parent_category;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_name): void {
        $this->name = $new_name;
    }

    /** @return Collection<int|string, Skill>&iterable<Skill> */
    public function getSkills(): Collection {
        return $this->skills;
    }

    public function addSkill(Skill $new_skill): void {
        $this->skills->add($new_skill);
    }
}
