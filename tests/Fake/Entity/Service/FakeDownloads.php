<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Download;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeDownloads extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                $entity->setId(12);
                $entity->setName('Fake Download');
                $entity->setPosition(12);
                $entity->setFileId('uploaded_file.pdf');
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                $entity->setId(123);
                $entity->setName('Fake Download');
                $entity->setPosition(123);
                $entity->setFileId('uploaded_file.pdf');
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                $entity->setId(1234);
                $entity->setName('Fake Download');
                $entity->setPosition(1234);
                $entity->setFileId('uploaded_file.pdf');
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
