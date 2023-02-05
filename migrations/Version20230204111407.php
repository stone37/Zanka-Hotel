<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204111407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review CHANGE personal_rating personal_rating DOUBLE PRECISION DEFAULT NULL, CHANGE equipment_rating equipment_rating DOUBLE PRECISION DEFAULT NULL, CHANGE property_rating property_rating DOUBLE PRECISION DEFAULT NULL, CHANGE comfort_rating comfort_rating DOUBLE PRECISION DEFAULT NULL, CHANGE price_rating price_rating DOUBLE PRECISION DEFAULT NULL, CHANGE location_rating location_rating DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review CHANGE personal_rating personal_rating INT DEFAULT NULL, CHANGE equipment_rating equipment_rating INT DEFAULT NULL, CHANGE property_rating property_rating INT DEFAULT NULL, CHANGE comfort_rating comfort_rating INT DEFAULT NULL, CHANGE price_rating price_rating INT DEFAULT NULL, CHANGE location_rating location_rating INT DEFAULT NULL');
    }
}
