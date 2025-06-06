<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Common\Time;

/**
 * @extends FakeEntity<TerminTemplate>
 */
class FakeTerminTemplate extends FakeEntity {
    public static function minimal(bool $fresh = false): TerminTemplate {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setStartTime(null);
                $entity->setDurationSeconds(null);
                $entity->setTitle(null);
                $entity->setText(null);
                $entity->setDeadlineEarlierSeconds(null);
                $entity->setDeadlineTime(null);
                $entity->setShouldPromote(false);
                $entity->setNewsletter(true);
                $entity->setLocation(null);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): TerminTemplate {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setStartTime(null);
                $entity->setDurationSeconds(null);
                $entity->setTitle(null);
                $entity->setText(null);
                $entity->setDeadlineEarlierSeconds(null);
                $entity->setDeadlineTime(null);
                $entity->setShouldPromote(false);
                $entity->setNewsletter(false);
                $entity->clearLabels();
                $entity->setLocation(null);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): TerminTemplate {
        return self::getFake(
            $fresh,
            function () {
                $termin_label_1 = new TerminLabel();
                $termin_label_1->setId(12341);
                $termin_label_1->setIdent('ol');
                $termin_label_2 = new TerminLabel();
                $termin_label_2->setId(12342);
                $termin_label_2->setIdent('club');
                $termin_location = new TerminLocation();
                $termin_location->setId(12341);
                $entity = new TerminTemplate();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setStartTime(new Time('09:00:00'));
                $entity->setDurationSeconds(7200);
                $entity->setTitle("Fake title");
                $entity->setText("Fake text");
                $entity->setDeadlineEarlierSeconds(86400 * 2);
                $entity->setDeadlineTime(new Time('18:00:00'));
                $entity->setShouldPromote(true);
                $entity->setNewsletter(true);
                $entity->clearLabels();
                $entity->addLabel($termin_label_1);
                $entity->addLabel($termin_label_2);
                $entity->setLocation($termin_location);
                $entity->setImageIds([
                    'image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
