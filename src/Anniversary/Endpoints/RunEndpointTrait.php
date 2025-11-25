<?php

namespace Olz\Anniversary\Endpoints;

use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\PhpStan\IsoDateTime;

/**
 * @phpstan-type OlzRunId int
 * @phpstan-type OlzRunData array{
 *   userId?: ?int,
 *   runAt?: ?IsoDateTime,
 *   distanceMeters: int,
 *   elevationMeters: int,
 *   source?: ?non-empty-string,
 * }
 */
trait RunEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzRunData */
    public function getEntityData(RunRecord $entity): array {
        $run_at = IsoDateTime::fromDateTime($entity->getRunAt());
        $this->generalUtils()->checkNotNull($run_at, "Invalid run_at: {$entity}");
        return [
            'userId' => $entity->getUser()?->getId(),
            'runAt' => $run_at,
            'distanceMeters' => $entity->getDistanceMeters(),
            'elevationMeters' => $entity->getElevationMeters(),
            'source' => $entity->getSource() ?: null,
        ];
    }

    /** @param OlzRunData $input_data */
    public function updateEntityWithData(RunRecord $entity, array $input_data): void {
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input_data['userId'] ?? null]);
        if ($user === null) {
            $user = $this->authUtils()->getCurrentUser();
        }

        $entity->setUser($user);
        $entity->setRunAt($input_data['runAt'] ?? $now);
        $entity->setDistanceMeters($input_data['distanceMeters']);
        $entity->setElevationMeters($input_data['elevationMeters']);
        $entity->setSource($input_data['source'] ?? 'manuell');
    }

    protected function getEntityById(int $id): RunRecord {
        $repo = $this->entityManager()->getRepository(RunRecord::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
