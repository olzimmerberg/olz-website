<?php

namespace Olz\Entity\Faq;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'question_categories')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity]
class QuestionCategory extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $position;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getPosition(): int {
        return $this->position;
    }

    public function setPosition(int $new_value): void {
        $this->position = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }
}
