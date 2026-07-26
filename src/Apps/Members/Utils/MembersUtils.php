<?php

namespace Olz\Apps\Members\Utils;

use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;

class MembersUtils {
    use WithUtilsTrait;

    public int $first_data_row = 1;
    public string $member_ident_key = '[Id]';
    public string $member_username_key = 'Benutzer-Id';
    public string $member_first_name_key = 'Vorname';
    public string $member_last_name_key = 'Nachname';

    /** @return array<array<string, string>> */
    public function parseCsv(string $csv_content): array {
        $encoding = mb_detect_encoding($csv_content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        $utf8_csv_content = mb_convert_encoding($csv_content, 'UTF-8', $encoding ?: 'Windows-1252');
        $this->generalUtils()->checkNotFalse($utf8_csv_content, 'Could not convert to UTF-8');
        $stream = new \SplTempFileObject();
        $stream->fwrite($utf8_csv_content);
        $stream->rewind();
        $stream->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $stream->setCsvControl(";", "\"", "\\");
        $header = $stream->current();
        if (!is_array($header)) {
            throw new \Exception("Invalid CSV header: {$header}");
        }
        $num_columns = count($header);
        $stream->next();

        $rows = [];
        $i = $this->first_data_row;
        while ($raw_row = $stream->current()) {
            if (!is_array($raw_row)) {
                throw new \Exception("Invalid CSV row[{$i}]: {$raw_row}");
            }
            $raw_row_count = count($raw_row);
            if ($raw_row_count !== $num_columns) {
                $enc_raw_row = json_encode($raw_row);
                $this->log()->notice("Member CSV parse: Row[{$i}] = '{$enc_raw_row}' length ({$raw_row_count}) is not {$num_columns}");
                continue;
            }
            $row = [];
            for ($j = 0; $j < $num_columns; $j++) {
                $row[$header[$j]] = "{$raw_row[$j]}";
            }
            $rows[] = $row;
            $i++;
            $stream->next();
        }
        return $rows;
    }

    /** @param array<string, string> $member */
    public function getMemberIdent(array $member): ?string {
        return $member[$this->member_ident_key] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberUsername(array $member): ?string {
        return $member[$this->member_username_key] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberFirstName(array $member): ?string {
        return $member[$this->member_first_name_key] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberLastName(array $member): ?string {
        return $member[$this->member_last_name_key] ?? null;
    }

    public function update(Member $member, ?User $user): void {
        if (!$user) {
            return;
        }
        if ($member->getUser()?->getId() !== $user->getId()) {
            $this->log()->error("Update requested for member {$member->getIdent()} ({$member->getUser()?->getId()} !== {$user->getId()})");
            return;
        }
        $member_data = json_decode($member->getData(), true);
        $user_data = $this->getUserData($user);
        $update = [];
        foreach ($user_data as $key => $user_value) {
            $member_value = $member_data[$key] ?? '';
            if ($member_value !== $user_value && $user_value !== '') {
                $this->log()->info("Field {$key} was updated.");
                $update[$key] = $user_value;
            }
        }
        if ($update) {
            $enc_update = json_encode($update);
            $this->generalUtils()->checkNotFalse($enc_update, 'JSON encode failed');
            $member->setUpdates($enc_update);
        } else {
            $member->setUpdates(null);
        }
    }

    /** @return array<string, string> */
    protected function getUserData(User $user): array {
        return [
            'Nachname' => $user->getLastName(),
            'Vorname' => $user->getFirstName(),
            'Adresse' => $user->getStreet() ?? '',
            'PLZ' => $user->getPostalCode() ?? '',
            'Ort' => $user->getCity() ?? '',
            'Benutzer-Id' => $user->getUsername(),
            // 'Land' => $user->getCountryCode() ?? '',
            // 'E-Mail' => $user->getEmail() ?? '',
            // 'Geschlecht' => $user->getGender() ?? '',
            // 'Geburtsdatum' => $user->getBirthdate()?->format('Y-m-d') ?? '',
            // 'SOLV NR' => $user->getSolvNumber() ?? '',
            // 'Badge Nummer' => $user->getSiCardNumber() ?? '',
            // 'Rechnungsversand' => 'E-Mail', // All online accounts get invoices by email

            // TODO: Think about the other fields:
            // "Telefon Privat" => $user->getPhone(),
            // "Telefon Mobil"
            // "Anrede"
            // "Titel"
            // "Briefanrede"
            // "Adress-Zusatz"
            // "Nationalität"
            // "[Gruppen]"
            // "Status"
            // "[Rolle]"
            // "Eintritt"
            // "Mitgliedsjahre"
            // "Austritt"
            // "Zivilstand"
            // "Jahrgang"
            // "Alter"
            // "Nie mahnen"
            // "IBAN"
            // "Kontoinhaber"
            // "Mail-MV"
            // "Werbegrund"
            // "Geburtsjahr"
        ];
    }
}
