<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Faq;

use Olz\Entity\Faq\Question;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<Question>
 */
class FakeQuestion extends FakeEntity {
    public static function minimal(bool $fresh = false): Question {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Question();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setIdent('minimal');
                $entity->setQuestion('');
                $entity->setCategory(null);
                $entity->setPositionWithinCategory(0);
                $entity->setAnswer(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Question {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Question();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setIdent('empty');
                $entity->setQuestion('');
                $entity->setCategory(FakeQuestionCategory::empty());
                $entity->setPositionWithinCategory(0);
                $entity->setAnswer('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): Question {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Question();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setIdent('maximal');
                $entity->setQuestion('Maximal Question');
                $entity->setCategory(FakeQuestionCategory::maximal());
                $entity->setPositionWithinCategory(3);
                $entity->setAnswer('Maximal Answer');
                return $entity;
            }
        );
    }
}
