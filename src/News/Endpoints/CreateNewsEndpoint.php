<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Users\User;
use Symfony\Component\Mime\Email;

/**
 * @phpstan-import-type OlzNewsId from NewsEndpointTrait
 * @phpstan-import-type OlzNewsData from NewsEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzNewsFormat from NewsEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzNewsId, OlzNewsData, array{
 *   captchaToken?: ?non-empty-string,
 * }, array{
 *   status: 'OK'|'DENIED'|'ERROR',
 * }>
 */
class CreateNewsEndpoint extends OlzCreateEntityTypedEndpoint {
    use NewsEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureNewsEndpointTrait();
        $this->phpStanUtils->registerTypeImport(NewsEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $input_data = $input['data'];
        $format = $input_data['format'];

        if ($format !== 'anonymous') {
            $this->checkPermission('any');
        }
        if ($format === 'kaderblog') {
            $this->checkPermission('kaderblog');
        }
        if ($format === 'aktuell') {
            $this->checkIsStaff();
        }

        $token = $input['custom']['captchaToken'] ?? null;
        $is_valid_token = $token ? $this->captchaUtils()->validateToken($token) : false;
        if ($format === 'anonymous' && !$is_valid_token) {
            return ['custom' => ['status' => 'DENIED'], 'id' => null];
        }

        $news_entry = new NewsEntry();
        $this->entityUtils()->createOlzEntity($news_entry, $input['meta']);
        $this->updateEntityWithData($news_entry, $input['data']);

        $this->entityManager()->persist($news_entry);
        $this->entityManager()->flush();
        $this->persistUploads($news_entry, $input['data']);

        if ($format === 'anonymous') {
            $anonymous_user = new User();
            $anonymous_user->setEmail($input_data['authorEmail'] ?? null);
            $anonymous_user->setFirstName($input_data['authorName'] ?? '-');
            $anonymous_user->setLastName('');

            $delete_news_token = urlencode($this->emailUtils()->encryptEmailReactionToken([
                'action' => 'delete_news',
                'news_id' => $news_entry->getId(),
            ]));
            $base_url = $this->envUtils()->getBaseHref();
            $code_href = $this->envUtils()->getCodeHref();
            $news_url = "{$base_url}{$code_href}news/{$news_entry->getId()}";
            $delete_news_url = "{$base_url}{$code_href}email_reaktion?token={$delete_news_token}";
            $text = <<<ZZZZZZZZZZ
                Hallo {$anonymous_user->getFirstName()},

                Du hast soeben auf [{$base_url}]({$base_url}) einen [anonymen Forumseintrag]({$news_url}) erstellt.

                Falls du deinen Eintrag wieder *löschen* willst, klicke [hier]({$delete_news_url}) oder auf folgenden Link:

                {$delete_news_url}

                ZZZZZZZZZZ;
            $config = [
                'no_unsubscribe' => true,
            ];

            try {
                $email = (new Email())->subject("[OLZ] Dein Forumseintrag");
                $email = $this->emailUtils()->buildOlzEmail($email, $anonymous_user, $text, $config);
                $this->emailUtils()->send($email);
                $this->log()->info("Forumseintrag email sent to {$anonymous_user->getEmail()}.");
            } catch (\Exception $exc) {
                $message = $exc->getMessage();
                $this->log()->critical("Error sending Forumseintrag email to {$anonymous_user->getEmail()}.: {$message}");
            }
        }

        return [
            'custom' => ['status' => 'OK'],
            'id' => $news_entry->getId(),
        ];
    }
}
