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
     * Whether the member had joined and not yet left during the given month.
     */
    public function isMemberActiveInMonth(Member $member, int $year, int $month): bool {
        $member_data = $this->getMemberData($member);
        // Membership ends with whichever comes first: leaving the club or deleting the record.
        $until = $this->getEarlierDate(
            $this->getMemberLeftOn($member_data),
            $member->getDeletedAt(),
        );
        return $this->isDateRangeActiveInMonth(
            $this->getMemberJoinedOn($member_data),
            $until,
            $year,
            $month
        );
    }

    /**
     * Whether the user account existed during the given month.
     */
    public function isUserActiveInMonth(User $user, int $year, int $month): bool {
        return $this->isDateRangeActiveInMonth(
            $user->getCreatedAt(),
            $user->getDeletedAt(),
            $year,
            $month
        );
    }

    /**
     * Whether the user had a club membership during the given month: the account must already
     * exist and the membership dates (`joined_on`, `left_on`) are maintained by the members import.
     */
    public function isUserMembershipActiveInMonth(User $user, int $year, int $month): bool {
        $joined_on = $user->getJoinedOn();
        if ($joined_on === null) {
            // Users without `joined_on` are not members.
            return false;
        }
        // A membership can only count once the user account exists.
        $from = $this->getLaterDate($joined_on, $user->getCreatedAt());
        // Membership ends with whichever comes first: leaving the club or deleting the account.
        $until = $this->getEarlierDate($user->getLeftOn(), $user->getDeletedAt());
        return $this->isDateRangeActiveInMonth($from, $until, $year, $month);
    }

    /**
     * Whether the date range from `$from` (inclusive) to `$until` (inclusive) overlaps the given month.
     * A `null` value means "unbounded": `$from` = existed before, `$until` = still exists.
     */
    private function isDateRangeActiveInMonth(?\DateTime $from, ?\DateTime $until, int $year, int $month): bool {
        $month_start = new \DateTime(sprintf('%04d-%02d-01 00:00:00', $year, $month));
        $next_month_start = (clone $month_start)->modify('+1 month');
        if ($from !== null && $from >= $next_month_start) {
            return false;
        }
        return $until === null || $until >= $month_start;
    }

    /** The earlier of two dates, where `null` means "unbounded". */
    private function getEarlierDate(?\DateTime $a, ?\DateTime $b): ?\DateTime {
        if ($a === null) {
            return $b;
        }
        if ($b === null) {
            return $a;
        }
        return $a < $b ? $a : $b;
    }

    /** The later of two dates. */
    private function getLaterDate(\DateTime $a, \DateTime $b): \DateTime {
        return $a > $b ? $a : $b;
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
