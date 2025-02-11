<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Faq;

use Olz\Entity\Faq\QuestionCategory;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<QuestionCategory>
 */
class FakeQuestionCategoryRepository extends FakeOlzRepository {
    public string $olzEntityClass = QuestionCategory::class;
    public string $fakeOlzEntityClass = FakeQuestionCategory::class;
}
