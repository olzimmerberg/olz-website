<?php

namespace Olz\Entity\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Quiz\SkillRepository;

#[ORM\Table(name: 'quiz_skill')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    /** @var Collection<int|string, SkillCategory>&iterable<SkillCategory> */
    #[ORM\JoinTable(name: 'quiz_skills_categories')]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: SkillCategory::class, inversedBy: 'skills')]
    private Collection $categories;

    public function __construct() {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_name): void {
        $this->name = $new_name;
    }

    /** @return Collection<int|string, SkillCategory>&iterable<SkillCategory> */
    public function getCategories(): Collection {
        return $this->categories;
    }

    public function addCategory(SkillCategory $new_category): void {
        $this->categories->add($new_category);
    }

    public function clearCategories(): void {
        $this->categories->clear();
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
