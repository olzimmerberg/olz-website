<?php

namespace Olz\Entity\Service;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'downloads')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity]
class Download extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $position;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $file_id;

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

    public function getPosition(): int {
        return $this->position;
    }

    public function setPosition(int $new_value): void {
        $this->position = $new_value;
    }

    public function getFileId(): ?string {
        return $this->file_id;
    }

    public function setFileId(?string $new_value): void {
        $this->file_id = $new_value;
    }

    public static function getEntityNameForStorage(): string {
        return 'downloads';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
