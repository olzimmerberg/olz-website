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
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($new_value) {
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
