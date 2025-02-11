<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Faq;

use Olz\Entity\Faq\Question;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Question>
 */
class FakeQuestionRepository extends FakeOlzRepository {
    public string $olzEntityClass = Question::class;
    public string $fakeOlzEntityClass = FakeQuestion::class;
}
