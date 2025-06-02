<?php

namespace Olz\Entity\Termine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\PositionableInterface;
use Olz\Entity\Common\SearchableInterface;
use Olz\Repository\Termine\TerminLabelRepository;

#[ORM\Table(name: 'termin_labels')]
#[ORM\Index(name: 'ident_index', columns: ['on_off', 'ident'])]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity(repositoryClass: TerminLabelRepository::class)]
class TerminLabel extends OlzEntity implements DataStorageInterface, PositionableInterface, SearchableInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    private string $ident;

    #[ORM\Column(type: 'string', length: 127, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $icon;

    #[ORM\Column(type: 'smallfloat', nullable: false)]
    private float $position;

    /** @var Collection<int|string, Termin>&iterable<Termin> */
    #[ORM\ManyToMany(targetEntity: Termin::class, mappedBy: 'labels')]
    private Collection $termine;

    /** @var Collection<int|string, TerminTemplate>&iterable<TerminTemplate> */
    #[ORM\ManyToMany(targetEntity: TerminTemplate::class, mappedBy: 'labels')]
    private Collection $termin_templates;

    public function __construct() {
        $this->termine = new ArrayCollection();
        $this->termin_templates = new ArrayCollection();
    }

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

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $new_value): void {
        $this->details = $new_value;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $new_value): void {
        $this->icon = $new_value;
    }

    public function getPosition(): float {
        return $this->position;
    }

    public function setPosition(float $new_value): void {
        $this->position = $new_value;
    }

    // ---

    public static function getEntityNameForStorage(): string {
        return 'termin_labels';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
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
        throw new \Exception("No such TerminLabel filter: {$key}");
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('name', $query),
        );
    }
}
