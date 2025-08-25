<?php

namespace Olz\Repository\Faq;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Olz\Entity\Faq\Question;
use Olz\Repository\Common\OlzRepository;

/**
 * @extends OlzRepository<Question>
 */
class QuestionRepository extends OlzRepository {
    protected string $entityClass = Question::class;

    public function getPredefinedQuestion(PredefinedQuestion $predefined_question): ?Question {
        $question = $this->findOneBy(['ident' => $predefined_question->value]);
        if (!$question) {
            $this->log()->warning("Predefined question does not exist: {$predefined_question->value}");
        }
        return $question;
    }

    /**
     * @param string[] $terms
     *
     * @return Collection<int, Question>&iterable<Question>
     */
    public function search(array $terms): Collection {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                Criteria::expr()->eq('on_off', 1),
                ...array_map(fn ($term) => Criteria::expr()->orX(
                    Criteria::expr()->contains('ident', $term),
                    Criteria::expr()->contains('question', $term),
                    Criteria::expr()->contains('answer', $term),
                ), $terms),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1000000)
        ;
        return $this->matching($criteria);
    }
}
