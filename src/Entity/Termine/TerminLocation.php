<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Repository\Termine\TerminLocationRepository;

#[ORM\Table(name: 'termin_locations')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Entity(repositoryClass: TerminLocationRepository::class)]
class TerminLocation extends OlzEntity implements SearchableInterface, DataStorageInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', length: 127, nullable: false)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $details;

    #[ORM\Column(type: 'float', nullable: false)]
    private $latitude;

    #[ORM\Column(type: 'float', nullable: false)]
    private $longitude;

    #[ORM\Column(type: 'text', nullable: true)]
    private $image_ids;

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

    public function getDetails() {
        return $this->details;
    }

    public function setDetails($new_value) {
        $this->details = $new_value;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($new_value) {
        $this->latitude = $new_value;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($new_value) {
        $this->longitude = $new_value;
    }

    public function getImageIds() {
        if ($this->image_ids == null) {
            return null;
        }
        return json_decode($this->image_ids, true);
    }

    public function setImageIds($new_value) {
        $this->image_ids = json_encode($new_value);
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId();
    }

    public static function getFieldNamesForSearch(): array {
        return ['name'];
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
