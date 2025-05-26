<?php

namespace Olz\Api\Endpoints;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\PositionableInterface;
use PhpTypeScriptApi\HttpError;

/**
 * TODO: Support key-of<self::SUPPORTED_ENTITY_FIELDS> in php-typescript-api.
 *
 * @phpstan-type OlzEntityPositionResult array{
 *   id: int, // TODO: int<1, max>
 *   position: ?float,
 *   title: non-empty-string,
 * }
 *
 * @phpstan-import-type OlzSearchableEntityType from SearchEntitiesEndpoint
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     entityType: OlzSearchableEntityType,
 *     entityField: non-empty-string,
 *     id?: ?int<1, max>,
 *     position?: ?float,
 *     filter?: ?array<non-empty-string, string>,
 *   },
 *   array{
 *     before?: ?OlzEntityPositionResult,
 *     this?: ?OlzEntityPositionResult,
 *     after?: ?OlzEntityPositionResult,
 *   }
 * >
 */
class GetEntitiesAroundPositionEndpoint extends OlzTypedEndpoint {
    public const FLOAT_EPSILON = 1e-6; // PHP_FLOAT_EPSILON does not work for doctrine...

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(SearchEntitiesEndpoint::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity_type = $input['entityType'];
        $entity_class = SearchEntitiesEndpoint::SUPPORTED_ENTITY_TYPES[$entity_type];
        $entity_field = $input['entityField'];
        try {
            $position_field = $entity_class::getPositionFieldName($entity_field);
        } catch (\Throwable $th) {
            throw new HttpError(400, "Invalid position field {$entity_field} for entity {$entity_type}: {$th->getMessage()}");
        }

        $repo = $this->entityManager()->getRepository($entity_class);

        $filter_criteria = [];
        foreach ($input['filter'] ?? [] as $key => $value) {
            try {
                $filter_criteria[] = $entity_class::getCriteriaForFilter($key, $value);
            } catch (\Throwable $th) {
                throw new HttpError(400, "Invalid filter {$key} => {$value} for entity {$entity_type}: {$th->getMessage()}");
            }
        }

        $on_off_criteria = is_subclass_of($entity_class, OlzEntity::class) ? [
            Criteria::expr()->eq('on_off', 1),
        ] : [];

        $id = $input['id'] ?? null;
        $id_field_name = $entity_class::getIdFieldNameForSearch();
        $id_criteria = $id ? [Criteria::expr()->eq($id_field_name, $id)] : [];

        $position = $input['position'] ?? null;
        $position_criteria = $position !== null ? [
            Criteria::expr()->gt($position_field, $position - self::FLOAT_EPSILON),
            Criteria::expr()->lt($position_field, $position + self::FLOAT_EPSILON),
        ] : [];

        if (count($id_criteria) === 0 && count($position_criteria) === 0) {
            return [
                'before' => null,
                'this' => null,
                'after' => null,
            ];
        }

        $this_entity_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                ...$on_off_criteria,
                ...$filter_criteria,
                ...$id_criteria,
                ...$position_criteria,
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        [$this_entity] = $repo->matching($this_entity_criteria);

        $this_position = $this_entity?->getPositionForEntityField($entity_field);
        if ($this_position === null) {
            return [
                'before' => null,
                'this' => $this->getOlzEntityPositionResult($entity_field, $this_entity),
                'after' => null,
            ];
        }

        $before_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->isNotNull($position_field),
                Criteria::expr()->lt($position_field, $this_position - self::FLOAT_EPSILON),
                ...$on_off_criteria,
                ...$filter_criteria,
            ))
            ->orderBy([$position_field => Order::Descending])
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        [$before_entity] = $repo->matching($before_criteria);

        $after_criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->isNotNull($position_field),
                Criteria::expr()->gt($position_field, $this_position + self::FLOAT_EPSILON),
                ...$on_off_criteria,
                ...$filter_criteria,
            ))
            ->orderBy([$position_field => Order::Ascending])
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        [$after_entity] = $repo->matching($after_criteria);

        return [
            'before' => $this->getOlzEntityPositionResult($entity_field, $before_entity),
            'this' => $this->getOlzEntityPositionResult($entity_field, $this_entity),
            'after' => $this->getOlzEntityPositionResult($entity_field, $after_entity),
        ];
    }

    /** @return ?OlzEntityPositionResult */
    protected function getOlzEntityPositionResult(string $entity_field, ?PositionableInterface $entity): ?array {
        if (!$entity) {
            return null;
        }
        $position = $entity->getPositionForEntityField($entity_field);
        return [
            'id' => $entity->getIdForSearch(),
            'position' => $position,
            'title' => $entity->getTitleForSearch() ?: '-',
        ];
    }
}
