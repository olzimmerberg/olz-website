<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<NotificationSubscription>
 */
class FakeNotificationSubscriptionRepository extends FakeOlzRepository {
    public string $olzEntityClass = NotificationSubscription::class;
    public string $fakeOlzEntityClass = FakeNotificationSubscription::class;

    /** @return array<NotificationSubscription> */
    public function findAll(): array {
        return [
            FakeNotificationSubscription::subscription1(),
            FakeNotificationSubscription::subscription2(),
            FakeNotificationSubscription::subscription3(),
            FakeNotificationSubscription::subscription4(),
            FakeNotificationSubscription::subscription5(),
            FakeNotificationSubscription::subscription6(),
            FakeNotificationSubscription::subscription7(),
            FakeNotificationSubscription::subscription8(),
            FakeNotificationSubscription::subscription9(),
            FakeNotificationSubscription::subscription10(),
            FakeNotificationSubscription::subscription11(),
            FakeNotificationSubscription::subscription12(),
            FakeNotificationSubscription::subscription13(),
            FakeNotificationSubscription::subscription14(),
            FakeNotificationSubscription::subscription15(),
            FakeNotificationSubscription::subscription16(),
            FakeNotificationSubscription::subscription17(),
            FakeNotificationSubscription::subscription18(),
            FakeNotificationSubscription::subscription19(),
            FakeNotificationSubscription::subscription20(),
            FakeNotificationSubscription::subscription21(),
            FakeNotificationSubscription::subscription22(),
            FakeNotificationSubscription::subscription23(),
        ];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        $found = $this->findBy($criteria, $orderBy, 2, 0);
        return match (count($found)) {
            0 => null,
            1 => $found[0],
            default => throw new \Exception("more than one result for '".json_encode($criteria)."'"),
        };
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderBy
     * @param mixed|null           $limit
     * @param mixed|null           $offset
     *
     * @return array<NotificationSubscription>
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
        $filtered = $this->findAll();
        foreach ($criteria as $field => $filter_value) {
            $new_filtered = [];
            foreach ($filtered as $item) {
                $field_value = $item->testOnlyGetField($field);
                $is_same = $field_value === $filter_value;
                if ($this->isDbObject($field_value)) {
                    $filter_id = $this->isDbObject($filter_value) ? $filter_value->getId() : $filter_value;
                    $is_same = ($field_value->getId() === $filter_id);
                }
                if ($is_same) {
                    $new_filtered[] = $item;
                }
            }
            $filtered = $new_filtered;
        }
        return $filtered;
    }

    protected function isDbObject(mixed $object): bool {
        return is_object($object) && method_exists($object, 'getId');
    }
}
