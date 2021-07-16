<?php

class FakeEntityManager {
    const AUTO_INCREMENT_ID = 270;

    public $persisted = [];
    public $removed = [];
    public $flushed_persisted = [];
    public $flushed_removed = [];
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
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
        $this->flushed_persisted = $this->persisted;
        $this->flushed_removed = $this->removed;
    }
}
