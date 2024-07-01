<?php

declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Olz\Command\Common;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Component\Console\Output\OutputInterface;

class OlzCommandOutputLogHandler extends AbstractProcessingHandler {
    private OutputInterface $output;

    public function __construct(OutputInterface $output, int|string|Level $level = Level::Debug, bool $bubble = true) {
        $this->output = $output;
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void {
        if ($this->output !== null) {
            $this->output->writeln($record->formatted);
        }
    }

    protected function getDefaultFormatter(): FormatterInterface {
        return new LineFormatter("%channel%.%level_name%: %message% %context% %extra%");
    }
}
