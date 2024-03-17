<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Doctrine\ORM\EntityManager;
use Olz\Entity\Karten\Karte;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Throttling;
use Olz\Entity\User;
use Olz\Tests\Fake\Entity\FakeThrottlingRepository;
use Olz\Tests\Fake\Entity\FakeUserRepository;
use Olz\Tests\Fake\Entity\Karten\FakeKarteRepository;
use Olz\Tests\Fake\Entity\News\FakeNewsRepository;
use Olz\Tests\Fake\Entity\Roles\FakeRoleRepository;
use Olz\Tests\Fake\Entity\Service\FakeDownloadRepository;
use Olz\Tests\Fake\Entity\Service\FakeLinkRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminLabelRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminLocationRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminTemplateRepository;

class FakeEntityManager extends EntityManager {
    public const AUTO_INCREMENT_ID = 270;

    public $persisted = [];
    public $removed = [];
    public $flushed = false;
    public $flushed_persisted = [];
    public $flushed_removed = [];
    public $repositories = [];

    public function __construct() {
        $this->repositories = [
            Download::class => new FakeDownloadRepository(),
            Karte::class => new FakeKarteRepository(),
            Link::class => new FakeLinkRepository(),
            NewsEntry::class => new FakeNewsRepository(),
            User::class => new FakeUserRepository(),
            Role::class => new FakeRoleRepository(),
            Termin::class => new FakeTerminRepository(),
            TerminLabel::class => new FakeTerminLabelRepository(),
            TerminLocation::class => new FakeTerminLocationRepository(),
            TerminTemplate::class => new FakeTerminTemplateRepository(),
            Throttling::class => new FakeThrottlingRepository(),
        ];
    }

    public function getRepository($class) {
        $repo = $this->repositories[$class] ?? null;
        if (!$repo) {
            throw new \Exception("Repository was not mocked: {$class}");
        }
        return $repo;
    }

    public function persist($object) {
        if (method_exists($object, 'getId')) {
            // Simulate SQL auto-increment.
            if ($object->getId() === null) {
                $object->setId(FakeEntityManager::AUTO_INCREMENT_ID);
            }
        }
        $this->persisted[] = $object;
    }

    public function remove($object) {
        $this->removed[] = $object;
    }

    public function flush($entity = null) {
        $this->flushed = true;
        $this->flushed_persisted = $this->persisted;
        $this->flushed_removed = $this->removed;
    }
}
