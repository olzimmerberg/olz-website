<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Olz\Utils\MapUtils;

final class Version20240610194821 extends AbstractMigration {
    public function getDescription(): string {
        return 'Populate latitude, longitude';
    }

    public function up(Schema $schema): void {
        $coord = new MapUtils();
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM karten');
        foreach ($rows as $row) {
            $args = [
                'id' => $row['id'],
                'latitude' => $coord->CHtoWGSlat($row['center_x'], $row['center_y']),
                'longitude' => $coord->CHtoWGSlng($row['center_x'], $row['center_y']),
            ];
            $this->addSql("UPDATE karten SET latitude = :latitude, longitude = :longitude WHERE id = :id", $args);
        }
    }

    public function down(Schema $schema): void {
    }
}
