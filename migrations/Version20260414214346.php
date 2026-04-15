<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Olz\Utils\MapUtils;

final class Version20260414214346 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate termine to latlng';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE termine ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
        $coord = new MapUtils();
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM termine');
        foreach ($rows as $row) {
            $args = ($row['xkoord'] > 0 && $row['ykoord'] > 0) ? [
                'id' => $row['id'],
                'latitude' => $coord->CHtoWGSlat($row['xkoord'], $row['ykoord']),
                'longitude' => $coord->CHtoWGSlng($row['xkoord'], $row['ykoord']),
            ] : [
                'id' => $row['id'],
                'latitude' => null,
                'longitude' => null,
            ];
            $this->addSql("UPDATE termine SET latitude = :latitude, longitude = :longitude WHERE id = :id", $args);
        }
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP latitude, DROP longitude');
    }
}
