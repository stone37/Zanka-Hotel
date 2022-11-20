<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819173758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cancelation (id INT AUTO_INCREMENT NOT NULL, state INT DEFAULT NULL, result INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_interval (id INT AUTO_INCREMENT NOT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE zone');
        $this->addSql('ALTER TABLE hostel ADD checkin_time_id INT NOT NULL, ADD checkout_time_id INT NOT NULL, ADD cancellation_policy_id INT NOT NULL, ADD children TINYINT(1) DEFAULT NULL, DROP average_rating, DROP property_average_rating, DROP equipment_average_rating, DROP personal_average_rating, DROP comfort_average_rating, DROP price_average_rating, DROP location_average_rating, DROP parking_price, DROP breakfast_price, DROP cancel_free_of_charge, DROP checkin_time, DROP checkout_time, CHANGE star_number star_number INT DEFAULT NULL, CHANGE animals_allowed animals_allowed TINYINT(1) DEFAULT NULL, CHANGE breakfast breakfast TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB167F222ED08 FOREIGN KEY (checkin_time_id) REFERENCES time_interval (id)');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB167A2813942 FOREIGN KEY (checkout_time_id) REFERENCES time_interval (id)');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB167F0C13F52 FOREIGN KEY (cancellation_policy_id) REFERENCES cancelation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38FBB167F222ED08 ON hostel (checkin_time_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38FBB167A2813942 ON hostel (checkout_time_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38FBB167F0C13F52 ON hostel (cancellation_policy_id)');
        $this->addSql('ALTER TABLE location ADD detail VARCHAR(255) DEFAULT NULL, CHANGE zone town VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB167F0C13F52');
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB167F222ED08');
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB167A2813942');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_A0EBC0078BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC0078BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE cancelation');
        $this->addSql('DROP TABLE time_interval');
        $this->addSql('DROP INDEX UNIQ_38FBB167F222ED08 ON hostel');
        $this->addSql('DROP INDEX UNIQ_38FBB167A2813942 ON hostel');
        $this->addSql('DROP INDEX UNIQ_38FBB167F0C13F52 ON hostel');
        $this->addSql('ALTER TABLE hostel ADD average_rating INT DEFAULT NULL, ADD property_average_rating INT DEFAULT NULL, ADD equipment_average_rating INT DEFAULT NULL, ADD personal_average_rating INT DEFAULT NULL, ADD comfort_average_rating INT DEFAULT NULL, ADD price_average_rating INT DEFAULT NULL, ADD location_average_rating INT DEFAULT NULL, ADD parking_price INT DEFAULT NULL, ADD breakfast_price INT DEFAULT NULL, ADD cancel_free_of_charge VARCHAR(255) DEFAULT NULL, ADD checkin_time TIME DEFAULT NULL, ADD checkout_time TIME DEFAULT NULL, DROP checkin_time_id, DROP checkout_time_id, DROP cancellation_policy_id, DROP children, CHANGE star_number star_number VARCHAR(50) DEFAULT NULL, CHANGE breakfast breakfast VARCHAR(255) DEFAULT NULL, CHANGE animals_allowed animals_allowed VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD zone VARCHAR(255) DEFAULT NULL, DROP town, DROP detail');
    }
}
