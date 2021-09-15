<?php

require_once __DIR__.'/../../src/model/NotificationSubscription.php';

function get_fake_notification_subscription($id, $delivery_type, $user, $notification_type, $notification_type_args, $created_at = null) {
    if ($created_at == null) {
        $created_at = new DateTime('2020-03-13 19:30:00');
    }
    $notification_subscription = new NotificationSubscription();
    $notification_subscription->setId($id);
    $notification_subscription->setDeliveryType($delivery_type);
    $notification_subscription->setUser($user);
    $notification_subscription->setNotificationType($notification_type);
    $notification_subscription->setNotificationTypeArgs($notification_type_args);
    $notification_subscription->setCreatedAt($created_at);
    return $notification_subscription;
}
