<?php

namespace Olz\Entity\Service;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\PositionableInterface;
use Olz\Repository\Service\LinkRepository;

#[ORM\Table(name: 'links')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link extends OlzEntity implements PositionableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'smallfloat', nullable: false)]
    private float $position;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $url;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $new_value): void {
        $this->name = $new_value;
    }

    public function getPosition(): float {
        return $this->position;
    }

    public function setPosition(float $new_value): void {
        $this->position = $new_value;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function setUrl(?string $new_value): void {
        $this->url = $new_value;
    }

    // ---

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
        return $this->getName() ?? '---';
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        throw new \Exception("No such Link filter: {$key}");
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->contains('name', $query);
    }
}
