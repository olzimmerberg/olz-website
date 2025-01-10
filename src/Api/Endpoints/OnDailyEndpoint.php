<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Throttling;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     authenticityCode: non-empty-string,
 *   },
 *   ?array{}
 * >
 */
class OnDailyEndpoint extends OlzTypedEndpoint {
    public function parseInput(Request $request): mixed {
        return [
            'authenticityCode' => $request->query->get('authenticityCode'),
        ];
    }

    public function shouldFailThrottling(): bool {
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

    protected function handle(mixed $input): mixed {
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
