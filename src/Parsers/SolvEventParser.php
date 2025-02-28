<?php

namespace Olz\Parsers;

use Olz\Entity\SolvEvent;
use Olz\Utils\WithUtilsTrait;

class SolvEventParser {
    use WithUtilsTrait;

    /** @var array<int, string> */
    public array $solv_entryportals = [
        1 => "GO2OL",
        2 => "picoTIMING",
        3 => "anderes",
    ];

    /** @return array<SolvEvent> */
    public function parse_solv_events_csv(string $csv_content): array {
        $data = str_getcsv($csv_content, "\n");
        $header = str_getcsv($data[0] ?? '', ";");
        $solv_events = [];
        for ($row_index = 1; $row_index < count($data); $row_index++) {
            $line = html_entity_decode($data[$row_index] ?? '', ENT_QUOTES);
            $row = str_getcsv($line, ";");
            $solv_event = new SolvEvent();
            $solv_event->setStartLink(null);
            $solv_event->setRankLink(null);
            for ($col_index = 0; $col_index < count($header); $col_index++) {
                $csv_column_name = $header[$col_index];
                $field_value = $row[$col_index];
                switch ($csv_column_name) {
                    case 'unique_id':
                        $solv_event->setSolvUid(intval($field_value));
                        break;
                    case 'date':
                        $date = $this->dateUtils()->sanitizeDateValue($field_value);
                        $this->generalUtils()->checkNotNull($date, "No SolvEvent date {$field_value}");
                        $solv_event->setDate($date);
                        break;
                    case 'duration':
                        $solv_event->setDuration(intval($field_value));
                        break;
                    case 'kind':
                        $solv_event->setKind($field_value ?: '');
                        break;
                    case 'day_night':
                        $solv_event->setDayNight($field_value ?: '');
                        break;
                    case 'national':
                        $solv_event->setNational(intval($field_value));
                        break;
                    case 'region':
                        $solv_event->setRegion($field_value ?: '');
                        break;
                    case 'type':
                        $solv_event->setType($field_value ?: '');
                        break;
                    case 'event_name':
                        $solv_event->setName($field_value ?: '');
                        break;
                    case 'event_link':
                        $solv_event->setLink($field_value ?: '');
                        break;
                    case 'club':
                        $solv_event->setClub($field_value ?: '');
                        break;
                    case 'map':
                        $solv_event->setMap($field_value ?: '');
                        break;
                    case 'location':
                        $solv_event->setLocation($field_value ?: '');
                        break;
                    case 'coord_x':
                        $solv_event->setCoordX(intval($field_value));
                        break;
                    case 'coord_y':
                        $solv_event->setCoordY(intval($field_value));
                        break;
                    case 'deadline':
                        $date = $this->dateUtils()->sanitizeDateValue($field_value);
                        $solv_event->setDeadline($date);
                        break;
                    case 'entryportal':
                        $solv_event->setEntryportal(intval($field_value));
                        break;
                    case 'last_modification':
                        $datetime = $this->dateUtils()->sanitizeDatetimeValue($field_value);
                        $solv_event->setLastModification($datetime ?? new \DateTime());
                        break;
                    default:
                        break;
                }
            }
            $solv_events[] = $solv_event;
        }
        return $solv_events;
    }
}
