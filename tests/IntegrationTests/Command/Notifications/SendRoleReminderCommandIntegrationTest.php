<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Olz\Command\Notifications\SendRoleReminderCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendRoleReminderCommand
 */
final class SendRoleReminderCommandIntegrationTest extends IntegrationTestCase {
    public function testSendRoleReminderCommandAutogenerateSubscriptions(): void {
        $job = $this->getSut();
        $job->autogenerateSubscriptions();

        $this->assertSame([
            'INFO Generating role reminder subscriptions...',
            "INFO Generating role (4) reminder subscription for 'vorstand (User ID: 2)'...",
            "INFO Generating role (4) reminder subscription for 'karten (User ID: 3)'...",
            "INFO Generating role (5) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (7) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (16) reminder subscription for 'karten (User ID: 3)'...",
            "INFO Generating role (17) reminder subscription for 'vorstand (User ID: 2)'...",
            "INFO Generating role (18) reminder subscription for 'karten (User ID: 3)'...",
            "INFO Generating role (22) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (22) reminder subscription for 'hackerman (User ID: 4)'...",
            "INFO Generating role (23) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (23) reminder subscription for 'vorstand (User ID: 2)'...",
            "INFO Generating role (25) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (25) reminder subscription for 'karten (User ID: 3)'...",
            "INFO Generating role (25) reminder subscription for 'hackerman (User ID: 4)'...",
            "INFO Generating role (49) reminder subscription for 'admin (User ID: 1)'...",
            "INFO Generating role (50) reminder subscription for 'kaderlaeufer (User ID: 9)'...",
        ], $this->getLogs());
    }

    public function testSendRoleReminderCommand(): void {
        $the_day = substr(SendRoleReminderCommand::EXECUTION_DATE, 4, 6);
        $date_utils = new DateUtils("2020{$the_day} 16:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notification = $job->getNotification(['role_id' => 49]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du bist im [OLZ-Organigramm](http://integration-test.host/verein) unter dem Ressort [**Kontaktperson Nachwuchs**](http://integration-test.host/verein/nachwuchs-kontakt) eingetragen, bzw. für dieses Ressort zuständig.

            **Vielen Dank, dass du mithilfst, unseren Verein am Laufen zu halten!**

            Um das Organigramm aktuell zu halten, bitten wir dich, die folgenden Punkte durchzugehen.
            
            **Falls etwas unklar ist, kontaktiere bitte den Website-Admin: website@staging.olzimmerberg.ch!**

            - Bitte schau dir die [Präsenz deines Ressorts auf olzimmerberg.ch](http://integration-test.host/verein/nachwuchs-kontakt) an, und **kontrolliere, ergänze und verbessere** gegebenenfalls die Angaben. Wenn du eingeloggt bist, kannst du diese direkt bearbeiten.
            - **Falls** du im kommenden Jahr nicht mehr für dieses Ressort zuständig sein kannst oder möchtest, bzw. nicht mehr unter diesem Ressort angezeigt werden solltest, kontaktiere bitte "deinen" Vorstand: Armin 😂 Admin 🤣, admin@staging.olzimmerberg.ch (oder den Präsi).
            - **Falls** du noch kein OLZ-Konto hast, erstelle doch eines ([zum Login-Dialog](http://integration-test.host/#login-dialog), dann "Noch kein OLZ-Konto?" wählen). Verwende den Benutzernamen "default", um automatisch Schreib-Zugriff für dein Ressort zu erhalten.

            Besten Dank für deine Mithilfe,
            
            Der Vorstand der OL Zimmerberg
            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Ressort-Erinnerung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    protected function getSut(): SendRoleReminderCommand {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SendRoleReminderCommand::class);
    }
}
