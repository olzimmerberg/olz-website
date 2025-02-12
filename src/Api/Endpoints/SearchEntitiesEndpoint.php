<?php

namespace Olz\Api\Endpoints;

use Doctrine\Common\Collections\Criteria;
use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Entity\Roles\Role;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Users\User;

/**
 * TODO: Support key-of<self::SUPPORTED_ENTITY_TYPES>.
 *
 * @phpstan-type OlzEntityResult array{
 *   id: int<1, max>,
 *   title: non-empty-string,
 * }
 * @phpstan-type OlzSearchableEntityTypes 'QuestionCategory'|'SolvEvent'|'TerminLocation'|'TerminTemplate'|'Role'|'User'
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     entityType: OlzSearchableEntityTypes,
 *     query?: ?string,
 *     id?: ?int<1, max>
 *   },
 *   array{
 *     result: array<OlzEntityResult>
 *   }
 * >
 */
class SearchEntitiesEndpoint extends OlzTypedEndpoint {
    public const SUPPORTED_ENTITY_TYPES = [
        'QuestionCategory' => QuestionCategory::class,
        'SolvEvent' => SolvEvent::class,
        'TerminLocation' => TerminLocation::class,
        'TerminTemplate' => TerminTemplate::class,
        'Role' => Role::class,
        'User' => User::class,
    ];

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity_type = $input['entityType'];
        $entity_class = self::SUPPORTED_ENTITY_TYPES[$entity_type];

        $search_terms = preg_split('/\s+/', $input['query'] ?? '');
        $matching_criterium = Criteria::expr()->andX(
            ...array_map(function ($search_term) use ($entity_class) {
                return $entity_class::getCriteriaForQuery($search_term);
            }, $search_terms),
        );

        $id_field_name = $entity_class::getIdFieldNameForSearch();
        $id_criteria = $input['id'] ? [Criteria::expr()->eq($id_field_name, $input['id'])] : [];

        $repo = $this->entityManager()->getRepository($entity_class);
        $on_off_criteria = is_subclass_of($entity_class, OlzEntity::class) ? [
            Criteria::expr()->eq('on_off', 1),
        ] : [];
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $matching_criterium,
                ...$on_off_criteria,
                ...$id_criteria,
            ))
            ->setFirstResult(0)
            ->setMaxResults(10)
        ;
        $matching_entities = $repo->matching($criteria);

        return [
            'result' => array_map(function ($entity) {
                return [
                    'id' => $entity->getIdForSearch(),
                    'title' => $entity->getTitleForSearch(),
                ];
            }, [...$matching_entities]),
        ];
    }
}
