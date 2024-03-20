<?php

namespace Olz\Repository\Startseite;

use Olz\Repository\Common\OlzRepository;

class WeeklyPictureRepository extends OlzRepository {
    public function getLatestThree() {
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
