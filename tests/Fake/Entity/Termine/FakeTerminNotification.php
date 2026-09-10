<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminNotification;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<TerminNotification>
 */
class FakeTerminNotification extends FakeEntity {
    public static function minimal(bool $fresh = false): TerminNotification {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminNotification();
                $entity->setId(12);
                $entity->setTermin(FakeTermin::minimal());
                $entity->setFiresEarlierSeconds(0);
                $entity->setTitle("Fake title!");
                $entity->setContent(null);
                $entity->setRecipientUser(null);
                $entity->setRecipientRole(null);
                $entity->setRecipientTerminOwnerUser(false);
                $entity->setRecipientTerminOwnerRole(false);
                $entity->setRecipientTerminOrganizer(false);
                $entity->setRecipientTerminVolunteers(false);
                $entity->setRecipientTerminParticipants(false);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): TerminNotification {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminNotification();
                $entity->setId(123);
                $entity->setTermin(FakeTermin::empty());
                $entity->setFiresEarlierSeconds(0);
                $entity->setTitle("Cannot be empty");
                $entity->setContent("");
                $entity->setRecipientUser(FakeUser::empty());
                $entity->setRecipientRole(FakeRole::empty());
                $entity->setRecipientTerminOwnerUser(false);
                $entity->setRecipientTerminOwnerRole(false);
                $entity->setRecipientTerminOrganizer(false);
                $entity->setRecipientTerminVolunteers(false);
                $entity->setRecipientTerminParticipants(false);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): TerminNotification {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminNotification();
                $entity->setId(1234);
                $entity->setTermin(FakeTermin::maximal());
                $entity->setFiresEarlierSeconds(86400);
                $entity->setTitle("Fake title!");
                $entity->setContent("Fake content!");
                $entity->setRecipientUser(FakeUser::maximal());
                $entity->setRecipientRole(FakeRole::maximal());
                $entity->setRecipientTerminOwnerUser(true);
                $entity->setRecipientTerminOwnerRole(true);
                $entity->setRecipientTerminOrganizer(true);
                $entity->setRecipientTerminVolunteers(true);
                $entity->setRecipientTerminParticipants(true);
                return $entity;
            }
        );
    }
}
