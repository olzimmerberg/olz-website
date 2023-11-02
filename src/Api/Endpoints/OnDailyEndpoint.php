<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Throttling;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;

class OnDailyEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'OnDailyEndpoint';
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

    public function parseInput(Request $request) {
        return [
            'authenticityCode' => $request->query->get('authenticityCode'),
        ];
    }

    public function shouldFailThrottling() {
        if ($this->envUtils()->hasUnlimitedCron()) {
            return false;
        }
        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $last_daily = $throttling_repo->getLastOccurrenceOf('on_daily');
        if (!$last_daily) {
            return false;
        }
        $now = new \DateTime($this->dateUtils()->getIsoNow());
        $min_interval = \DateInterval::createFromDateString('+22 hours');
        $min_now = $last_daily->add($min_interval);
        return $now < $min_now;
    }

    protected function handle($input) {
        $expected_code = $this->envUtils()->getCronAuthenticityCode();
        $actual_code = $input['authenticityCode'];
        if ($actual_code != $expected_code) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        set_time_limit(4000);
        ignore_user_abort(true);

        $throttling_repo = $this->entityManager()->getRepository(Throttling::class);
        $throttling_repo->recordOccurrenceOf('on_daily', $this->dateUtils()->getIsoNow());

        $command_input = new ArrayInput([]);
        $command_output = new BufferedOutput();
        $this->symfonyUtils()->callCommand('olz:on-daily', $command_input, $command_output);

        return [];
    }
}
