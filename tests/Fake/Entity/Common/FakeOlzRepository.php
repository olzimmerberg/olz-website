<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;

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

    /** @var ?callable(array<string, mixed>): ?T */
    public mixed $entityToBeFoundForQuery = null;

    /** @var ?array<T> */
    public ?array $entitiesToBeMatched = null;

    /** @var ?callable(array<string, mixed>): array<T> */
    public mixed $entitiesToBeFoundForQuery = null;

    public function __construct(EntityManagerInterface $em) {
        // @phpstan-ignore-next-line
        parent::__construct($em, new ClassMetadata('Fake'));
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($this->entityToBeFoundForQuery !== null) {
            $fn = $this->entityToBeFoundForQuery;
            try {
                return $fn($criteria);
            } catch (\Throwable $th) {
                // ignore
            }
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

    /** @return AbstractLazyCollection<int, T>&Selectable<int, T> */
    public function matching(Criteria $criteria): AbstractLazyCollection&Selectable {
        if ($this->entitiesToBeMatched !== null) {
            return new FakeLazyCollection($this->entitiesToBeMatched);
        }
        $class = $this->fakeOlzEntityClass;
        $all_fake_entities = [
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ];
        $matching_fake_entities = [];
        foreach ($all_fake_entities as $fake) {
            if ($this->isFakeMatchingCriteria($fake, $criteria)) {
                $matching_fake_entities[] = $fake;
            }
        }
        return new FakeLazyCollection($matching_fake_entities);
    }

    protected function isFakeMatchingCriteria(TestableInterface $fake, Criteria $criteria): bool {
        $visitor = new ExpressionEvaluationVisitor($fake);
        $criteria->getWhereExpression()?->visit($visitor);
        return $visitor->isMatching;
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
        if ($this->entitiesToBeFoundForQuery !== null) {
            $fn = $this->entitiesToBeFoundForQuery;
            try {
                return $fn($criteria);
            } catch (\Throwable $th) {
                // ignore
            }
        }
        $class = $this->fakeOlzEntityClass;
        return [
            $class::minimal(),
            $class::empty(),
            $class::maximal(),
        ];
    }
}
