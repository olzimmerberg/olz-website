<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../src/parsers/solv_results.php';

/**
 * @internal
 * @coversNothing
 */
final class SolvYearlyResultsJsonParserTest extends TestCase {
    private $results_2006_path = __DIR__.'/data/results-2006.json';

    private $results_2018_path = __DIR__.'/data/results-2018.json';

    public function testParseResults2006(): void {
        $results_2006 = file_get_contents($this->results_2006_path);

        $solv_events_2006 = parse_solv_yearly_results_json($results_2006);

        $this->assertSame(0, count($solv_events_2006));
    }

    public function testParseResults2018(): void {
        $results_2018 = file_get_contents($this->results_2018_path);

        $solv_events_2018 = parse_solv_yearly_results_json($results_2018);

        $this->assertSame(177, count($solv_events_2018));

        $this->assertSame(['result_list_id' => 4491], $solv_events_2018[8891]);
    }
}

/**
 * @internal
 * @coversNothing
 */
final class SolvEventResultsHtmlParserTest extends TestCase {
    // In 2006, rankings did not have IDs yet...
    private $result_2006_path = __DIR__.'/data/result-2006-?.html';

    private $result_2018_path = __DIR__.'/data/result-2018-4491.html';

    public function testParseResults2006(): void {
        $result_2006 = file_get_contents($this->result_2006_path);

        $results = parse_solv_event_result_html($result_2006, 3230);

        // Those old rankings cannot be parsed
        $this->assertSame(0, count($results));
    }

    public function testParseResults2018(): void {
        $result_2018 = file_get_contents($this->result_2018_path);

        $results = parse_solv_event_result_html($result_2018, 8891);

        $this->assertSame(52, count($results));

        $first_result = $results[0];
        $this->assertSame('H10', $first_result->class);
        $this->assertSame(4, $first_result->rank);
        $this->assertSame('Manuel Gasser', $first_result->name);
        $this->assertSame('08', $first_result->birth_year);
        $this->assertSame('Horgen', $first_result->domicile);
        $this->assertSame('OL Zimmerberg', $first_result->club);
        $this->assertSame(664, $first_result->result);
        $this->assertSame('1.   0.38 (2)  2.   1.06 (2)  3.   1.40 (3)  4.   1.41 (3)  5.   2.10 (4)
   131   0.38 (2) 164   0.28 (2) 140   0.34 (6) 145   0.01 (1) 180   0.29 (7)
         0.04           0.04           0.07           0.00           0.10
    6.   3.15 (5)  7.   3.31 (5)  8.   4.22 (5)  9.   4.25 (5) 10.   5.02 (4)
   185   1.05(10)  95   0.16 (4) 160   0.51 (5) 165   0.03(10)  92   0.37 (2)
         0.32           0.06           0.14           0.02           0.02
   11.   5.41 (4) 12.   6.14 (4) 13.   7.38 (4) 14.   7.55 (4) 15.   9.14 (4)
   170   0.39 (2)  96   0.33 (5)  94   1.24 (7)  93   0.17 (1)  97   1.19 (4)
         0.03           0.05           0.28           0.00           0.21
   16.  10.07 (4) 17.  10.33 (4)      11.04 (4)
   177   0.53 (2) 190   0.26 (3)  Zi   0.31(10)
         0.02           0.05           0.06', $first_result->splits);
        $this->assertSame(1600, $first_result->class_distance);
        $this->assertSame(10, $first_result->class_elevation);
        $this->assertSame(17, $first_result->class_control_count);
        $this->assertSame(14, $first_result->class_competitor_count);
    }
}
