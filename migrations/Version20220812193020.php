<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812193020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) DEFAULT NULL, percent INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE settings DROP active_parrainage, DROP number_user_room_per_page, DROP parrain_credit_offer, DROP fiole_credit_offer, DROP active_register_drift, DROP register_drift_credit_offer, DROP parrainage_number_booking_required, DROP register_drift_number_booking_required');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plan');
        $this->addSql('ALTER TABLE settings ADD active_parrainage TINYINT(1) DEFAULT NULL, ADD number_user_room_per_page INT DEFAULT NULL, ADD parrain_credit_offer INT DEFAULT NULL, ADD fiole_credit_offer INT NOT NULL, ADD active_register_drift TINYINT(1) DEFAULT NULL, ADD register_drift_credit_offer INT DEFAULT NULL, ADD parrainage_number_booking_required INT DEFAULT NULL, ADD register_drift_number_booking_required INT DEFAULT NULL');
    }
}
