<?php

// =============================================================================
// iCal-Datei generieren mit Terminen des aktuellen Jahres.
// Dieses Script wird immer beim Sichern und beim Löschen eines Termins
// aufgerufen.
// =============================================================================

namespace Olz\Termine\Components\OlzICal;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminReaction;

/** @extends OlzComponent<array<string, mixed>> */
class OlzICal extends OlzComponent {
    public function getHtml(mixed $args): string {
        $jahr = $this->dateUtils()->getCurrentDateInFormat('Y');
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $now_fmt = date('Ymd\THis\Z');
        $host = $this->envUtils()->getEmailForwardingHost();
        $user = $this->authUtils()->getTokenUser();

        // Termine abfragen
        $termin_class = Termin::class;
        $termin_reaction_class = TerminReaction::class;
        // TODO: Add `HAVING MIN(tyes.emoji) IS NOT NULL OR MIN(tno.emoji) IS NULL`
        $dql = <<<ZZZZZZZZZZ
            SELECT t, MIN(tyes.emoji), MIN(tno.emoji)
            FROM {$termin_class} t
                LEFT JOIN {$termin_reaction_class} tyes ON (
                    tyes.user = :user_id
                    AND tyes.termin = t.id
                    AND tyes.emoji IN ('👍', '✅', '✔️')
                )
                LEFT JOIN {$termin_reaction_class} tno ON (
                    tno.user = :user_id
                    AND tno.termin = t.id
                    AND tno.emoji IN ('👎', '❎', '❌', '😢', '🚫')
                )
            WHERE
                t.start_date >= :start_date
                AND t.on_off = '1'
            GROUP BY t.id
            ORDER BY t.start_date ASC, t.start_time ASC
            ZZZZZZZZZZ;
        $termine = $this->entityManager()
            ->createQuery($dql)
            ->setParameter('user_id', $user?->getId())
            ->setParameter('start_date', "{$jahr}-01-01")
            ->getResult()
        ;

        // ical-Kalender
        $ical = "BEGIN:VCALENDAR".
        "\r\nPRODID:OL Zimmerberg Termine".
        "\r\nVERSION:2.0".
        "\r\nMETHOD:PUBLISH".
        "\r\nCALSCALE:GREGORIAN".
        "\r\nX-WR-CALNAME:OL Zimmerberg Termine".
        "\r\nX-WR-TIMEZONE:Europe/Zurich";

        // Termine
        foreach ($termine as $termin_row) {
            [$termin, $yes_emoji, $no_emoji] = $termin_row;

            $id = $termin->getId();
            $start_date = $termin->getStartDate();
            $end_date = $termin->getEndDate() ?? $start_date;
            $duration_days = ($end_date->getTimestamp() - $start_date->getTimestamp()) / 86400;
            $should_split = $duration_days > 8;
            $solv_id = $termin->getSolvId();

            $olz_url = "{$base_href}{$code_href}termine/{$id}";
            $solv_url = "https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id={$solv_id}";

            $links = "OLZ-Termin: {$olz_url}";
            $attach = "\r\nATTACH;FMTTYPE=text/html:{$olz_url}";
            $links .= $solv_id ? "\nSOLV-Termin: {$solv_url}" : "";
            $attach .= $solv_id ? "\r\nATTACH;FMTTYPE=text/html:{$solv_url}" : "";

            $plus_one_day = \DateInterval::createFromDateString("+1 days");
            $end_date_end = (new \DateTime($end_date->format('Y-m-d')))->add($plus_one_day);
            $start_date_fmt = $this->dateUtils()->olzDate('jjjjmmtt', $start_date);
            $end_date_fmt = $this->dateUtils()->olzDate('jjjjmmtt', $end_date);
            $end_date_end_fmt = $this->dateUtils()->olzDate('jjjjmmtt', $end_date_end);
            $modified_fmt = $termin->getLastModifiedAt()->format('Ymd\THis\Z');
            $created_fmt = $termin->getCreatedAt()->format('Ymd\THis\Z');
            $yes_emoji_suffix = $yes_emoji ? " {$yes_emoji}" : '';
            $no_emoji_suffix = $no_emoji ? " {$no_emoji}" : '';
            $title_fmt = "{$termin->getTitle()}{$yes_emoji_suffix}{$no_emoji_suffix}";
            $description_fmt = $this->escapeText("{$termin->getText()}\n{$links}");
            $label_idents = implode(', ', array_map(
                fn ($label) => "{$label->getIdent()}",
                [...$termin->getLabels()],
            ));
            if ($should_split) {
                $ical .=
                "\r\nBEGIN:VEVENT".
                "\r\nDTSTART;VALUE=DATE:{$start_date_fmt}".
                "\r\nDTEND;VALUE=DATE:{$start_date_fmt}".
                "\r\nDTSTAMP:{$now_fmt}".
                "\r\nLAST-MODIFIED:{$modified_fmt}".
                "\r\nCREATED:{$created_fmt}".
                "\r\nSUMMARY:{$title_fmt} (Beginn)".
                "\r\nDESCRIPTION:{$description_fmt}".
                "\r\nCATEGORIES:{$label_idents}".
                $attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}_start@{$host}".
                "\r\nEND:VEVENT".
                "\r\nBEGIN:VEVENT".
                "\r\nDTSTART;VALUE=DATE:{$end_date_fmt}".
                "\r\nDTEND;VALUE=DATE:{$end_date_fmt}".
                "\r\nDTSTAMP:{$now_fmt}".
                "\r\nLAST-MODIFIED:{$modified_fmt}".
                "\r\nCREATED:{$created_fmt}".
                "\r\nSUMMARY:{$title_fmt} (Ende)".
                "\r\nDESCRIPTION:{$description_fmt}".
                "\r\nCATEGORIES:{$label_idents}".
                $attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}_end@{$host}".
                "\r\nEND:VEVENT";
            } else {
                $ical .=
                "\r\nBEGIN:VEVENT".
                "\r\nDTSTART;VALUE=DATE:{$start_date_fmt}".
                "\r\nDTEND;VALUE=DATE:{$end_date_end_fmt}".
                "\r\nDTSTAMP:{$now_fmt}".
                "\r\nLAST-MODIFIED:{$modified_fmt}".
                "\r\nCREATED:{$created_fmt}".
                "\r\nSUMMARY:{$title_fmt}".
                "\r\nDESCRIPTION:{$description_fmt}".
                "\r\nCATEGORIES:{$label_idents}".
                $attach.
                "\r\nCLASS:PUBLIC".
                "\r\nUID:olz_termin_{$id}@{$host}".
                "\r\nEND:VEVENT";
            }
        }

        $ical .= "\r\nEND:VCALENDAR";

        return $ical;
    }

    protected function escapeText(string $text): string {
        return preg_replace("/(\r\n|\n|\r)/", "\\n", $text) ?: '';
    }
}
