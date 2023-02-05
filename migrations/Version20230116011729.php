<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230116011729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD cancelation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE5A85444 FOREIGN KEY (cancelation_id) REFERENCES cancelation (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE5A85444 ON booking (cancelation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE5A85444');
        $this->addSql('DROP INDEX IDX_E00CEDDE5A85444 ON booking');
        $this->addSql('ALTER TABLE booking DROP cancelation_id');
    }
}
