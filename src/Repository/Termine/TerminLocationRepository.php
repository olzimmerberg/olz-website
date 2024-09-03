<?php

namespace Olz\Repository\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Repository\Common\IdentStringRepositoryInterface;
use Olz\Repository\Common\IdentStringRepositoryTrait;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<TerminLocation>
 *
 * @implements IdentStringRepositoryInterface<TerminLocation>
 */
class TerminLocationRepository extends OlzRepository implements IdentStringRepositoryInterface {
    /** @use IdentStringRepositoryTrait<TerminLocation> */
    use IdentStringRepositoryTrait;
}
