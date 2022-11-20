<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107142503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, main_text VARCHAR(255) DEFAULT NULL, secondary_text VARCHAR(255) DEFAULT NULL, route LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', type VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, bg_color VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, file_mobile_name VARCHAR(255) DEFAULT NULL, file_mobile_size INT DEFAULT NULL, file_mobile_mime_type VARCHAR(255) DEFAULT NULL, file_mobile_original_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD file_name VARCHAR(255) DEFAULT NULL, ADD file_size INT DEFAULT NULL, ADD file_mime_type VARCHAR(255) DEFAULT NULL, ADD file_original_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE banner');
        $this->addSql('ALTER TABLE city DROP file_name, DROP file_size, DROP file_mime_type, DROP file_original_name');
    }
}
