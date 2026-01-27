<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260127134010 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add last_control_code to SOLV results';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE solv_results ADD last_control_code INT NOT NULL');

        $solv_results = $this->connection->fetchAllAssociative('SELECT id, splits FROM solv_results');
        foreach ($solv_results as $solv_result) {
            $args = [
                'id' => $solv_result['id'],
                'last_control_code' => $this->parse_last_control_code($solv_result['splits']),
            ];
            $this->addSql("UPDATE solv_results SET last_control_code=:last_control_code WHERE id=:id;", $args);
        }
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE solv_results DROP last_control_code');
    }

    protected function parse_last_control_code(string $splits): int {
        // Last control and finish are on the same line
        $has_match = preg_match('/ ([0-9]+)\s+[0-9.:]+\s*\([0-9]+\)\s*Zi /', $splits, $matches);
        if ($has_match) {
            return intval($matches[1]);
        }
        // Last control and finish are NOT on the same line
        $has_match = preg_match('/ ([0-9]+)\s+[0-9.:]+\s*\([0-9]+\)\s*\n.+\n.+\n\s*Zi /', $splits, $matches);
        if ($has_match) {
            return intval($matches[1]);
        }
        return 0;
    }
}
