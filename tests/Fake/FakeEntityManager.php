<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\User;

class FakeEntityManager {
    public const AUTO_INCREMENT_ID = 270;

    public $persisted = [];
    public $removed = [];
    public $flushed = false;
    public $flushed_persisted = [];
    public $flushed_removed = [];
    public $repositories = [];

    public function __construct() {
        require_once __DIR__.'/FakeUserRepository.php';
        $this->repositories = [
            User::class => new FakeUserRepository(),
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

    public function flush() {
        $this->flushed = true;
        $this->flushed_persisted = $this->persisted;
        $this->flushed_removed = $this->removed;
    }
}
