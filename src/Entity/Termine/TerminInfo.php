<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Termine\TerminInfoRepository;

#[ORM\Table(name: 'termin_infos')]
#[ORM\Index(name: 'termin_language_index', columns: ['termin_id', 'language', 'index'])]
#[ORM\Entity(repositoryClass: TerminInfoRepository::class)]
class TerminInfo implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: Termin::class)]
    #[ORM\JoinColumn(name: 'termin_id', referencedColumnName: 'id', nullable: false)]
    private Termin $termin;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private ?string $language;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $index;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getTermin(): Termin {
        return $this->termin;
    }

    public function setTermin(Termin $new_value): void {
        $this->termin = $new_value;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(?string $new_value): void {
        $this->language = $new_value;
    }

    public function getIndex(): int {
        return $this->index;
    }

    public function setIndex(int $new_value): void {
        $this->index = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $new_value): void {
        $this->content = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
