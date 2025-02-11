<?php

namespace Olz\Entity\Faq;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Faq\QuestionRepository;

#[ORM\Table(name: 'questions')]
#[ORM\Index(name: 'ident_index', columns: ['on_off', 'ident'])]
#[ORM\Index(name: 'category_position_index', columns: ['on_off', 'category_id', 'position_within_category'])]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question extends OlzEntity implements DataStorageInterface {
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

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $position_within_category;

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

    public function getPositionWithinCategory(): int {
        return $this->position_within_category;
    }

    public function setPositionWithinCategory(int $new_value): void {
        $this->position_within_category = $new_value;
    }

    public function getAnswer(): ?string {
        return $this->answer;
    }

    public function setAnswer(?string $new_value): void {
        $this->answer = $new_value;
    }

    // ---

    public static function getEntityNameForStorage(): string {
        return 'questions';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
