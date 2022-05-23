<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../public/_/oev/utils/TransportSuggestion.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \TransportSuggestion
 */
final class TransportSuggestionTest extends UnitTestCase {
    public function testGetField(): void {
        $field = TransportSuggestion::getField();
        $this->assertSame(
            'OlzTransportSuggestion',
            $field->getTypeScriptType(),
        );
        $this->assertSame(
            [
                'OlzTransportSuggestion' => "{\n    'mainConnection': OlzTransportConnection,\n    'sideConnections': Array<{\n    'connection': OlzTransportConnection,\n    'joiningStationId': string,\n}>,\n    'originInfo': Array<OlzOriginInfo>,\n    'debug': string,\n}",
                'OlzTransportConnection' => "{\n    'sections': Array<OlzTransportSection>,\n}",
                'OlzTransportSection' => "{\n    'departure': OlzTransportHalt,\n    'arrival': OlzTransportHalt,\n    'passList': Array<OlzTransportHalt>,\n    'isWalk': boolean,\n}",
                'OlzTransportHalt' => "{\n    'stationId': string,\n    'stationName': string,\n    'time': string,\n}",
                'OlzOriginInfo' => "{\n    'halt': OlzTransportHalt,\n    'isSkipped': boolean,\n    'rating': number,\n}",
            ],
            $field->getExportedTypeScriptTypes(),
        );
    }
}
