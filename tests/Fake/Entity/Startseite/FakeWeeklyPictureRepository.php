<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Startseite;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<WeeklyPicture>
 */
class FakeWeeklyPictureRepository extends FakeOlzRepository {
    public string $olzEntityClass = WeeklyPicture::class;
    public string $fakeOlzEntityClass = FakeWeeklyPicture::class;
}
