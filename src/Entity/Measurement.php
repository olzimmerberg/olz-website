<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\MeasurementRepository;

#[ORM\Table(name: 'measurements')]
#[ORM\Entity(repositoryClass: MeasurementRepository::class)]
class Measurement implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    private string $name;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $year;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $month;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $value;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(?int $new_value): void {
        $this->year = $new_value;
    }

    public function getMonth(): ?int {
        return $this->month;
    }

    public function setMonth(?int $new_value): void {
        $this->month = $new_value;
    }

    public function getValue(): ?float {
        return $this->value;
    }

    public function setValue(?float $new_value): void {
        $this->value = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
