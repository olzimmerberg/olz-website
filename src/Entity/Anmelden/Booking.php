<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Anmelden\BookingRepository;

#[ORM\Table(name: 'anmelden_bookings')]
#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'registration_id', referencedColumnName: 'id', nullable: false)]
    private Registration $registration;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $form_data;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getRegistration(): Registration {
        return $this->registration;
    }

    public function setRegistration(Registration $new_registration): void {
        $this->registration = $new_registration;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_user): void {
        $this->user = $new_user;
    }

    public function getFormData(): string {
        return $this->form_data;
    }

    public function setFormData(string $new_form_data): void {
        $this->form_data = $new_form_data;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
