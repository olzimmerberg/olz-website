<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;
use Olz\Utils\AbstractDateUtils;

/**
 * @internal
 *
 * @coversNothing
 */
final class LiveResultsTest extends SystemTestCase {
    public function testLiveResultsScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $date_utils = AbstractDateUtils::fromEnv();
        $live_file_content = json_encode([
            'last_updated_at' => $date_utils->getIsoNow(),
            'file' => 'results.xml',
        ]);
        file_put_contents($this->getLiveFilePath(), $live_file_content);

        $browser->get($this->getUrl());
        $this->screenshot('live_results_link');

        unlink($this->getLiveFilePath());

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }

    protected function getLiveFilePath(): string {
        return __DIR__.'/../../public/results/_live.json';
    }
}
