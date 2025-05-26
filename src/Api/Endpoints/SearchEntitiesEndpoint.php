<?php

namespace Olz\Api\Endpoints;

use Doctrine\Common\Collections\Criteria;
use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Faq\Question;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Entity\Roles\Role;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;

/**
 * TODO: Support key-of<self::SUPPORTED_ENTITY_TYPES> in php-typescript-api.
 *
 * @phpstan-type OlzEntityResult array{
 *   id: int<1, max>,
 *   title: non-empty-string,
 * }
 * @phpstan-type OlzSearchableEntityType 'Download'|'Link'|'Question'|'QuestionCategory'|'SolvEvent'|'TerminLabel'|'TerminLocation'|'TerminTemplate'|'Role'|'User'
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     entityType: OlzSearchableEntityType,
 *     query?: ?string,
 *     id?: ?int<1, max>,
 *     filter?: ?array<string, string>,
 *   },
 *   array{
 *     result: array<OlzEntityResult>
 *   }
 * >
 */
class SearchEntitiesEndpoint extends OlzTypedEndpoint {
    public const SUPPORTED_ENTITY_TYPES = [
        'Download' => Download::class,
        'Link' => Link::class,
        'Question' => Question::class,
        'QuestionCategory' => QuestionCategory::class,
        'SolvEvent' => SolvEvent::class,
        'TerminLabel' => TerminLabel::class,
        'TerminLocation' => TerminLocation::class,
        'TerminTemplate' => TerminTemplate::class,
        'Role' => Role::class,
        'User' => User::class,
    ];

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity_type = $input['entityType'];
        $entity_class = self::SUPPORTED_ENTITY_TYPES[$entity_type];

        $filter_criteria = [];
        foreach ($input['filter'] ?? [] as $key => $value) {
            try {
                $filter_criteria[] = $entity_class::getCriteriaForFilter($key, $value);
            } catch (\Throwable $th) {
                throw new HttpError(400, "Invalid filter {$key} => {$value} for entity {$entity_type}: {$th->getMessage()}");
            }
        }

        $search_terms = preg_split('/\s+/', $input['query'] ?? '') ?: [];
        $matching_criterium = Criteria::expr()->andX(
            ...array_map(function ($search_term) use ($entity_class) {
                return $entity_class::getCriteriaForQuery($search_term);
            }, $search_terms),
        );

        $id = $input['id'] ?? null;
        $id_field_name = $entity_class::getIdFieldNameForSearch();
        $id_criteria = $id ? [Criteria::expr()->eq($id_field_name, $id)] : [];

        $repo = $this->entityManager()->getRepository($entity_class);
        $on_off_criteria = is_subclass_of($entity_class, OlzEntity::class) ? [
            Criteria::expr()->eq('on_off', 1),
        ] : [];
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $matching_criterium,
                ...$on_off_criteria,
                ...$filter_criteria,
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
