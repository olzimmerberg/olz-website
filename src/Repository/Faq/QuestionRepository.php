<?php

namespace Olz\Repository\Faq;

use Olz\Entity\Faq\Question;
use Olz\Repository\Common\IdentStringRepositoryInterface;
use Olz\Repository\Common\IdentStringRepositoryTrait;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Question>
 *
 * @implements IdentStringRepositoryInterface<Question>
 */
class QuestionRepository extends OlzRepository implements IdentStringRepositoryInterface {
    /** @use IdentStringRepositoryTrait<Question> */
    use IdentStringRepositoryTrait;
}
