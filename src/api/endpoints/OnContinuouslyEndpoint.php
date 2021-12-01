<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../OlzEndpoint.php';
require_once __DIR__.'/../../model/Throttling.php';

class OnContinuouslyEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $_DATE, $entityManager;
        require_once __DIR__.'/../../config/date.php';
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../config/server.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../tasks/ProcessEmailTask.php';
        require_once __DIR__.'/../../tasks/SendDailyNotificationsTask.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/notify/EmailUtils.php';
        require_once __DIR__.'/../../utils/notify/TelegramUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $date_utils = $_DATE;
        $email_utils = EmailUtils::fromEnv();
        $telegram_utils = TelegramUtils::fromEnv();
        $process_email_task = new ProcessEmailTask($entityManager, $auth_utils, $email_utils, $date_utils, $_CONFIG);
        $send_daily_notifications_task = new SendDailyNotificationsTask($entityManager, $email_utils, $telegram_utils, $date_utils, $_CONFIG);
        $this->setProcessEmailTask($process_email_task);
        $this->setSendDailyNotificationsTask($send_daily_notifications_task);
        $this->setEntityManager($entityManager);
        $this->setDateUtils($date_utils);
        $this->setEnvUtils($_CONFIG);
    }

    public function setSendDailyNotificationsTask($sendDailyNotificationsTask) {
        $this->sendDailyNotificationsTask = $sendDailyNotificationsTask;
    }

    public function setProcessEmailTask($processEmailTask) {
        $this->processEmailTask = $processEmailTask;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
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
        $expected_code = $this->envUtils->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        if ($this->shouldSendDailyMailNow()) {
            $throttling_repo = $this->entityManager->getRepository(Throttling::class);
            $throttling_repo->recordOccurrenceOf('daily_notifications', $this->dateUtils->getIsoNow());

            $this->sendDailyNotificationsTask->run();
        }

        $this->processEmailTask->run();

        return [];
    }

    public function shouldSendDailyMailNow(): bool {
        $daily_notifications_time = '16:27:00';
        $throttling_repo = $this->entityManager->getRepository(Throttling::class);
        $last_daily_notifications = $throttling_repo->getLastOccurrenceOf('daily_notifications');
        $is_too_soon = false;
        if ($last_daily_notifications) {
            $now = new DateTime($this->dateUtils->getIsoNow());
            // Consider daylight saving change date => not 23 hours!
            $min_interval = DateInterval::createFromDateString('+22 hours');
            $min_now = $last_daily_notifications->add($min_interval);
            $is_too_soon = $now < $min_now;
        }
        $is_right_time_of_day = $this->dateUtils->getCurrentDateInFormat('H:i:s') >= $daily_notifications_time;
        return !$is_too_soon && $is_right_time_of_day;
    }
}
