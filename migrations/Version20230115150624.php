<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230115150624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking_search_token (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cancel_payout (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, owner_id INT DEFAULT NULL, amount INT DEFAULT NULL, state INT DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8C0932D882EA2E54 (commande_id), INDEX IDX_8C0932D87E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cancel_payout ADD CONSTRAINT FK_8C0932D882EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE cancel_payout ADD CONSTRAINT FK_8C0932D87E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE payment ADD phone VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE booking_search_token');
        $this->addSql('DROP TABLE cancel_payout');
        $this->addSql('ALTER TABLE payment DROP phone, DROP email');
    }
}
