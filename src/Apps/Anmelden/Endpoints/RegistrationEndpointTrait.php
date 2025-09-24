<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
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
                'type' => $this->getTypeForApi($registration_info),
                'isOptional' => $registration_info->getIsOptional(),
                'title' => $registration_info->getTitle() ?: '-',
                'description' => $registration_info->getDescription(),
                'options' => $options,
            ];
        }

        return [
            'title' => $entity->getTitle() ?: '-',
            'description' => $entity->getDescription(),
            'infos' => $infos,
            'opensAt' => IsoDateTime::fromDateTime($entity->getOpensAt()),
            'closesAt' => IsoDateTime::fromDateTime($entity->getClosesAt()),
        ];
    }

    /** @param OlzRegistrationData $input_data */
    public function updateEntityWithData(Registration $entity, array $input_data): void {
        $entity->setTitle($input_data['title']);
        $entity->setDescription($input_data['description']);
        $entity->setOpensAt($input_data['opensAt'] ?? null);
        $entity->setClosesAt($input_data['closesAt'] ?? null);
    }

    protected function getEntityById(int $id): Registration {
        $repo = $this->entityManager()->getRepository(Registration::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }

    // ---

    /** @return ValidRegistrationInfoType */
    protected function getTypeForApi(RegistrationInfo $entity): string {
        switch ($entity->getType()) {
            case 'email': return 'email';
            case 'firstName': return 'firstName';
            case 'lastName': return 'lastName';
            case 'gender': return 'gender';
            case 'street': return 'street';
            case 'postalCode': return 'postalCode';
            case 'city': return 'city';
            case 'region': return 'region';
            case 'countryCode': return 'countryCode';
            case 'birthdate': return 'birthdate';
            case 'phone': return 'phone';
            case 'siCardNumber': return 'siCardNumber';
            case 'solvNumber': return 'solvNumber';
            case 'string': return 'string';
            case 'enum': return 'enum';
            case 'reservation': return 'reservation';
            default: throw new \Exception("Unknown registration info type: {$entity->getType()} ({$entity})");
        }
    }
}
