<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\News\NewsReaction;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     newsEntryId: int<1, max>,
 *     emoji: non-empty-string,
 *     action: 'on'|'off'|'toggle',
 *   },
 *   array{}
 * >
 */
class ToggleNewsReactionEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $user = $this->authUtils()->getCurrentUser();
        if (!$has_access || !$user) {
            throw new HttpError(403, 'Kein Zugriff');
        }
        if (mb_strlen($input['emoji']) !== 1) {
            throw new HttpError(400, 'Ungültiges Emoji');
        }

        $news_reaction_repo = $this->entityManager()->getRepository(NewsReaction::class);
        $reactions = $news_reaction_repo->findBy([
            'news_entry' => $input['newsEntryId'],
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
            $news_repo = $this->entityManager()->getRepository(NewsEntry::class);
            $news_entry = $news_repo->findOneBy(['id' => $input['newsEntryId']]);
            if (!$news_entry) {
                throw new HttpError(400, "Kein solcher News-Eintrag");
            }
            $reaction = new NewsReaction();
            $reaction->setNewsEntry($news_entry);
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
