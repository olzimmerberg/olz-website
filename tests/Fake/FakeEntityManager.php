<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Doctrine\ORM\EntityManager;
use Olz\Entity\Roles\Role;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\User;

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
            User::class => new FakeUserRepository(),
            Role::class => new FakeRoleRepository(),
            TerminLabel::class => new FakeTerminLabelRepository(),
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
