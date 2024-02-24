<?php

namespace Olz\Api\Endpoints;

use Doctrine\Common\Collections\Criteria;
use Olz\Api\OlzEndpoint;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class SearchEntitiesEndpoint extends OlzEndpoint {
    public const SUPPORTED_ENTITY_TYPES = [
        'TerminLocation' => TerminLocation::class,
        'TerminTemplate' => TerminTemplate::class,
    ];

    public static function getIdent() {
        return 'SearchEntitiesEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'result' => new FieldTypes\ArrayField([
                'item_field' => new FieldTypes\ObjectField([
                    'export_as' => 'OlzEntityResult',
                    'field_structure' => [
                        'id' => new FieldTypes\IntegerField(['min_value' => 1]),
                        'title' => new FieldTypes\StringField([]),
                    ],
                ]),
            ]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'entityType' => new FieldTypes\EnumField([
                'export_as' => 'OlzSearchableEntityTypes',
                'allowed_values' => array_keys(self::SUPPORTED_ENTITY_TYPES),
            ]),
            'query' => new FieldTypes\StringField(['allow_null' => true, 'allow_empty' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true, 'min_value' => 1]),
        ]]);
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $entity_type = $input['entityType'];
        $entity_class = self::SUPPORTED_ENTITY_TYPES[$entity_type] ?? null;
        if (!$entity_class) {
            throw new HttpError(400, 'Invalid entityType');
        }
        $entity_instance = new $entity_class();
        if (!($entity_instance instanceof SearchableInterface)) {
            throw new HttpError(400, "{$entity_class} does not implement SearchableInterface");
        }

        $field_names = $entity_class::getFieldNamesForSearch();
        $matching_criterium = Criteria::expr()->orX(
            ...array_map(function ($field_name) use ($input) {
                return Criteria::expr()->contains($field_name, $input['query']);
            }, $field_names),
        );
        $id_field_name = $entity_class::getIdFieldNameForSearch();
        $id_criteria = $input['id'] ? [Criteria::expr()->eq($id_field_name, $input['id'])] : [];

        $repo = $this->entityManager()->getRepository($entity_class);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $matching_criterium,
                Criteria::expr()->eq('on_off', 1),
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
