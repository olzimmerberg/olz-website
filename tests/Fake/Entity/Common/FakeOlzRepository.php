<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Olz\Entity\Common\OlzEntity;

/**
 * @template T of object
 *
 * @extends EntityRepository<T>
 */
class FakeOlzRepository extends EntityRepository {
    public const MINIMAL_ID = 12;
    public const EMPTY_ID = 123;
    public const MAXIMAL_ID = 1234;
    public const NULL_ID = 9999;

    public string $olzEntityClass = OlzEntity::class;
    public string $fakeOlzEntityClass = FakeEntity::class;

    /** @var ?T */
    public ?object $entityToBeFound = null;
    public mixed $entityToBeFoundForQuery = null;

    /** @var ?array<T> */
    public ?array $entitiesToBeMatched = null;

    /** @var ?array<T> */
    public ?array $entitiesToBeFound = null;

    public function __construct(EntityManagerInterface $em) {
        // @phpstan-ignore-next-line
        parent::__construct($em, new ClassMetadata('Fake'));
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($this->entityToBeFound !== null) {
            return $this->entityToBeFound;
        }
        if ($this->entityToBeFoundForQuery !== null) {
            $fn = $this->entityToBeFoundForQuery;
            return $fn($criteria);
        }
        $class = $this->fakeOlzEntityClass;
        if ($criteria === ['id' => self::MINIMAL_ID]) {
            return $class::minimal();
        }
        if ($criteria === ['id' => self::EMPTY_ID]) {
            return $class::empty();
        }
        if ($criteria === ['id' => self::MAXIMAL_ID]) {
            return $class::maximal();
        }
        if ($criteria === ['id' => self::NULL_ID]) {
            return null;
        }
        $criteria_json = json_encode($criteria);
        throw new \Exception("Query not mocked in {$class} repo findOneBy: {$criteria_json}", 1);
    }

    /** @return FakeLazyCollection<T> */
    public function matching(Criteria $criteria): AbstractLazyCollection {
        if ($this->entitiesToBeMatched !== null) {
            return new FakeLazyCollection($this->entitiesToBeMatched);
        }
        $class = $this->fakeOlzEntityClass;
        return new FakeLazyCollection([
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ]);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderBy
     * @param mixed|null           $limit
     * @param mixed|null           $offset
     *
     * @return array<T>
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
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
