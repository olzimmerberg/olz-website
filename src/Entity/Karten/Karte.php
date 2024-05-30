<?php

namespace Olz\Entity\Karten;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Karten\KartenRepository;

#[ORM\Table(name: 'karten')]
#[ORM\Index(name: 'typ_index', columns: ['on_off', 'typ'])]
#[ORM\Entity(repositoryClass: KartenRepository::class)]
class Karte extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $kartennr;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    // @deprecated
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $center_x;

    // @deprecated
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $center_y;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $jahr;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $massstab;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ort;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $zoom;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $typ;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $vorschau;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getKartenNr(): ?int {
        return $this->kartennr;
    }

    public function setKartenNr(?int $new_value): void {
        $this->kartennr = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getCenterX(): ?int {
        return $this->center_x;
    }

    public function setCenterX(?int $new_value): void {
        $this->center_x = $new_value;
    }

    public function getCenterY(): ?int {
        return $this->center_y;
    }

    public function setCenterY(?int $new_value): void {
        $this->center_y = $new_value;
    }

    public function getLatitude(): ?float {
        return $this->latitude;
    }

    public function setLatitude(?float $new_value): void {
        $this->latitude = $new_value;
    }

    public function getLongitude(): ?float {
        return $this->longitude;
    }

    public function setLongitude(?float $new_value): void {
        $this->longitude = $new_value;
    }

    public function getYear(): ?string {
        return $this->jahr;
    }

    public function setYear(?string $new_value): void {
        $this->jahr = $new_value;
    }

    public function getScale(): ?string {
        return $this->massstab;
    }

    public function setScale(?string $new_value): void {
        $this->massstab = $new_value;
    }

    public function getPlace(): ?string {
        return $this->ort;
    }

    public function setPlace(?string $new_value): void {
        $this->ort = $new_value;
    }

    public function getZoom(): ?int {
        return $this->zoom;
    }

    public function setZoom(?int $new_value): void {
        $this->zoom = $new_value;
    }

    public function getKind(): ?string {
        return $this->typ;
    }

    public function setKind(?string $new_value): void {
        $this->typ = $new_value;
    }

    public function getPreviewImageId(): ?string {
        return $this->vorschau;
    }

    public function setPreviewImageId(?string $new_value): void {
        $this->vorschau = $new_value;
    }

    public static function getEntityNameForStorage(): string {
        return 'karten';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
