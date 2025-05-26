<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Faq;

use Olz\Entity\Faq\QuestionCategory;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<QuestionCategory>
 */
class FakeQuestionCategory extends FakeEntity {
    public static function minimal(bool $fresh = false): QuestionCategory {
        return self::getFake(
            $fresh,
            function () {
                $entity = new QuestionCategory();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setPosition(0.0);
                $entity->setName('');
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): QuestionCategory {
        return self::getFake(
            $fresh,
            function () {
                $entity = new QuestionCategory();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setPosition(0.0);
                $entity->setName('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): QuestionCategory {
        return self::getFake(
            $fresh,
            function () {
                $entity = new QuestionCategory();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setPosition(3.0);
                $entity->setName('Maximal Category');
                return $entity;
            }
        );
    }
}
