<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\News\NewsReaction;

/**
 * @phpstan-type OlzNewsReactionFilter array{newsEntryId: int<1, max>}
 * @phpstan-type OlzReaction array{
 *   userId: int,
 *   name: ?non-empty-string,
 *   emoji: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     filter: OlzNewsReactionFilter
 *   },
 *   array{
 *     result: array<OlzReaction>
 *   }
 * >
 */
class ListNewsReactionsEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_any_access = $this->authUtils()->hasPermission('any');
        $news_reaction_repo = $this->entityManager()->getRepository(NewsReaction::class);
        $reactions = $news_reaction_repo->findBy([
            'news_entry' => $input['filter']['newsEntryId'],
        ]);
        $result = [];
        foreach ($reactions as $reaction) {
            $result[] = [
                'userId' => $reaction->getUser()->getId() ?? 0,
                'name' => $has_any_access ? $reaction->getUser()->getFullName() ?: '?' : null,
                'emoji' => $reaction->getEmoji() ?: '?',
            ];
        }
        return [
            'result' => $result,
        ];
    }
}
