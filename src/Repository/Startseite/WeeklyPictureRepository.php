<?php

namespace Olz\Repository\Startseite;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<WeeklyPicture>
 */
class WeeklyPictureRepository extends OlzRepository {
    /** @return array<WeeklyPicture> */
    public function getLatestThree(): array {
        $dql = "
            SELECT wp
            FROM Olz\\Entity\\Startseite\\WeeklyPicture wp
            WHERE wp.on_off = 1
            ORDER BY wp.datum DESC
        ";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(3);
        return $query->getResult();
    }
}
