<?php

namespace Olz\Entity\Service;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'downloads')]
#[ORM\Entity]
class Download extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\Column(type: 'text', nullable: true)]
    private $file_id;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($new_value) {
        $this->position = $new_value;
    }

    public function getFileId() {
        return $this->file_id;
    }

    public function setFileId($new_value) {
        $this->file_id = $new_value;
    }

    public static function getEntityNameForStorage(): string {
        return 'downloads';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
