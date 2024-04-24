<?php

namespace Olz\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Quiz\SkillRepository;

#[ORM\Table(name: 'quiz_skill')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

    #[ORM\JoinTable(name: 'quiz_skills_categories')]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: SkillCategory::class, inversedBy: 'skills')]
    private $categories;

    public function __construct() {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function addCategory($new_category) {
        $this->categories->add($new_category);
    }

    public function clearCategories() {
        $this->categories->clear();
    }
}
