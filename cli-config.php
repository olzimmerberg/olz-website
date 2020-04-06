<?php

require_once __DIR__.'/src/config/doctrine.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
