<?php

namespace Olz\Repository\Startseite;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<WeeklyPicture>
 */
class WeeklyPictureRepository extends OlzRepository {
    protected string $entityClass = WeeklyPicture::class;

    /** @return array<WeeklyPicture> */
    public function getLatestThree(): array {
        $dql = "
            SELECT wp
            FROM {$this->entityClass} wp
            WHERE wp.on_off = 1
            ORDER BY wp.datum DESC
        ";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(3);
        return $query->getResult();
    }
}
