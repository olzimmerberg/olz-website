<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Throttling;
use Olz\Tasks\SendDailyNotificationsTask;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class OnContinuouslyEndpoint extends OlzEndpoint {
    protected $sendDailyNotificationsTask;

    public function runtimeSetup() {
        parent::runtimeSetup();
        $send_daily_notifications_task = new SendDailyNotificationsTask();
        $this->setSendDailyNotificationsTask($send_daily_notifications_task);
    }

    public function setSendDailyNotificationsTask($sendDailyNotificationsTask) {
        $this->sendDailyNotificationsTask = $sendDailyNotificationsTask;
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

        $command_input = new ArrayInput([]);
        $command_output = new BufferedOutput();
        $this->symfonyUtils()->callCommand('olz:onContinuously', $command_input, $command_output);

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
