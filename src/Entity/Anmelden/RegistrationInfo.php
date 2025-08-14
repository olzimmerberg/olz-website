<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Anmelden\RegistrationInfoRepository;

#[ORM\Table(name: 'anmelden_registration_infos')]
#[ORM\Index(name: 'ident_index', columns: ['ident'])]
#[ORM\Entity(repositoryClass: RegistrationInfoRepository::class)]
class RegistrationInfo extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'registration_id', referencedColumnName: 'id', nullable: false)]
    private Registration $registration;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $index_within_registration;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $ident;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $description;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $type;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $is_optional;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $options;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getRegistration(): Registration {
        return $this->registration;
    }

    public function setRegistration(Registration $new_value): void {
        $this->registration = $new_value;
    }

    public function getIdent(): string {
        return $this->ident;
    }

    public function setIdent(string $new_value): void {
        $this->ident = $new_value;
    }

    public function getIndexWithinRegistration(): int {
        return $this->index_within_registration;
    }

    public function setIndexWithinRegistration(int $new_value): void {
        $this->index_within_registration = $new_value;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $new_value): void {
        $this->title = $new_value;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $new_value): void {
        $this->description = $new_value;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $new_value): void {
        $this->type = $new_value;
    }

    public function getIsOptional(): bool {
        return $this->is_optional;
    }

    public function setIsOptional(bool $new_value): void {
        $this->is_optional = $new_value;
    }

    public function getOptions(): string {
        return $this->options;
    }

    public function setOptions(string $new_value): void {
        $this->options = $new_value;
    }

    // ---

    public function __toString(): string {
        return "RegistrationInfo (ID: {$this->getId()})";
    }

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
