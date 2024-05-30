<?php

namespace Olz\Entity\Termine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Repository\Termine\TerminLocationRepository;

#[ORM\Table(name: 'termin_locations')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Entity(repositoryClass: TerminLocationRepository::class)]
class TerminLocation extends OlzEntity implements SearchableInterface, DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 127, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $latitude;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $longitude;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_ids;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
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

    public function getLatitude(): float {
        return $this->latitude;
    }

    public function setLatitude(float $new_value): void {
        $this->latitude = $new_value;
    }

    public function getLongitude(): float {
        return $this->longitude;
    }

    public function setLongitude(float $new_value): void {
        $this->longitude = $new_value;
    }

    /** @return array<string> */
    public function getImageIds(): array {
        if ($this->image_ids == null) {
            return [];
        }
        $array = json_decode($this->image_ids, true);
        return is_array($array) ? $array : [];
    }

    /** @param array<string> $new_value */
    public function setImageIds(array $new_value): void {
        $enc_value = json_encode($new_value);
        if (!$enc_value) {
            return;
        }
        $this->image_ids = $enc_value;
    }

    // ---

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId() ?? 0;
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('name', $query),
        );
    }

    public function getTitleForSearch(): string {
        return $this->getName();
    }

    public static function getEntityNameForStorage(): string {
        return 'termin_locations';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
