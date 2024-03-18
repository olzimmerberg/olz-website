<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

class FakeOlzRepository {
    public const MINIMAL_ID = 12;
    public const EMPTY_ID = 123;
    public const MAXIMAL_ID = 1234;
    public const NULL_ID = 9999;

    public $fakeOlzEntityClass = FakeOlzEntity::class;

    public $entityToBeFound;
    public $entityToBeFoundForQuery;

    public $entitiesToBeMatched;

    public $entitiesToBeFound;

    public function findOneBy($where) {
        if ($this->entityToBeFound !== null) {
            return $this->entityToBeFound;
        }
        if ($this->entityToBeFoundForQuery !== null) {
            $fn = $this->entityToBeFoundForQuery;
            return $fn($where);
        }
        $class = $this->fakeOlzEntityClass;
        if ($where === ['id' => self::MINIMAL_ID]) {
            return $class::minimal();
        }
        if ($where === ['id' => self::EMPTY_ID]) {
            return $class::empty();
        }
        if ($where === ['id' => self::MAXIMAL_ID]) {
            return $class::maximal();
        }
        if ($where === ['id' => self::NULL_ID]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in {$class} repo findOneBy: {$where_json}", 1);
    }

    public function matching($criteria) {
        if ($this->entitiesToBeMatched !== null) {
            return $this->entitiesToBeMatched;
        }
        $class = $this->fakeOlzEntityClass;
        return [
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ];
    }

    public function findBy($query) {
        if ($this->entitiesToBeFound !== null) {
            return $this->entitiesToBeFound;
        }
        $class = $this->fakeOlzEntityClass;
        return [
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ];
    }
}
