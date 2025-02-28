<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Utils\WithUtilsTrait;

class TransportSuggestion {
    use WithUtilsTrait;

    protected TransportConnection $mainConnection;
    /** @var array<array{connection: TransportConnection, joiningStationId: string}> */
    protected array $sideConnections = [];
    /** @var array<mixed> */
    protected array $originInfo = [];
    /** @var array<string> */
    protected array $debug = [];

    /** @return array<string, mixed> */
    public function getFieldValue(): array {
        return [
            'mainConnection' => $this->mainConnection->getFieldValue(),
            'sideConnections' => array_map(function ($side_connection) {
                return [
                    'connection' => $side_connection['connection']->getFieldValue(),
                    'joiningStationId' => $side_connection['joiningStationId'],
                ];
            }, $this->sideConnections),
            'originInfo' => $this->originInfo,
            'debug' => implode("\n", $this->debug),
        ];
    }

    /** @param array<string, mixed> $value */
    public static function fromFieldValue(array $value): self {
        $instance = new self();
        $instance->populateFromFieldValue($value);
        return $instance;
    }

    /** @param array<string, mixed> $value */
    protected function populateFromFieldValue(array $value): void {
        $this->mainConnection = TransportConnection::fromFieldValue($value['mainConnection']);
        $this->sideConnections = array_map(function ($side_connection) {
            return [
                'connection' => TransportConnection::fromFieldValue($side_connection['connection']),
                'joiningStationId' => $side_connection['joiningStationId'],
            ];
        }, $value['sideConnections']);
        $this->originInfo = $value['originInfo'];
        $this->debug = explode("\n", $value['debug']);
    }

    public function getPrettyPrint(): string {
        $all_entries = [];
        foreach ($this->mainConnection->getFlatHalts() as $halt) {
            $all_entries[] = [
                'halt' => $halt,
                'connection_index' => 0,
                'is_joining_halt' => false,
            ];
        }
        $side_connection_index = 1;
        foreach ($this->sideConnections as $side_connection) {
            $has_joined = false;
            foreach ($side_connection['connection']->getFlatHalts() as $halt) {
                $is_joining_halt = $side_connection['joiningStationId'] == $halt->getStationId();
                if (!$has_joined) {
                    $all_entries[] = [
                        'halt' => $halt,
                        'connection_index' => $side_connection_index,
                        'is_joining_halt' => $is_joining_halt,
                    ];
                }
                if ($is_joining_halt) {
                    $has_joined = true;
                }
            }
            $side_connection_index++;
        }
        usort(
            $all_entries,
            function ($entry_a, $entry_b) {
                $entry_a_value = $entry_a['halt']->getTimeSeconds();
                $entry_b_value = $entry_b['halt']->getTimeSeconds();
                return $entry_a_value < $entry_b_value ? -1 : 1;
            }
        );
        $num_connections = count($this->sideConnections) + 1;
        return implode("\n", array_map(function ($entry) use ($num_connections) {
            $before_dot = str_repeat('  ', $entry['connection_index']);
            $after_dot = str_repeat('  ', $num_connections - $entry['connection_index'] - 1);
            $dot = $entry['is_joining_halt'] ? '<' : 'O';
            $halt = $entry['halt'];
            return "{$before_dot}{$dot}{$after_dot} {$halt->getTimeString()} {$halt->getStationName()}";
        }, $all_entries));
    }

    public function getMainConnection(): TransportConnection {
        return $this->mainConnection;
    }

    public function setMainConnection(TransportConnection $new_main_connection): void {
        $this->mainConnection = $new_main_connection;
    }

    /** @return array<array{connection: TransportConnection, joiningStationId: string}> */
    public function getSideConnections(): array {
        return $this->sideConnections;
    }

    /** @param array{connection: TransportConnection, joiningStationId: string} $new_side_connection */
    public function addSideConnection(array $new_side_connection): void {
        $this->sideConnections[] = $new_side_connection;
    }

    /** @return array<mixed> */
    public function getOriginInfo(): array {
        return $this->originInfo;
    }

    /** @param array<mixed> $new_origin_info */
    public function setOriginInfo(array $new_origin_info): void {
        $this->originInfo = $new_origin_info;
    }

    /** @return array<string> */
    public function getDebug(): array {
        return $this->debug;
    }

    public function addDebug(string $line): void {
        $this->debug[] = $line;
    }

    /**
     * @param array<mixed> $origin_stations
     *
     * @return array<mixed>
     */
    public function generateOriginInfo(array $origin_stations): array {
        $most_important_stations = array_slice($origin_stations, 0);
        usort(
            $most_important_stations,
            function ($station_a, $station_b) {
                $station_a_weight = $station_a['weight'] ?? 0;
                $station_b_weight = $station_b['weight'] ?? 0;
                return $station_a_weight < $station_b_weight ? 1 : -1;
            }
        );
        $destination_halt = $this->getDestinationHalt();
        return array_map(function ($station) use ($destination_halt) {
            $station_id = $station['id'];
            $halt_at_station = $this->getHaltAtStation($station_id);
            return [
                'halt' => [
                    'stationId' => $station_id,
                    'stationName' => $station['name'],
                    'time' => $halt_at_station?->getTimeString() ?? '',
                ],
                'isSkipped' => $halt_at_station === null,
                'rating' => $this->getRatingForHalt($halt_at_station, $destination_halt),
            ];
        }, $most_important_stations);
    }

    public function getDestinationHalt(): TransportHalt {
        return $this->mainConnection->getDestinationHalt();
    }

    public function getHaltAtStation(string $station_id): ?TransportHalt {
        $halts = $this->getFlatHalts();
        $halts_at_station = array_values(array_filter(
            $halts,
            function ($halt) use ($station_id) {
                return $halt->getStationId() === $station_id;
            }
        ));
        $last_index = count($halts_at_station) - 1;
        if ($last_index === -1) {
            return null;
        }
        return $halts_at_station[$last_index];
    }

    /** @return array<TransportHalt> */
    public function getFlatHalts(): array {
        return array_merge(
            $this->mainConnection->getFlatHalts(),
            ...array_map(function ($side_connection) {
                return $side_connection['connection']->getFlatHalts();
            }, $this->sideConnections),
        );
    }

    public function getRatingForHalt(?TransportHalt $halt, ?TransportHalt $destination_halt): float {
        if (!$halt || !$destination_halt) {
            return 0;
        }
        $duration_seconds = $destination_halt->getTimeSeconds() - $halt->getTimeSeconds();
        return 1 / max(1, log($duration_seconds));
    }
}
