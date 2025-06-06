<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Entity\News\NewsEntry;
use Olz\Tests\Fake\Entity\Common\Date;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Common\Time;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<NewsEntry>
 */
class FakeNews extends FakeEntity {
    public static function minimal(bool $fresh = false): NewsEntry {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsEntry();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setFormat('aktuell');
                $entity->setAuthorName(null);
                $entity->setAuthorEmail(null);
                $entity->setAuthorUser(null);
                $entity->setAuthorRole(null);
                $entity->setPublishedDate(new Date('2020-03-13'));
                $entity->setPublishedTime(new Time('18:00:00'));
                $entity->setTitle("Fake title");
                $entity->setTeaser("");
                $entity->setContent("");
                $entity->setTags('');
                $entity->setTermin(0);
                $entity->setExternalUrl(null);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): NewsEntry {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsEntry();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setFormat('aktuell');
                $entity->setAuthorName('');
                $entity->setAuthorEmail('');
                $entity->setAuthorUser(null);
                $entity->setAuthorRole(null);
                $entity->setPublishedDate(new Date('0000-01-01'));
                $entity->setPublishedTime(new Time('00:00:00'));
                $entity->setTitle("Cannot be empty");
                $entity->setTeaser("");
                $entity->setContent("");
                $entity->setTags('');
                $entity->setTermin(0);
                $entity->setExternalUrl('');
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): NewsEntry {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsEntry();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setFormat('aktuell');
                $entity->setAuthorName('Manuel');
                $entity->setAuthorEmail('manual-author@staging.olzimmerberg.ch');
                $entity->setAuthorUser(FakeUser::adminUser());
                $entity->setAuthorRole(FakeRole::adminRole());
                $entity->setPublishedDate(new Date('2020-03-13'));
                $entity->setPublishedTime(new Time('18:00:00'));
                $entity->setTitle("Fake title");
                $entity->setTeaser("Fake teaser");
                $entity->setContent("Fake content");
                $entity->setTags(' test unit ');
                $entity->setTermin(0);
                $entity->setExternalUrl('');
                $entity->setImageIds(['image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
