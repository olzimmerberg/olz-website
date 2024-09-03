<?php

namespace Olz\Repository\Karten;

use Olz\Entity\Karten\Karte;
use Olz\Repository\Common\IdentStringRepositoryInterface;
use Olz\Repository\Common\IdentStringRepositoryTrait;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Karte>
 *
 * @implements IdentStringRepositoryInterface<Karte>
 */
class KartenRepository extends OlzRepository implements IdentStringRepositoryInterface {
    /** @use IdentStringRepositoryTrait<Karte> */
    use IdentStringRepositoryTrait;
}
