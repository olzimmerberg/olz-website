<?php

namespace Olz\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\OlzEntity;
use Olz\Entity\User;
use Olz\Repository\SkillLevelRepository;

#[ORM\Table(name: 'quiz_skill_levels')]
#[ORM\Index(name: 'user_skill_index', columns: ['user_id', 'skill_id'])]
#[ORM\Entity(repositoryClass: SkillLevelRepository::class)]
class SkillLevel extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id', nullable: true)]
    private $skill;

    #[ORM\Column(type: 'float', nullable: false)]
    private $value;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $recorded_at;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getSkill() {
        return $this->skill;
    }

    public function setSkill($new_skill) {
        $this->skill = $new_skill;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($new_value) {
        $this->value = $new_value;
    }

    public function getRecordedAt() {
        return $this->recorded_at;
    }

    public function setRecordedAt($new_recorded_at) {
        $this->recorded_at = $new_recorded_at;
    }
}
