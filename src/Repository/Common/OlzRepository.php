<?php

namespace Olz\Repository\Common;

use Doctrine\ORM\EntityRepository;
use Olz\Utils\WithUtilsTrait;

class OlzRepository extends EntityRepository {
    use WithUtilsTrait;
}
