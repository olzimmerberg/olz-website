<?php

namespace Olz\Entity\Quiz;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Quiz\SkillLevelRepository;

#[ORM\Table(name: 'quiz_skill_levels')]
#[ORM\Index(name: 'user_skill_index', columns: ['user_id', 'skill_id'])]
#[ORM\Entity(repositoryClass: SkillLevelRepository::class)]
class SkillLevel extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id', nullable: true)]
    private ?Skill $skill;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $value;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $recorded_at;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_user): void {
        $this->user = $new_user;
    }

    public function getSkill(): ?Skill {
        return $this->skill;
    }

    public function setSkill(?Skill $new_skill): void {
        $this->skill = $new_skill;
    }

    public function getValue(): float {
        return $this->value;
    }

    public function setValue(float $new_value): void {
        $this->value = $new_value;
    }

    public function getRecordedAt(): \DateTime {
        return $this->recorded_at;
    }

    public function setRecordedAt(\DateTime $new_recorded_at): void {
        $this->recorded_at = $new_recorded_at;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
