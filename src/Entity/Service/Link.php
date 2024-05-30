<?php

namespace Olz\Entity\Service;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'links')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity]
class Link extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $position;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $url;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $new_value): void {
        $this->name = $new_value;
    }

    public function getPosition(): int {
        return $this->position;
    }

    public function setPosition(int $new_value): void {
        $this->position = $new_value;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function setUrl(?string $new_value): void {
        $this->url = $new_value;
    }
}
