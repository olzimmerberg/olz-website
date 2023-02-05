<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\OlzEntity;
use Olz\Repository\RegistrationInfoRepository;

/**
 * @ORM\Entity(repositoryClass=RegistrationInfoRepository::class)
 *
 * @ORM\Table(
 *     name="anmelden_registration_infos",
 *     indexes={@ORM\Index(name="ident_index", columns={"ident"})},
 * )
 */
class RegistrationInfo extends OlzEntity {
    /**
     * @ORM\Id @ORM\Column(type="bigint", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Registration")
     *
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false)
     */
    private $registration;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $index_within_registration;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $ident;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $title;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_optional;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $options;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getRegistration() {
        return $this->registration;
    }

    public function setRegistration($new_registration) {
        $this->registration = $new_registration;
    }

    public function getIdent() {
        return $this->ident;
    }

    public function setIdent($new_ident) {
        $this->ident = $new_ident;
    }

    public function getIndexWithinRegistration() {
        return $this->index_within_registration;
    }

    public function setIndexWithinRegistration($new_index_within_registration) {
        $this->index_within_registration = $new_index_within_registration;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_title) {
        $this->title = $new_title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($new_description) {
        $this->description = $new_description;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($new_type) {
        $this->type = $new_type;
    }

    public function getIsOptional() {
        return $this->is_optional;
    }

    public function setIsOptional($new_is_optional) {
        $this->is_optional = $new_is_optional;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($new_options) {
        $this->options = $new_options;
    }
}
