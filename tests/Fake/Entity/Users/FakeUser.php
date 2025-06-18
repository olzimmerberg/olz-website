<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Users;

use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Common\Date;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Roles\FakeRole;

/**
 * @extends FakeEntity<User>
 */
class FakeUser extends FakeEntity {
    public static function minimal(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = new User();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setParentUserId(null);
                $entity->setFirstName('Required');
                $entity->setLastName('Non-empty');
                $entity->setUsername('minimal-user');
                $entity->setOldUsername(null);
                $entity->setEmail(null);
                $entity->setEmailIsVerified(false);
                $entity->setEmailVerificationToken(null);
                $entity->setPasswordHash(null);
                $entity->setPermissions('');
                $entity->setRoot(null);
                $entity->setPhone(null);
                $entity->setGender(null);
                $entity->setBirthdate(null);
                $entity->setStreet(null);
                $entity->setPostalCode(null);
                $entity->setRegion(null);
                $entity->setCity(null);
                $entity->setCountryCode(null);
                $entity->setSiCardNumber(null);
                $entity->setSolvNumber(null);
                $entity->setAhvNumber(null);
                $entity->setDressSize(null);
                $entity->setAvatarImageId(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = new User();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setParentUserId(null);
                $entity->setFirstName('Required');
                $entity->setLastName('Non-empty');
                $entity->setUsername('empty-user');
                $entity->setOldUsername(null);
                $entity->setEmail('');
                $entity->setEmailIsVerified(false);
                $entity->setEmailVerificationToken('');
                $entity->setPasswordHash('');
                $entity->setPermissions('');
                $entity->setRoot('');
                $entity->setPhone('');
                $entity->setGender('');
                $entity->setBirthdate(new Date('1970-01-01'));
                $entity->setStreet('');
                $entity->setPostalCode('');
                $entity->setRegion('');
                $entity->setCity('');
                $entity->setCountryCode('');
                $entity->setSiCardNumber('');
                $entity->setSolvNumber('');
                $entity->setAhvNumber('');
                $entity->setDressSize('');
                $entity->setAvatarImageId('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = new User();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setParentUserId(1);
                $entity->setFirstName('Maximal');
                $entity->setLastName('User');
                $entity->setUsername('maximal-user');
                $entity->setOldUsername('maximal-user-old');
                $entity->setEmail('maximal-user@staging.olzimmerberg.ch');
                $entity->setEmailIsVerified(true);
                $entity->setEmailVerificationToken('admintoken');
                $entity->setPasswordHash(md5('adm1n')); // just for test
                $entity->setPermissions('all verified_email');
                $entity->setRoot('karten');
                $entity->setPhone('+410123456');
                $entity->setGender('M');
                $entity->setBirthdate(new Date('2020-03-13'));
                $entity->setStreet('Data Hwy. 42');
                $entity->setPostalCode('19216811');
                $entity->setRegion('XX');
                $entity->setCity('Test');
                $entity->setCountryCode('CH');
                $entity->setSiCardNumber('127001');
                $entity->setSolvNumber('000ADM');
                $entity->setAhvNumber('756.9999.9999.99');
                $entity->setDressSize('3XL');
                $entity->setAvatarImageId('image__________________1.jpg');
                return $entity;
            }
        );
    }

