<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/parsers/TimeParser.php';

/**
 * @internal
 * @covers \TimeParser
 */
final class TimeParserTest extends TestCase {
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
