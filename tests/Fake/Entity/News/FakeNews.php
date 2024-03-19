<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Entity\News\NewsEntry;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeNews extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $published_at = new \DateTime('2020-03-13 18:00:00');
                $entity = new NewsEntry();
                $entity->setId(12);
                $entity->setFormat('aktuell');
                $entity->setPublishedDate($published_at);
                $entity->setPublishedTime($published_at);
                $entity->setTitle("Fake title");
                $entity->setTeaser("");
                $entity->setContent("");
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $published_at = new \DateTime('0000-01-01 00:00:00');
                $entity = new NewsEntry();
                $entity->setId(123);
                $entity->setFormat('aktuell');
                $entity->setPublishedDate($published_at);
                $entity->setPublishedTime($published_at);
                $entity->setTitle("Cannot be empty");
                $entity->setTeaser("");
                $entity->setContent("");
                $entity->setTags('');
                $entity->setTermin('');
                $entity->setExternalUrl('');
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $published_at = new \DateTime('2020-03-13 18:00:00');
                $entity = new NewsEntry();
                $entity->setId(1234);
                $entity->setFormat('aktuell');
                $entity->setPublishedDate($published_at);
                $entity->setPublishedTime($published_at);
                $entity->setTitle("Fake title");
                $entity->setTeaser("Fake teaser");
                $entity->setContent("Fake content");
                $entity->setTags(' test unit ');
                $entity->setImageIds(['image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
