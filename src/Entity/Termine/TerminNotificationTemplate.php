<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Repository\Termine\TerminNotificationTemplateRepository;

#[ORM\Table(name: 'termin_notification_templates')]
#[ORM\Index(name: 'termin_template_index', columns: ['termin_template_id'])]
#[ORM\Entity(repositoryClass: TerminNotificationTemplateRepository::class)]
class TerminNotificationTemplate implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: TerminTemplate::class)]
    #[ORM\JoinColumn(name: 'termin_template_id', referencedColumnName: 'id', nullable: false)]
    private TerminTemplate $termin_template;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $fires_earlier_seconds;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $recipient_user;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'recipient_role_id', referencedColumnName: 'id', nullable: true)]
    private ?Role $recipient_role;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $recipient_termin_owners;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $recipient_termin_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $recipient_termin_participants;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getTerminTemplate(): TerminTemplate {
        return $this->termin_template;
    }

    public function setTerminTemplate(TerminTemplate $new_value): void {
        $this->termin_template = $new_value;
    }

    public function getFiresEarlierSeconds(): ?int {
        return $this->fires_earlier_seconds;
    }

    public function setFiresEarlierSeconds(?int $new_value): void {
        $this->fires_earlier_seconds = $new_value;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $new_value): void {
        $this->title = $new_value;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $new_value): void {
        $this->content = $new_value;
    }

    public function getRecipientUser(): ?User {
        return $this->recipient_user;
    }

    public function setRecipientUser(?User $new_value): void {
        $this->recipient_user = $new_value;
    }

    public function getRecipientRole(): ?Role {
        return $this->recipient_role;
    }

    public function setRecipientRole(?Role $new_value): void {
        $this->recipient_role = $new_value;
    }

    public function getRecipientTerminOwners(): bool {
        return $this->recipient_termin_owners;
    }

    public function setRecipientTerminOwners(bool $new_value): void {
        $this->recipient_termin_owners = $new_value;
    }

    public function getRecipientTerminVolunteers(): bool {
        return $this->recipient_termin_volunteers;
    }

    public function setRecipientTerminVolunteers(bool $new_value): void {
        $this->recipient_termin_volunteers = $new_value;
    }

    public function getRecipientTerminParticipants(): bool {
        return $this->recipient_termin_participants;
    }

    public function setRecipientTerminParticipants(bool $new_value): void {
        $this->recipient_termin_participants = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
