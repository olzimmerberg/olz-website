<?php

namespace App\Entity\Quiz;

use App\Entity\OlzEntity;
use App\Repository\SkillRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 * @ORM\Table(
 *     name="quiz_skill",
 *     indexes={
 *         @ORM\Index(name="name_index", columns={"name"}),
 *     },
 * )
 */
class Skill extends OlzEntity {
    /**
     * @ORM\Id @ORM\Column(type="bigint", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;
    /**
     * @ORM\ManyToMany(targetEntity="SkillCategory", inversedBy="skills")
     * @ORM\JoinTable(
     *     name="quiz_skills_categories",
     *     joinColumns={
     *         @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *     }
     * )
     */
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
