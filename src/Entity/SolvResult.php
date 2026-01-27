<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\SolvResultRepository;

#[ORM\Table(name: 'solv_results')]
#[ORM\Index(name: 'person_name_index', columns: ['person', 'name'])]
#[ORM\Index(name: 'event_index', columns: ['event'])]
#[ORM\Entity(repositoryClass: SolvResultRepository::class)]
class SolvResult implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $person;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $event;

    #[ORM\Column(type: 'string', nullable: false, length: 15)]
    private string $class;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $rank;

    #[ORM\Column(type: 'string', nullable: false, length: 31)]
    private string $name;

    #[ORM\Column(type: 'string', nullable: false, length: 3)]
    private string $birth_year;

    #[ORM\Column(type: 'string', nullable: false, length: 31)]
    private string $domicile;

    #[ORM\Column(type: 'string', nullable: false, length: 31)]
    private string $club;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $result;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $splits;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $last_control_code;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $finish_split;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $class_distance;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $class_elevation;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $class_control_count;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $class_competitor_count;

    public function __toString(): string {
        return <<<ZZZZZZZZZZ
            SolvResult(
                id:{$this->id},
                event:{$this->event},
                class:{$this->class},
                person:{$this->person},
                name:{$this->name},
                birth_year:{$this->birth_year},
                domicile:{$this->domicile},
                club:{$this->club},
            )
            ZZZZZZZZZZ;
    }

    /** @var array<string, true> */
    private array $valid_field_names = [
        'id' => true,
        'person' => true,
        'event' => true,
        'class' => true,
        'rank' => true,
        'name' => true,
        'birth_year' => true,
        'domicile' => true,
        'club' => true,
        'result' => true,
        'splits' => true,
        'finish_split' => true,
        'class_distance' => true,
        'class_elevation' => true,
        'class_control_count' => true,
        'class_competitor_count' => true,
    ];
    // PRIMARY KEY (`id`),
    // UNIQUE KEY `person` (`person`,`event`,`class`,`name`,`birth_year`,`domicile`,`club`)

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getPerson(): int {
        return $this->person;
    }

    public function setPerson(int $new_value): void {
        $this->person = $new_value;
    }

    public function getEvent(): int {
        return $this->event;
    }

    public function setEvent(int $new_value): void {
        $this->event = $new_value;
    }

    public function getClass(): string {
        return $this->class;
    }

    public function setClass(string $new_value): void {
        $this->class = $new_value;
    }

    public function getRank(): int {
        return $this->rank;
    }

    public function setRank(int $new_value): void {
        $this->rank = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getBirthYear(): string {
        return $this->birth_year;
    }

    public function setBirthYear(string $new_value): void {
        $this->birth_year = $new_value;
    }

    public function getDomicile(): string {
        return $this->domicile;
    }

    public function setDomicile(string $new_value): void {
        $this->domicile = $new_value;
    }

    public function getClub(): string {
        return $this->club;
    }

    public function setClub(string $new_value): void {
        $this->club = $new_value;
    }

    public function getResult(): int {
        return $this->result;
    }

    public function setResult(int $new_value): void {
        $this->result = $new_value;
    }

    public function getSplits(): string {
        return $this->splits;
    }

    public function setSplits(string $new_value): void {
        $this->splits = $new_value;
    }

    public function getLastControlCode(): int {
        return $this->last_control_code;
    }

    public function setLastControlCode(int $new_value): void {
        $this->last_control_code = $new_value;
    }

    public function getFinishSplit(): int {
        return $this->finish_split;
    }

    public function setFinishSplit(int $new_value): void {
        $this->finish_split = $new_value;
    }

    public function getClassDistance(): int {
        return $this->class_distance;
    }

    public function setClassDistance(int $new_value): void {
        $this->class_distance = $new_value;
    }

    public function getClassElevation(): int {
        return $this->class_elevation;
    }

    public function setClassElevation(int $new_value): void {
        $this->class_elevation = $new_value;
    }

    public function getClassControlCount(): int {
        return $this->class_control_count;
    }

    public function setClassControlCount(int $new_value): void {
        $this->class_control_count = $new_value;
    }

    public function getClassCompetitorCount(): int {
        return $this->class_competitor_count;
    }

    public function setClassCompetitorCount(int $new_value): void {
        $this->class_competitor_count = $new_value;
    }

    public function getFieldValue(string $field_name): mixed {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue(string $field_name, mixed $new_value): void {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
