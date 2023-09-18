<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Repository\Termine\TerminNotificationTemplateRepository;

#[ORM\Table(name: 'termin_notification_templates')]
#[ORM\Index(name: 'termin_template_index', columns: ['termin_template_id'])]
#[ORM\Entity(repositoryClass: TerminNotificationTemplateRepository::class)]
class TerminNotificationTemplate {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity: TerminTemplate::class)]
    #[ORM\JoinColumn(name: 'termin_template_id', referencedColumnName: 'id', nullable: false)]
    private $termin_template;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $fires_earlier_seconds;

    #[ORM\Column(type: 'text', nullable: false)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_user_id', referencedColumnName: 'id', nullable: true)]
    private $recipient_user;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'recipient_role_id', referencedColumnName: 'id', nullable: true)]
    private $recipient_role;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private $recipient_termin_owners;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private $recipient_termin_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private $recipient_termin_participants;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getTerminTemplate() {
        return $this->termin_template;
    }

    public function setTerminTemplate(TerminTemplate $new_value) {
        $this->termin_template = $new_value;
    }

    public function getFiresEarlierSeconds() {
        return $this->fires_earlier_seconds;
    }

    public function setFiresEarlierSeconds($new_value) {
        $this->fires_earlier_seconds = $new_value;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_value) {
        $this->title = $new_value;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($new_value) {
        $this->content = $new_value;
    }

    public function getRecipientUser() {
        return $this->recipient_user;
    }

    public function setRecipientUser(User $new_value) {
        $this->recipient_user = $new_value;
    }

    public function getRecipientRole() {
        return $this->recipient_role;
    }

    public function setRecipientRole(Role $new_value) {
        $this->recipient_role = $new_value;
    }

    public function getRecipientTerminOwners() {
        return $this->recipient_termin_owners;
    }

    public function setRecipientTerminOwners($new_value) {
        $this->recipient_termin_owners = $new_value;
    }

    public function getRecipientTerminVolunteers() {
        return $this->recipient_termin_volunteers;
    }

    public function setRecipientTerminVolunteers($new_value) {
        $this->recipient_termin_volunteers = $new_value;
    }

    public function getRecipientTerminParticipants() {
        return $this->recipient_termin_participants;
    }

    public function setRecipientTerminParticipants($new_value) {
        $this->recipient_termin_participants = $new_value;
    }
}
