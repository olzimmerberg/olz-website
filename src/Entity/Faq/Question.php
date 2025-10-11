<?php

namespace Olz\Entity\Faq;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\PositionableInterface;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Faq\QuestionRepository;

#[ORM\Table(name: 'questions')]
#[ORM\Index(name: 'ident_index', columns: ['on_off', 'ident'])]
#[ORM\Index(name: 'category_position_index', columns: ['on_off', 'category_id', 'position_within_category'])]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question extends OlzEntity implements DataStorageInterface, PositionableInterface, TestableInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    private string $ident;

    #[ORM\ManyToOne(targetEntity: QuestionCategory::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true)]
    private ?QuestionCategory $category;

    #[ORM\Column(type: 'smallfloat', nullable: false)]
    private float $position_within_category;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $question;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answer;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getIdent(): string {
        return $this->ident;
    }

    public function setIdent(string $new_value): void {
        $this->ident = $new_value;
    }

    public function getQuestion(): string {
        return $this->question;
    }

    public function setQuestion(string $new_value): void {
        $this->question = $new_value;
    }

    public function getCategory(): ?QuestionCategory {
        return $this->category;
    }

    public function setCategory(?QuestionCategory $new_value): void {
        $this->category = $new_value;
    }

    public function getPositionWithinCategory(): float {
        return $this->position_within_category;
    }

    public function setPositionWithinCategory(float $new_value): void {
        $this->position_within_category = $new_value;
    }

    public function getAnswer(): ?string {
        return $this->answer;
    }

    public function setAnswer(?string $new_value): void {
        $this->answer = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }

    public static function getEntityNameForStorage(): string {
        return 'questions';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }

    public static function getPositionFieldName(string $field): string {
        switch ($field) {
            case 'positionWithinCategory':
                return 'position_within_category';
            default: throw new \Exception("No such position field: {$field}");
        }
    }

    public function getPositionForEntityField(string $field): ?float {
        switch ($field) {
            case 'positionWithinCategory':
                return $this->getPositionWithinCategory();
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
        return "{$this->getIdent()} - {$this->getQuestion()}";
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        switch ($key) {
            case 'questionCategoryId':
                $category = new QuestionCategory();
                $category->setId(intval($value));
                return Criteria::expr()->eq('category', $category);
            default: throw new \Exception("No such Question filter: {$key}");
        }
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('ident', $query),
            Criteria::expr()->contains('question', $query),
        );
    }
}
