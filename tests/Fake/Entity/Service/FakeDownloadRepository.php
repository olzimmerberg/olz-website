<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Download;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Download>
 */
class FakeDownloadRepository extends FakeOlzRepository {
    public string $olzEntityClass = Download::class;
    public string $fakeOlzEntityClass = FakeDownload::class;
}
