<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Anmelden\RegistrationInfoRepository;

#[ORM\Table(name: 'anmelden_registration_infos')]
#[ORM\Index(name: 'ident_index', columns: ['ident'])]
#[ORM\Entity(repositoryClass: RegistrationInfoRepository::class)]
class RegistrationInfo extends OlzEntity {
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

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getRegistration(): Registration {
        return $this->registration;
    }

    public function setRegistration(Registration $new_registration): void {
        $this->registration = $new_registration;
    }

    public function getIdent(): string {
        return $this->ident;
    }

    public function setIdent(string $new_ident): void {
        $this->ident = $new_ident;
    }

    public function getIndexWithinRegistration(): int {
        return $this->index_within_registration;
    }

    public function setIndexWithinRegistration(int $new_index_within_registration): void {
        $this->index_within_registration = $new_index_within_registration;
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

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $new_type): void {
        $this->type = $new_type;
    }

    public function getIsOptional(): bool {
        return $this->is_optional;
    }

    public function setIsOptional(bool $new_is_optional): void {
        $this->is_optional = $new_is_optional;
    }

    public function getOptions(): string {
        return $this->options;
    }

    public function setOptions(string $new_options): void {
        $this->options = $new_options;
    }
}
