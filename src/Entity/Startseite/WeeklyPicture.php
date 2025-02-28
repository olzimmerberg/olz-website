<?php

namespace Olz\Entity\Startseite;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Startseite\WeeklyPictureRepository;

#[ORM\Table(name: 'weekly_picture')]
#[ORM\Index(name: 'datum_index', columns: ['datum'])]
#[ORM\Entity(repositoryClass: WeeklyPictureRepository::class)]
class WeeklyPicture extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $datum;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_id;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getPublishedDate(): ?\DateTime {
        return $this->datum;
    }

    public function setPublishedDate(?\DateTime $new_datum): void {
        $this->datum = $new_datum;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $new_text): void {
        $this->text = $new_text;
    }

    public function getImageId(): ?string {
        return $this->image_id;
    }

    public function setImageId(?string $new_image_id): void {
        $this->image_id = $new_image_id;
    }

    // ---

    public function __toString(): string {
        return "WeeklyPicture (ID: {$this->getId()})";
    }

    public static function getEntityNameForStorage(): string {
        return 'weekly_picture';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
