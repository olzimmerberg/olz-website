<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Anmelden\RegistrationRepository;

#[ORM\Table(name: 'anmelden_registrations')]
#[ORM\Index(name: 'opens_at_index', columns: ['opens_at'])]
#[ORM\Index(name: 'closes_at_index', columns: ['closes_at'])]
#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $opens_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $closes_at;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $new_title): void {
        $this->title = $new_title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $new_description): void {
        $this->description = $new_description;
    }

    public function getOpensAt(): ?\DateTime {
        return $this->opens_at;
    }

    public function setOpensAt(?\DateTime $new_opens_at): void {
        $this->opens_at = $new_opens_at;
    }

    public function getClosesAt(): ?\DateTime {
        return $this->closes_at;
    }

    public function setClosesAt(?\DateTime $new_closes_at): void {
        $this->closes_at = $new_closes_at;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
