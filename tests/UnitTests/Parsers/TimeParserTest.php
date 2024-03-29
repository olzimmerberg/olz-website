<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Parsers;

use Olz\Parsers\TimeParser;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Parsers\TimeParser
 */
final class TimeParserTest extends UnitTestCase {
    public function testParseTimeWithoutHour(): void {
        $parser = new TimeParser();

        $this->assertSame(807, $parser->time_str_to_seconds('13:27'));
    }

    public function testParseTimeWithHour(): void {
        $parser = new TimeParser();

        $this->assertSame(8007, $parser->time_str_to_seconds('2:13:27'));
    }

    public function testParseTimeWithZeroHour(): void {
        $parser = new TimeParser();

        $this->assertSame(807, $parser->time_str_to_seconds('0:13:27'));
    }

    public function testParseTimeWithMoreThanOneDay(): void {
        $parser = new TimeParser();

        $this->assertSame(87207, $parser->time_str_to_seconds('24:13:27'));
    }

    public function testParseInvalidTime(): void {
        $parser = new TimeParser();

        $this->assertSame(-1, $parser->time_str_to_seconds('13;27'));
        $this->assertSame(-1, $parser->time_str_to_seconds('13:27:13:27'));
        $this->assertSame(-1, $parser->time_str_to_seconds(' 13:27'));
        $this->assertSame(-1, $parser->time_str_to_seconds('13:27 '));
        $this->assertSame(-1, $parser->time_str_to_seconds(':13:27'));
        $this->assertSame(-1, $parser->time_str_to_seconds('13:27:'));
    }
}
