<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeOlzRepository extends FakeEntity {
    public const MINIMAL_ID = 12;
    public const EMPTY_ID = 123;
    public const MAXIMAL_ID = 1234;
    public const NULL_ID = 9999;

    public $fakeOlzEntityClass = FakeOlzEntity::class;

    public function findOneBy($where) {
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
}
