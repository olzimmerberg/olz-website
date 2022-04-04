<?php

use Doctrine\ORM\Mapping as ORM;

require_once __DIR__.'/../../model/OlzEntity.php';

/**
 * @ORM\Entity(repositoryClass="BookingRepository")
 * @ORM\Table(
 *     name="anmelden_bookings",
 * )
 */
class Booking extends OlzEntity {
    /**
     * @ORM\Id @ORM\Column(type="bigint", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false)
     */
    private $registration;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $form_data;

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

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getFormData() {
        return $this->form_data;
    }

    public function setFormData($new_form_data) {
        $this->form_data = $new_form_data;
    }
}
