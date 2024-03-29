<?php

namespace Olz\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\SkillCategoryRepository;

#[ORM\Table(name: 'quiz_categories')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Index(name: 'parent_category_index', columns: ['parent_category_id'])]
#[ORM\Entity(repositoryClass: SkillCategoryRepository::class)]
class SkillCategory extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity: SkillCategory::class)]
    #[ORM\JoinColumn(name: 'parent_category_id', referencedColumnName: 'id', nullable: true)]
    private $parent_category;

    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Skill::class, mappedBy: 'categories')]
    private $skills;

    public function __construct() {
        $this->skills = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getParentCategory() {
        return $this->parent_category;
    }

    public function setParentCategory($new_parent_category) {
        $this->parent_category = $new_parent_category;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getSkills() {
        return $this->skills;
    }

    public function addSkill($new_skill) {
        $this->skills->add($new_skill);
    }
}
