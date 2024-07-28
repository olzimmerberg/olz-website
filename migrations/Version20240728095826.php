<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240728095826 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate Termin::typ, TerminTemplate::typ to TerminLabel map';
    }

    public function up(Schema $schema): void {
        $termin_labels = $this->connection->fetchAllAssociative('SELECT id, ident FROM termin_labels');
        $label_id_by_ident = [];
        foreach ($termin_labels as $termin_label) {
            $label_id_by_ident[$termin_label['ident']] = $termin_label['id'];
        }

        $termine = $this->connection->fetchAllAssociative('SELECT id, typ FROM termine');
        foreach ($termine as $termin) {
            $types = explode(' ', $termin['typ'] ?? '');
            foreach ($types as $type) {
                $label_id = $label_id_by_ident[$type] ?? null;
                if ($label_id !== null) {
                    $args = [
                        'termin_id' => $termin['id'],
                        'label_id' => $label_id,
                    ];
                    $this->addSql("INSERT INTO termin_label_map (termin_id, label_id) VALUES (:termin_id, :label_id);", $args);
                }
            }
        }

        $termin_templates = $this->connection->fetchAllAssociative('SELECT id, types FROM termin_templates');
        foreach ($termin_templates as $termin_template) {
            $types = explode(' ', $termin_template['types'] ?? '');
            foreach ($types as $type) {
                $label_id = $label_id_by_ident[$type] ?? null;
                if ($label_id !== null) {
                    $args = [
                        'termin_template_id' => $termin_template['id'],
                        'label_id' => $label_id,
                    ];
                    $this->addSql("INSERT INTO termin_template_label_map (termin_template_id, label_id) VALUES (:termin_template_id, :label_id);", $args);
                }
            }
        }
    }

    public function down(Schema $schema): void {
    }
}
