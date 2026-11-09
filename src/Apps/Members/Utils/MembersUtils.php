<?php

namespace Olz\Apps\Members\Utils;

use Olz\Common\Utils\WithUtilsTrait;
use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;

/**
 * @phpstan-type MemberCsvColumn 'ident'|'username'|'first_name'|'last_name'|'company'|'status'|'joined_on'|'left_on'
 */
class MembersUtils {
    use WithUtilsTrait;

    private int $first_data_row = 1;

    /** @var array<MemberCsvColumn, string> */
    private array $csv_columns = [
        'ident' => '[Id]',
        'username' => 'Benutzer-Id',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'company' => 'Firma',
        'status' => 'Status',
        'joined_on' => 'Eintritt',
        'left_on' => 'Austritt',
    ];

    /** @var array<string, bool> */
    private array $is_enabled_by_status = [
        'Kein Mitglied' => false,
        'Aktivmitglied' => true,
        'Passivmitglied' => true,
        'Ehrenmitglied' => true,
        'Ehemaliges Mitglied' => false,
        'Verstorben' => false,
        'Sp' => true,
        'A' => true,
        'V' => false,
        'E' => true,
    ];

    /** @return array<array<string, string>> */
    public function parseCsv(string $csv_content): array {
        $utf8_csv_content = $this->convertMemberCsvEncoding($csv_content);
        $stream = new \SplTempFileObject();
        $stream->fwrite($utf8_csv_content);
        $stream->rewind();
        $stream->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $stream->setCsvControl(";", "\"", "\\");
        $header = $stream->current();
        if (!is_array($header)) {
            throw new \Exception("Invalid CSV header: {$header}");
        }
        $has_column_by_value = [];
        foreach ($header as $value) {
            $has_column_by_value[$value] = true;
        }
        /** @var array<MemberCsvColumn> */
        $missing_columns = [];
        foreach ($this->csv_columns as $key => $value) {
            if (!($has_column_by_value[$value] ?? false)) {
                $missing_columns[] = $key;
            }
        }
        if (count($missing_columns) > 0) {
            $pretty_columns = [];
            foreach ($missing_columns as $key) {
                $value = $this->csv_columns[$key];
                $pretty_columns[] = "{$key} ({$value})";
            }
            $pretty_columns_str = implode(', ', $pretty_columns);
            throw new \InvalidArgumentException("Missing columns in CSV: {$pretty_columns_str}");
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

    protected function convertMemberCsvEncoding(string $csv_content): string {
        if (strpos($csv_content, "Ge\x8andert") !== false) {
            $converted = @iconv('macintosh', 'UTF-8', $csv_content);
            if ($converted) {
                return $converted;
            }
        }
        $encoding = mb_detect_encoding($csv_content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        $utf8_csv_content = mb_convert_encoding($csv_content, 'UTF-8', $encoding ?: 'ISO-8859-1');
        $this->generalUtils()->checkNotFalse($utf8_csv_content, 'Could not convert to UTF-8');
        return $utf8_csv_content;
    }

    /** @param array<string, string> $member */
    public function getMemberIdent(array $member): ?string {
        return $member[$this->csv_columns['ident']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberUsername(array $member): ?string {
        return $member[$this->csv_columns['username']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberFirstName(array $member): ?string {
        return $member[$this->csv_columns['first_name']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberLastName(array $member): ?string {
        return $member[$this->csv_columns['last_name']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberCompany(array $member): ?string {
        return $member[$this->csv_columns['company']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberStatus(array $member): ?string {
        return $member[$this->csv_columns['status']] ?? null;
    }

    /** @param array<string, string> $member */
    public function getMemberJoinedOn(array $member): ?\DateTime {
        return $this->parseMemberDate($member[$this->csv_columns['joined_on']] ?? '');
    }

    /** @param array<string, string> $member */
    public function getMemberLeftOn(array $member): ?\DateTime {
        return $this->parseMemberDate($member[$this->csv_columns['left_on']] ?? '');
    }

    /** @param array<string, string> $member */
    public function getMemberStatusEnabled(array $member): bool {
        $status = $this->getMemberStatus($member);
        return $this->is_enabled_by_status[$status ?? ''] ?? false;
    }

    private function parseMemberDate(string $value): ?\DateTime {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        $range = $this->dateUtils()->parseDateTimeRange($trimmed);
        return $range !== null ? $range['start'] : null;
    }

    /** @return array<string, string> */
    public function getMemberData(Member $member): array {
        $decoded_member_data = json_decode($member->getData(), true);
        return is_array($decoded_member_data) ? $decoded_member_data : [];
    }

    public function update(Member $member, ?User $user): void {
        if (!$user) {
            return;
        }
        if ($member->getUser()?->getId() !== $user->getId()) {
            $this->log()->error("Update requested for member {$member->getIdent()} ({$member->getUser()?->getId()} !== {$user->getId()})");
            return;
        }
        $member_data = $this->getMemberData($member);
        $this->updateUserMembershipDates($user, $member_data);
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

    /**
     * Whether the member was (counted as) a member at the given point in time.
     */
    public function isMemberActiveAt(Member $member, \DateTime $at): bool {
        $member_data = $this->getMemberData($member);
        $joined_on = $this->getMemberJoinedOn($member_data);
        if ($joined_on !== null && $joined_on > $at) {
            return false; // Not yet joined
        }
        $left_on = $this->getMemberLeftOn($member_data);
        if ($left_on !== null && $left_on < $at) {
            return false; // Already left
        }
        // The record was removed from the club (soft-deleted) before `$at`. Deliberately
        // not `$member->exists($at)`, because `created_at` only says when the record was
        // imported, not since when the person is a member.
        $deleted_at = $member->getDeletedAt();
        return $deleted_at === null || $deleted_at >= $at;
    }

    /**
     * The membership period is stored on the user as well, so that e.g. measurements
     * of members with a user account can be calculated from the users table.
     *
     * @param array<string, string> $member
     */
    protected function updateUserMembershipDates(User $user, array $member): void {
        $joined_on = $this->getMemberJoinedOn($member);
        if (!$this->isSameDate($user->getJoinedOn(), $joined_on)) {
            $this->log()->info("User {$user->getUsername()}: joined_on was updated.");
            $user->setJoinedOn($joined_on);
        }
        $left_on = $this->getMemberLeftOn($member);
        if (!$this->isSameDate($user->getLeftOn(), $left_on)) {
            $this->log()->info("User {$user->getUsername()}: left_on was updated.");
            $user->setLeftOn($left_on);
        }
    }

    private function isSameDate(?\DateTime $existing, ?\DateTime $new): bool {
        return $existing?->format('Y-m-d') === $new?->format('Y-m-d');
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
