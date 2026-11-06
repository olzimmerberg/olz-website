<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Measurement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:update-measurements')]
class UpdateMeasurementsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $db = $this->dbUtils()->getDb();
        // There is no OUTER JOIN in MySQL
        $sql = <<<'ZZZZZZZZZZ'
            SELECT m.id AS m_id, u.id AS u_id, m.on_off AS m_on, u.on_off AS u_on
            FROM members m LEFT JOIN users u ON (m.user_id = u.id)
            UNION
            SELECT m.id AS m_id, u.id AS u_id, m.on_off AS m_on, u.on_off AS u_on
            FROM users u LEFT JOIN members m ON (u.id = m.user_id)
            ZZZZZZZZZZ;
        $result = $db->query($sql);
        $this->generalUtils()->checkNotBool($result, "Database query failed: {$sql}");
        $num_member_users = 0;
        $num_nonmember_users = 0;
        $num_nonuser_members = 0;
        for ($i = 0; $i < $result->num_rows; $i++) {
            $row = $result->fetch_assoc() ?? false;
            $this->generalUtils()->checkNotFalse($row, "Invalid row");
            $is_user = (bool) $row['u_id'];
            $is_member = (bool) $row['m_id'];
            if ($is_user && !$row['u_on']) {
                continue; // Skip deleted user
            }
            if ($is_member && !$row['m_on']) {
                continue; // Skip deleted member
            }
            if ($is_user && $is_member) {
                $num_member_users++;
            }
            if ($is_user && !$is_member) {
                $num_nonmember_users++;
            }
            if (!$is_user && $is_member) {
                $num_nonuser_members++;
            }
        }

        $year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);
        $measurement_repo->set('num_member_users', $year, $month, $num_member_users);
        $measurement_repo->set('num_nonmember_users', $year, $month, $num_nonmember_users);
        $measurement_repo->set('num_nonuser_members', $year, $month, $num_nonuser_members);
        $this->logAndOutput("Users & Members measurements updated:");
        $this->logAndOutput("  num_member_users: {$num_member_users}");
        $this->logAndOutput("  num_nonmember_users: {$num_nonmember_users}");
        $this->logAndOutput("  num_nonuser_members: {$num_nonuser_members}");

        $this->logAndOutput("Measurements updated.");

        return Command::SUCCESS;
    }
}
