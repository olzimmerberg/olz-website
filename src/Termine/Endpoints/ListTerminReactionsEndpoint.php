<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Termine\TerminReaction;

/**
 * @phpstan-type OlzTerminReactionFilter array{terminId: int<1, max>}
 * @phpstan-type OlzReaction array{
 *   userId: int,
 *   name: ?non-empty-string,
 *   emoji: non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     filter: OlzTerminReactionFilter
 *   },
 *   array{
 *     result: array<OlzReaction>
 *   }
 * >
 */
class ListTerminReactionsEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_any_access = $this->authUtils()->hasPermission('any');
        $termin_reaction_repo = $this->entityManager()->getRepository(TerminReaction::class);
        $reactions = $termin_reaction_repo->findBy([
            'termin' => $input['filter']['terminId'],
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
