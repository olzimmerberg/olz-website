<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminReaction;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzReaction from ListTerminReactionsEndpoint
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     userId?: ?int<1, max>,
 *     terminId: int<1, max>,
 *     emoji: non-empty-string,
 *     action: 'on'|'off'|'toggle',
 *   },
 *   array{
 *     result: ?OlzReaction,
 *   }
 * >
 */
class ToggleTerminReactionEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $user = $this->authUtils()->getCurrentUser();
        if (($input['userId'] ?? null) !== null) {
            $user_repo = $this->entityManager()->getRepository(User::class);
            $user = $user_repo->findOneBy(['id' => $input['userId']]);
        }
        if (!$has_access || !$user) {
            throw new HttpError(403, 'Kein Zugriff');
        }
        $auth_user_id = $this->session()->get('auth_user_id');
        $is_parent = $auth_user_id && intval($user->getParentUserId()) === intval($auth_user_id);
        $is_self = $auth_user_id && intval($user->getId()) === intval($auth_user_id);
        if (!$is_self && !$is_parent) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        if (!$this->generalUtils()->isOneEmoji($input['emoji'])) {
            $enc_emoji = urlencode($input['emoji']);
            throw new HttpError(400, "Ungültiges Emoji: {$input['emoji']} ({$enc_emoji})");
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
        $result = null;

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
            $result = [
                'userId' => $reaction->getUser()->getId() ?? 0,
                'name' => $reaction->getUser()->getFullName() ?: '?',
                'emoji' => $reaction->getEmoji() ?: '?',
            ];
        }
        if ($has_reactions && !$want_reaction) {
            foreach ($reactions as $reaction) {
                $this->entityManager()->remove($reaction);
            }
            $this->entityManager()->flush();
        }

        return ['result' => $result];
    }
}
