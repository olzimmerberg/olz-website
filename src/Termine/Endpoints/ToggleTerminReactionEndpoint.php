<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminReaction;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     terminId: int<1, max>,
 *     emoji: non-empty-string,
 *     action: 'on'|'off'|'toggle',
 *   },
 *   array{}
 * >
 */
class ToggleTerminReactionEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $user = $this->authUtils()->getCurrentUser();
        if (!$has_access || !$user) {
            throw new HttpError(403, 'Kein Zugriff');
        }
        if (mb_strlen($input['emoji']) !== 1) {
            throw new HttpError(400, 'Ungültiges Emoji');
        }

        $termin_reaction_repo = $this->entityManager()->getRepository(TerminReaction::class);
        $reactions = $termin_reaction_repo->findBy([
            'termin' => $input['terminId'],
            'emoji' => $input['emoji'],
            'user' => $user,
        ]);
        // Hack for prod not applying the emoji filter correctly.
        $reactions = array_filter(
            $reactions,
            fn ($reaction) => $input['emoji'] === $reaction->getEmoji(),
        );
        $has_reactions = count($reactions) > 0;
        $want_reaction = $input['action'] === 'on' || ($input['action'] === 'toggle' && !$has_reactions);

        if (!$has_reactions && $want_reaction) {
            $termin_repo = $this->entityManager()->getRepository(Termin::class);
            $termin = $termin_repo->findOneBy(['id' => $input['terminId']]);
            if (!$termin) {
                throw new HttpError(400, "Kein solcher Termin");
            }
            $reaction = new TerminReaction();
            $reaction->setTermin($termin);
            $reaction->setUser($user);
            $reaction->setEmoji($input['emoji']);
            $this->entityManager()->persist($reaction);
            $this->entityManager()->flush();
        }
        if ($has_reactions && !$want_reaction) {
            foreach ($reactions as $reaction) {
                $this->entityManager()->remove($reaction);
            }
            $this->entityManager()->flush();
        }

        return [];
    }
}
