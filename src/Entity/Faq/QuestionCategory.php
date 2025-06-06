<?php

namespace Olz\Entity\Faq;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\PositionableInterface;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Common\TestableInterface;

#[ORM\Table(name: 'question_categories')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity]
class QuestionCategory extends OlzEntity implements PositionableInterface, SearchableInterface, TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'smallfloat', nullable: false)]
    private float $position;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getPosition(): float {
        return $this->position;
    }

    public function setPosition(float $new_value): void {
        $this->position = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }

    public static function getPositionFieldName(string $field): string {
        switch ($field) {
            case 'position':
                return 'position';
            default: throw new \Exception("No such position field: {$field}");
        }
    }

    public function getPositionForEntityField(string $field): ?float {
        switch ($field) {
            case 'position':
                return $this->getPosition();
            default: throw new \Exception("No such position field: {$field}");
        }
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId() ?? 0;
    }

    public function getTitleForSearch(): string {
        return $this->getName();
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        throw new \Exception("No such QuestionCategory filter: {$key}");
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->contains('name', $query);
    }
}
