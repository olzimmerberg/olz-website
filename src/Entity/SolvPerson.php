<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\SolvPersonRepository;

#[ORM\Table(name: 'solv_people')]
#[ORM\Index(name: 'same_as_index', columns: ['same_as'])]
#[ORM\Entity(repositoryClass: SolvPersonRepository::class)]
class SolvPerson {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $same_as;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $birth_year;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $domicile;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $member;

    /** @var array<string, true> */
    private array $valid_field_names = [
        'id' => true,
        'same_as' => true,
        'name' => true,
        'birth_year' => true,
        'domicile' => true,
        'member' => true,
    ];
    // PRIMARY KEY (`id`)

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getSameAs(): ?int {
        return $this->same_as;
    }

    public function setSameAs(?int $new_same_as): void {
        $this->same_as = $new_same_as;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_name): void {
        $this->name = $new_name;
    }

    public function getBirthYear(): string {
        return $this->birth_year;
    }

    public function setBirthYear(string $new_birth_year): void {
        $this->birth_year = $new_birth_year;
    }

    public function getDomicile(): string {
        return $this->domicile;
    }

    public function setDomicile(string $new_domicile): void {
        $this->domicile = $new_domicile;
    }

    public function getMember(): int {
        return $this->member;
    }

    public function setMember(int $new_member): void {
        $this->member = $new_member;
    }

    public function getFieldValue(string $field_name): mixed {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue(string $field_name, mixed $new_field_value): void {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_field_value;
    }
}
