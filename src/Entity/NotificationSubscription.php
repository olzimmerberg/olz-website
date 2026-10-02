<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Constants\NotificationDeliveryType;
use Olz\Constants\NotificationType;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\NotificationSubscriptionRepository;

#[ORM\Table(name: 'notification_subscriptions')]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Index(name: 'notification_type_index', columns: ['notification_type'])]
#[ORM\Entity(repositoryClass: NotificationSubscriptionRepository::class)]
class NotificationSubscription implements TestableInterface {
    #[ORM\Column(nullable: false)]
    private NotificationDeliveryType $delivery_type;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(nullable: false)]
    private NotificationType $notification_type;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notification_type_args;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $created_at;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    public function __toString() {
        $label = 'NotificationSubscription(';
        $label .= "delivery_type={$this->getDeliveryType()->value}, ";
        $label .= "user={$this->getUser()->getId()}, ";
        $label .= "notification_type={$this->getNotificationType()->value}, ";
        $label .= "notification_type_args={$this->getNotificationTypeArgs()}, ";
        $label .= ')';
        return $label;
    }

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getDeliveryType(): NotificationDeliveryType {
        return $this->delivery_type;
    }

    public function setDeliveryType(NotificationDeliveryType $new_delivery_type): void {
        $this->delivery_type = $new_delivery_type;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $new_user): void {
        $this->user = $new_user;
    }

    public function getNotificationType(): NotificationType {
        return $this->notification_type;
    }

    public function setNotificationType(NotificationType $new_notification_type): void {
        $this->notification_type = $new_notification_type;
    }

    public function getNotificationTypeArgs(): ?string {
        return $this->notification_type_args;
    }

    public function setNotificationTypeArgs(?string $new_notification_type_args): void {
        $this->notification_type_args = $new_notification_type_args;
    }

    public function getCreatedAt(): \DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $new_created_at): void {
        $this->created_at = $new_created_at;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
