<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Throttling;
use Olz\Tasks\ProcessEmailTask;
use Olz\Tasks\SendDailyNotificationsTask;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class OnContinuouslyEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        $process_email_task = new ProcessEmailTask(
            $this->entityManager(),
            $this->authUtils(),
            $this->emailUtils(),
            $this->dateUtils(),
            $this->envUtils()
        );
        $send_daily_notifications_task = new SendDailyNotificationsTask(
            $this->entityManager(),
            $this->emailUtils(),
            $this->telegramUtils(),
            $this->dateUtils(),
            $this->envUtils()
        );
        $this->setProcessEmailTask($process_email_task);
        $this->setSendDailyNotificationsTask($send_daily_notifications_task);
    }

    public function setSendDailyNotificationsTask($sendDailyNotificationsTask) {
        $this->sendDailyNotificationsTask = $sendDailyNotificationsTask;
    }

    public function setProcessEmailTask($processEmailTask) {
        $this->processEmailTask = $processEmailTask;
    }

    public static function getIdent() {
        return 'OnContinuouslyEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'authenticityCode' => new FieldTypes\StringField([]),
        ]]);
    }

    public function parseInput() {
        global $_GET;
        $input = [
            'authenticityCode' => $_GET['authenticityCode'],
        ];
        return $input;
    }

    protected function handle($input) {
        $expected_code = $this->envUtils()->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        if ($this->shouldSendDailyMailNow()) {
            $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
            $throttling_repo->recordOccurrenceOf('daily_notifications', $this->dateUtils()->getIsoNow());

            $this->sendDailyNotificationsTask->run();
        }

        $this->processEmailTask->run();

        return [];
    }

    public function shouldSendDailyMailNow(): bool {
        $daily_notifications_time = '16:27:00';
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_daily_notifications = $throttling_repo->getLastOccurrenceOf('daily_notifications');
        $is_too_soon = false;
        if ($last_daily_notifications) {
            $now = new \DateTime($this->dateUtils()->getIsoNow());
            // Consider daylight saving change date => not 23 hours!
            $min_interval = \DateInterval::createFromDateString('+22 hours');
            $min_now = $last_daily_notifications->add($min_interval);
            $is_too_soon = $now < $min_now;
        }
        $is_right_time_of_day = $this->dateUtils()->getCurrentDateInFormat('H:i:s') >= $daily_notifications_time;
        return !$is_too_soon && $is_right_time_of_day;
    }
}