    public static function adminUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () use ($fresh) {
                $entity = new User();
                self::populateEntityFields($entity, $fresh);
                $entity->setId(2);
                $entity->setParentUserId(null);
                $entity->setFirstName('Admin');
                $entity->setLastName('Istrator');
                $entity->setUsername('admin');
                $entity->setOldUsername('admin-old');
                $entity->setEmail('admin-user@staging.olzimmerberg.ch');
                $entity->setEmailIsVerified(true);
                $entity->setEmailVerificationToken('admintoken');
                $entity->setPasswordHash(md5('adm1n')); // just for test
                $entity->setPermissions('all verified_email');
                $entity->setRoot('karten');
                $entity->setPhone('+410123456');
                $entity->setGender('M');
                $entity->setBirthdate(new Date('2000-01-01'));
                $entity->setStreet('Data Hwy. 42');
                $entity->setPostalCode('19216811');
                $entity->setRegion('XX');
                $entity->setCity('Test');
                $entity->setCountryCode('CH');
                $entity->setSiCardNumber('127001');
                $entity->setSolvNumber('000ADM');
                $entity->setAhvNumber('756.1337.1337.42');
                $entity->setDressSize('M');
                $entity->setAvatarImageId(null);
                return $entity;
            },
            function ($entity) {
                $entity->addRole(FakeRole::adminRole());
            }
        );
    }

    public static function vorstandUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () use ($fresh) {
                $entity = new User();
                self::populateEntityFields($entity, $fresh);
                $entity->setId(3);
                $entity->setFirstName('Vorstand');
                $entity->setLastName('Mitglied');
                $entity->setUsername('vorstand');
                $entity->setEmail('vorstand-user@staging.olzimmerberg.ch');
                $entity->setParentUserId(null);
                $entity->setPasswordHash(md5('v0r57and')); // just for test
                $entity->setPermissions('aktuell ftp vorstand_user');
                $entity->setRoot('vorstand');
                $entity->setAvatarImageId('oyLeyPTaCfmadcm5ShEJ236e.jpg');
                return $entity;
            },
            function ($entity) {
                $entity->addRole(FakeRole::vorstandRole());
            }
        );
    }

    public static function parentUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () use ($fresh) {
                $entity = new User();
                self::populateEntityFields($entity, $fresh);
                $entity->setId(4);
                $entity->setFirstName('Eltern');
                $entity->setLastName('Teil');
                $entity->setUsername('parent');
                $entity->setEmail('parent-user@staging.olzimmerberg.ch');
                $entity->setParentUserId(null);
                $entity->setPasswordHash(md5('par3n7')); // just for test
                $entity->setPermissions('parent');
                $entity->setRoot('parent');
                return $entity;
            }
        );
    }

    public static function child1User(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = new User();
                FakeOlzEntity::maximal($entity);
                $entity->setId(5);
                $entity->setFirstName('Kind');
                $entity->setLastName('Eins');
                $entity->setUsername('child1');
                $entity->setEmail('child1-user@staging.olzimmerberg.ch');
                $entity->setParentUserId(4);
                $entity->setPasswordHash(null);
                $entity->setPermissions('child1');
                $entity->setRoot('child1');
                return $entity;
            }
        );
    }

    public static function child2User(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () use ($fresh) {
                $entity = new User();
                self::populateEntityFields($entity, $fresh);
                $entity->setId(6);
                $entity->setFirstName('Kind');
                $entity->setLastName('Zwei');
                $entity->setUsername('child2');
                $entity->setEmail('child2-user@staging.olzimmerberg.ch');
                $entity->setParentUserId(4);
                $entity->setPasswordHash('');
                $entity->setPermissions('child2');
                $entity->setRoot('child2');
                return $entity;
            }
        );
    }

    public static function defaultUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () use ($fresh) {
                $entity = new User();
                self::populateEntityFields($entity, $fresh);
                $entity->setId(1);
                $entity->setFirstName('Default');
                $entity->setLastName('User');
                $entity->setUsername('default');
                $entity->setEmail('default-user@staging.olzimmerberg.ch');
                $entity->setEmailIsVerified(false);
                $entity->setEmailVerificationToken('defaulttoken');
                $entity->setPasswordHash(md5('u53r')); // just for test
                $entity->setPermissions('default');
                $entity->setRoot(null);
                $entity->setParentUserId(2);
                $entity->setPhone('+0815');
                $entity->setGender('F');
                $entity->setBirthdate(new Date('1970-01-01'));
                $entity->setStreet('Hauptstrasse 1');
                $entity->setPostalCode('0815');
                $entity->setRegion('XX');
                $entity->setCity('Muster');
                $entity->setCountryCode('CH');
                $entity->setSiCardNumber('8150815');
                $entity->setSolvNumber('D3F4UL7');
                $entity->setAhvNumber('756.1234.1234.12');
                $entity->setDressSize('M');
                return $entity;
            }
        );
    }

    public static function noAccessUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultUser(true);
                $entity->setPermissions('ftp');
                return $entity;
            }
        );
    }

    public static function specificAccessUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultUser(true);
                $entity->setPermissions('test');
                return $entity;
            }
        );
    }

    public static function provokeErrorUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultUser(true);
                $entity->setId(666);
                return $entity;
            }
        );
    }

    public static function noTelegramLinkUser(bool $fresh = false): User {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultUser(true);
                $entity->setId(404);
                return $entity;
            }
        );
    }

    protected static function populateEntityFields(User $entity, bool $fresh = false): void {
        $entity->setOnOff(1);
        $entity->setOwnerUser($fresh ? null : $entity);
        $entity->setOwnerRole(null);
        $entity->setCreatedAt(new \DateTime('2006-01-13 18:43:36'));
        $entity->setCreatedByUser($fresh ? null : $entity);
        $entity->setLastModifiedAt(new \DateTime('2020-03-13 18:43:36'));
        $entity->setLastModifiedByUser($fresh ? null : $entity);
    }
}
