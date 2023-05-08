<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\NotificationSubscriptionRepository;

#[ORM\Table(name: 'notification_subscriptions')]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Index(name: 'notification_type_index', columns: ['notification_type'])]
#[ORM\Entity(repositoryClass: NotificationSubscriptionRepository::class)]
class NotificationSubscription {
    public const DELIVERY_EMAIL = 'email';
    public const DELIVERY_TELEGRAM = 'telegram';

    public const ALL_DELIVERY_TYPES = [
        self::DELIVERY_EMAIL,
        self::DELIVERY_TELEGRAM,
    ];

    public const TYPE_DAILY_SUMMARY = 'daily_summary';
    public const TYPE_DEADLINE_WARNING = 'deadline_warning';
    public const TYPE_EMAIL_CONFIG_REMINDER = 'email_config_reminder';
    public const TYPE_IMMEDIATE = 'immediate';
    public const TYPE_MONTHLY_PREVIEW = 'monthly_preview';
    public const TYPE_TELEGRAM_CONFIG_REMINDER = 'telegram_config_reminder';
    public const TYPE_WEEKLY_PREVIEW = 'weekly_preview';
    public const TYPE_WEEKLY_SUMMARY = 'weekly_summary';

    public const ALL_NOTIFICATION_TYPES = [
        self::TYPE_DAILY_SUMMARY,
        self::TYPE_DEADLINE_WARNING,
        self::TYPE_EMAIL_CONFIG_REMINDER,
        self::TYPE_IMMEDIATE,
        self::TYPE_MONTHLY_PREVIEW,
        self::TYPE_TELEGRAM_CONFIG_REMINDER,
        self::TYPE_WEEKLY_PREVIEW,
        self::TYPE_WEEKLY_SUMMARY,
    ];

    #[ORM\Column(type: 'string', nullable: false)]
    private $delivery_type;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private $user;

    #[ORM\Column(type: 'string', nullable: false)]
    private $notification_type;

    #[ORM\Column(type: 'text', nullable: true)]
    private $notification_type_args;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $created_at;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    public function __toString() {
        $label = 'NotificationSubscription(';
        $label .= "delivery_type={$this->getDeliveryType()}, ";
        $label .= "user={$this->getUser()->getId()}, ";
        $label .= "notification_type={$this->getNotificationType()}, ";
        $label .= "notification_type_args={$this->getNotificationTypeArgs()}, ";
        $label .= ')';
        return $label;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getDeliveryType() {
        return $this->delivery_type;
    }

    public function setDeliveryType($new_delivery_type) {
        $this->delivery_type = $new_delivery_type;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getNotificationType() {
        return $this->notification_type;
    }

    public function setNotificationType($new_notification_type) {
        $this->notification_type = $new_notification_type;
    }

    public function getNotificationTypeArgs() {
        return $this->notification_type_args;
    }

    public function setNotificationTypeArgs($new_notification_type_args) {
        $this->notification_type_args = $new_notification_type_args;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }
}
