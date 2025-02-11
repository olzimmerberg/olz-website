<?php

namespace Olz\Repository\Faq;

use Olz\Entity\Faq\Question;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Question>
 */
class QuestionRepository extends OlzRepository {
    protected string $question_class = Question::class;

    public function getPredefinedQuestion(PredefinedQuestion $predefined_question): ?Question {
        $question = $this->findOneBy(['ident' => $predefined_question->value]);
        if (!$question) {
            $this->log()->warning("Predefined question does not exist: {$predefined_question->value}");
        }
        return $question;
    }
}
