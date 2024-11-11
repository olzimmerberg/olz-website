<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<Termin>
 */
class FakeTermin extends FakeEntity {
    public static function minimal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Termin();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setFromTemplate(null);
                $entity->setStartDate(new \DateTime('2020-03-13'));
                $entity->setStartTime(null);
                $entity->setEndDate(null);
                $entity->setEndTime(null);
                $entity->setTitle("Fake title");
                $entity->setText("");
                $entity->setLocation(null);
                $entity->setCoordinateX(null);
                $entity->setCoordinateY(null);
                $entity->setDeadline(null);
                $entity->setSolvId(null);
                $entity->setGo2olId(null);
                $entity->setNewsletter(false);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Termin();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setFromTemplate(null);
                $entity->setStartDate(new \DateTime('0000-01-01'));
                $entity->setStartTime(new \DateTime('00:00:00'));
                $entity->setEndDate(new \DateTime('0000-01-01'));
                $entity->setEndTime(new \DateTime('00:00:00'));
                $entity->setTitle("Cannot be empty");
                $entity->setText("");
                $entity->clearLabels();
                $entity->setLocation(null);
                $entity->setCoordinateX(0);
                $entity->setCoordinateY(0);
                $entity->setDeadline(new \DateTime('0000-01-01 00:00:00'));
                $entity->setSolvId(0);
                $entity->setGo2olId('');
                $entity->setNewsletter(false);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $termin_template = new TerminTemplate();
                $termin_template->setId(12341);
                $termin_label_1 = new TerminLabel();
                $termin_label_1->setId(12341);
                $termin_label_1->setIdent('training');
                $termin_label_2 = new TerminLabel();
                $termin_label_2->setId(12342);
                $termin_label_2->setIdent('weekends');
                $termin_location = new TerminLocation();
                $termin_location->setId(12341);
                $entity = new Termin();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setFromTemplate($termin_template);
                $entity->setStartDate(new \DateTime('2020-03-13'));
                $entity->setStartTime(new \DateTime('19:30:00'));
                $entity->setEndDate(new \DateTime('2020-03-16'));
                $entity->setEndTime(new \DateTime('12:00:00'));
                $entity->setTitle("Fake title");
                $entity->setText("Fake content");
                $entity->clearLabels();
                $entity->addLabel($termin_label_1);
                $entity->addLabel($termin_label_2);
                $entity->setLocation($termin_location);
                $entity->setCoordinateX(684835);
                $entity->setCoordinateY(237021);
                $entity->setDeadline(new \DateTime('2020-03-13 18:00:00'));
                $entity->setSolvId(11012);
                $entity->setGo2olId('deprecated');
                $entity->setNewsletter(true);
                $entity->setImageIds(['image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
