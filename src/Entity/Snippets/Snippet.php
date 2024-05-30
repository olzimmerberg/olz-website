<?php

namespace Olz\Entity\Snippets;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Snippets\SnippetRepository;

#[ORM\Table(name: 'snippets')]
#[ORM\Entity(repositoryClass: SnippetRepository::class)]
class Snippet extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $new_value): void {
        $this->text = $new_value;
    }

    // ---

    public static function getEntityNameForStorage(): string {
        return 'snippets';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
