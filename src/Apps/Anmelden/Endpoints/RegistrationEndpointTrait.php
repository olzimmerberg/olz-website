<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\PhpStan\IsoDateTime;

/**
 * @phpstan-type OlzRegistrationId non-empty-string
 * @phpstan-type OlzRegistrationData array{
 *   title: non-empty-string,
 *   description: string,
 *   infos: array<OlzRegistrationInfo>,
 *   opensAt?: ?IsoDateTime,
 *   closesAt?: ?IsoDateTime,
 * }
 * @phpstan-type OlzRegistrationInfo array{
 *   type: ValidRegistrationInfoType,
 *   isOptional: bool,
 *   title: non-empty-string,
 *   description: string,
 *   options?: ?(
 *     array{text: array<non-empty-string>}
 *     | array{svg: array<non-empty-string>}
 *   ),
 * }
 * @phpstan-type ValidRegistrationInfoType 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'siCardNumber'|'solvNumber'|'string'|'enum'|'reservation'
 */
trait RegistrationEndpointTrait {
    use WithUtilsTrait;

    public function configureRegistrationEndpointTrait(): void {
        $this->phpStanUtils->registerApiObject(IsoDateTime::class);
    }

    /** @return OlzRegistrationData */
    public function getEntityData(Registration $entity): array {
        $infos = [];
        $registration_info_repo = $this->entityManager()->getRepository(RegistrationInfo::class);
        $registration_infos = $registration_info_repo->findBy(
            ['registration' => $entity, 'on_off' => true],
            ['indexWithinRegistration' => 'ASC'],
        );
        foreach ($registration_infos as $registration_info) {
            $options = json_decode($registration_info->getOptions(), true);
            $infos[] = [
                'type' => $registration_info->getType(),
                'isOptional' => $registration_info->getIsOptional(),
                'title' => $registration_info->getTitle(),
                'description' => $registration_info->getDescription(),
                'options' => $options,
            ];
        }

        return [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'infos' => $infos,
            'opensAt' => $entity->getOpensAt()?->format('Y-m-d H:i:s'),
            'closesAt' => $entity->getClosesAt()?->format('Y-m-d H:i:s'),
        ];
    }

    /** @param OlzRegistrationData $input_data */
    public function updateEntityWithData(Registration $entity, array $input_data): void {
        $entity->setTitle($input_data['title']);
        $entity->setDescription($input_data['description']);
        $entity->setOpensAt($input_data['opensAt']);
        $entity->setClosesAt($input_data['closesAt']);
    }
}
