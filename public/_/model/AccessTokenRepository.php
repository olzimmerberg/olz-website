<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/AccessToken.php';
require_once __DIR__.'/../config/doctrine.php';

class AccessTokenRepository extends EntityRepository {
}
