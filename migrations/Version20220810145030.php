<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220810145030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, hostel_id INT NOT NULL, room_id INT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, checkin DATETIME DEFAULT NULL, checkout DATETIME DEFAULT NULL, days INT DEFAULT NULL, room_number INT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, adult INT DEFAULT NULL, children INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, confirmed_at DATETIME DEFAULT NULL, cancelled_at DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, taxe_amount INT DEFAULT NULL, discount_amount INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E00CEDDE7E3C61F9 (owner_id), INDEX IDX_E00CEDDEFC68ACC0 (hostel_id), INDEX IDX_E00CEDDE54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) DEFAULT NULL, slug VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, discount_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, booking_id INT DEFAULT NULL, hostel_id INT NOT NULL, validated TINYINT(1) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, amount_total INT DEFAULT NULL, taxe_amount INT DEFAULT NULL, discount_amount INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6EEAA67D4C7C611F (discount_id), INDEX IDX_6EEAA67D7E3C61F9 (owner_id), UNIQUE INDEX UNIQ_6EEAA67D3301C60 (booking_id), INDEX IDX_6EEAA67DFC68ACC0 (hostel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, discount INT DEFAULT NULL, code VARCHAR(100) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, utilisation INT DEFAULT NULL, utiliser INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_verification (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, email VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_FE22358F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailing (id INT AUTO_INCREMENT NOT NULL, destinataire VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, groupe VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, equipment_group_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D338D5837E3C61F9 (owner_id), INDEX IDX_D338D583CE975D61 (equipment_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_group (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_849965217E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange_rate (id INT AUTO_INCREMENT NOT NULL, source_currency_id INT NOT NULL, target_currency_id INT NOT NULL, ratio DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E9521FAB45BD1D6 (source_currency_id), INDEX IDX_E9521FABBF1ECE7C (target_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, hostel_id INT NOT NULL, INDEX IDX_68C58ED97E3C61F9 (owner_id), INDEX IDX_68C58ED9FC68ACC0 (hostel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hostel (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, owner_id INT NOT NULL, location_id INT NOT NULL, name VARCHAR(180) DEFAULT NULL, slug VARCHAR(180) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(25) DEFAULT NULL, description LONGTEXT DEFAULT NULL, reference VARCHAR(50) DEFAULT NULL, star_number VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, code_postal VARCHAR(255) DEFAULT NULL, average_rating INT DEFAULT NULL, property_average_rating INT DEFAULT NULL, equipment_average_rating INT DEFAULT NULL, personal_average_rating INT DEFAULT NULL, comfort_average_rating INT DEFAULT NULL, price_average_rating INT DEFAULT NULL, location_average_rating INT DEFAULT NULL, parking TINYINT(1) DEFAULT NULL, parking_price INT DEFAULT NULL, animals_allowed VARCHAR(255) DEFAULT NULL, mobile_payment_allowed TINYINT(1) DEFAULT NULL, card_payment_allowed TINYINT(1) DEFAULT NULL, spoken_languages LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', breakfast VARCHAR(255) DEFAULT NULL, breakfast_price INT DEFAULT NULL, cancel_free_of_charge VARCHAR(255) DEFAULT NULL, checkin_time TIME DEFAULT NULL, checkout_time TIME DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', notifications_read_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_38FBB167E7927C74 (email), INDEX IDX_38FBB16712469DE2 (category_id), INDEX IDX_38FBB1677E3C61F9 (owner_id), UNIQUE INDEX UNIQ_38FBB16764D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hostel_equipment_group (hostel_id INT NOT NULL, equipment_group_id INT NOT NULL, INDEX IDX_7D7F24B5FC68ACC0 (hostel_id), INDEX IDX_7D7F24B5CE975D61 (equipment_group_id), PRIMARY KEY(hostel_id, equipment_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hostel_gallery (id INT AUTO_INCREMENT NOT NULL, hostel_id INT NOT NULL, extension VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_814B6E6CFC68ACC0 (hostel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) DEFAULT NULL, zone VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login_attempt (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_8C11C1B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_data (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE occupant (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, INDEX IDX_E7D9DBCA3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6B7BA4B67E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, price INT DEFAULT NULL, discount INT DEFAULT NULL, taxe INT DEFAULT NULL, refunded TINYINT(1) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6D28840D82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, slug VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, exclusive TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, INDEX IDX_C11D7DD17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_room (promotion_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_C465D904139DF194 (promotion_id), INDEX IDX_C465D90454177093 (room_id), PRIMARY KEY(promotion_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_action (id INT AUTO_INCREMENT NOT NULL, promotion_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, configuration LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_5276A7AF139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, hostel_id INT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, booking_number VARCHAR(180) DEFAULT NULL, title VARCHAR(180) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, personal_rating INT DEFAULT NULL, equipment_rating INT DEFAULT NULL, property_rating INT DEFAULT NULL, comfort_rating INT DEFAULT NULL, price_rating INT DEFAULT NULL, location_rating INT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_794381C67E3C61F9 (owner_id), INDEX IDX_794381C6FC68ACC0 (hostel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, hostel_id INT NOT NULL, taxe_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, room_number INT DEFAULT NULL, price INT DEFAULT NULL, original_price INT DEFAULT NULL, smoker VARCHAR(255) DEFAULT NULL, maximum_adults INT DEFAULT NULL, maximum_of_children INT DEFAULT NULL, area INT DEFAULT NULL, couchage VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_729F519BFC68ACC0 (hostel_id), INDEX IDX_729F519B1AB947A4 (taxe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_room_equipment_group (room_id INT NOT NULL, room_equipment_group_id INT NOT NULL, INDEX IDX_6B8FDBB254177093 (room_id), INDEX IDX_6B8FDBB25100F7A (room_equipment_group_id), PRIMARY KEY(room_id, room_equipment_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_supplement (room_id INT NOT NULL, supplement_id INT NOT NULL, INDEX IDX_35A803D554177093 (room_id), INDEX IDX_35A803D57793FA21 (supplement_id), PRIMARY KEY(room_id, supplement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_equipment (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, room_equipment_group_id INT NOT NULL, name VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, INDEX IDX_4F9135EA7E3C61F9 (owner_id), INDEX IDX_4F9135EA5100F7A (room_equipment_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_equipment_group (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_47DCEB777E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_gallery (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, extension VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AB09C4DA54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, base_currency_id INT NOT NULL, default_locale_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, facebook_address VARCHAR(255) DEFAULT NULL, twitter_address VARCHAR(255) DEFAULT NULL, instagram_address VARCHAR(255) DEFAULT NULL, youtube_address VARCHAR(255) DEFAULT NULL, linkedin_address VARCHAR(255) DEFAULT NULL, active_pub TINYINT(1) DEFAULT NULL, active_parrainage TINYINT(1) DEFAULT NULL, number_hostel_per_page INT DEFAULT NULL, number_room_per_page INT DEFAULT NULL, number_user_room_per_page INT DEFAULT NULL, number_user_hostel_favorite_per_page INT DEFAULT NULL, parrain_credit_offer INT DEFAULT NULL, fiole_credit_offer INT NOT NULL, active_register_drift TINYINT(1) DEFAULT NULL, register_drift_credit_offer INT DEFAULT NULL, parrainage_number_booking_required INT DEFAULT NULL, register_drift_number_booking_required INT DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E545A0C53101778E (base_currency_id), UNIQUE INDEX UNIQ_E545A0C5743BF776 (default_locale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplement (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, slug VARCHAR(180) DEFAULT NULL, price INT DEFAULT NULL, type INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_15A73C97E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxe (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(100) DEFAULT NULL, value DOUBLE PRECISION DEFAULT NULL, included_in_price TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_56322FE95E237E06 (name), INDEX IDX_56322FE97E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(180) DEFAULT NULL, firstname VARCHAR(180) DEFAULT NULL, lastname VARCHAR(180) DEFAULT NULL, phone VARCHAR(25) DEFAULT NULL, description LONGTEXT DEFAULT NULL, birth_day DATE DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, banned_at DATETIME DEFAULT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, is_verified TINYINT(1) DEFAULT NULL, subscribed_to_newsletter TINYINT(1) DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, notifications_read_at DATETIME DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, delete_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(100) DEFAULT NULL, slug VARCHAR(100) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, INDEX IDX_A0EBC0078BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEFC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5837E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583CE975D61 FOREIGN KEY (equipment_group_id) REFERENCES equipment_group (id)');
        $this->addSql('ALTER TABLE equipment_group ADD CONSTRAINT FK_849965217E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB45BD1D6 FOREIGN KEY (source_currency_id) REFERENCES currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FABBF1ECE7C FOREIGN KEY (target_currency_id) REFERENCES currency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED97E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9FC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB16712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB1677E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE hostel ADD CONSTRAINT FK_38FBB16764D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE hostel_equipment_group ADD CONSTRAINT FK_7D7F24B5FC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hostel_equipment_group ADD CONSTRAINT FK_7D7F24B5CE975D61 FOREIGN KEY (equipment_group_id) REFERENCES equipment_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hostel_gallery ADD CONSTRAINT FK_814B6E6CFC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE login_attempt ADD CONSTRAINT FK_8C11C1B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE occupant ADD CONSTRAINT FK_E7D9DBCA3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B67E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE promotion ADD CONSTRAINT FK_C11D7DD17E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE promotion_room ADD CONSTRAINT FK_C465D904139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotion_room ADD CONSTRAINT FK_C465D90454177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotion_action ADD CONSTRAINT FK_5276A7AF139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6FC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BFC68ACC0 FOREIGN KEY (hostel_id) REFERENCES hostel (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B1AB947A4 FOREIGN KEY (taxe_id) REFERENCES taxe (id)');
        $this->addSql('ALTER TABLE room_room_equipment_group ADD CONSTRAINT FK_6B8FDBB254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_room_equipment_group ADD CONSTRAINT FK_6B8FDBB25100F7A FOREIGN KEY (room_equipment_group_id) REFERENCES room_equipment_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_supplement ADD CONSTRAINT FK_35A803D554177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_supplement ADD CONSTRAINT FK_35A803D57793FA21 FOREIGN KEY (supplement_id) REFERENCES supplement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_equipment ADD CONSTRAINT FK_4F9135EA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room_equipment ADD CONSTRAINT FK_4F9135EA5100F7A FOREIGN KEY (room_equipment_group_id) REFERENCES room_equipment_group (id)');
        $this->addSql('ALTER TABLE room_equipment_group ADD CONSTRAINT FK_47DCEB777E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE room_gallery ADD CONSTRAINT FK_AB09C4DA54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C53101778E FOREIGN KEY (base_currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C5743BF776 FOREIGN KEY (default_locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE supplement ADD CONSTRAINT FK_15A73C97E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE taxe ADD CONSTRAINT FK_56322FE97E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC0078BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D3301C60');
        $this->addSql('ALTER TABLE occupant DROP FOREIGN KEY FK_E7D9DBCA3301C60');
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB16712469DE2');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC0078BAC62AF');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D82EA2E54');
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB45BD1D6');
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FABBF1ECE7C');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C53101778E');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4C7C611F');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583CE975D61');
        $this->addSql('ALTER TABLE hostel_equipment_group DROP FOREIGN KEY FK_7D7F24B5CE975D61');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEFC68ACC0');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFC68ACC0');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9FC68ACC0');
        $this->addSql('ALTER TABLE hostel_equipment_group DROP FOREIGN KEY FK_7D7F24B5FC68ACC0');
        $this->addSql('ALTER TABLE hostel_gallery DROP FOREIGN KEY FK_814B6E6CFC68ACC0');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6FC68ACC0');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BFC68ACC0');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C5743BF776');
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB16764D218E');
        $this->addSql('ALTER TABLE promotion_room DROP FOREIGN KEY FK_C465D904139DF194');
        $this->addSql('ALTER TABLE promotion_action DROP FOREIGN KEY FK_5276A7AF139DF194');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE54177093');
        $this->addSql('ALTER TABLE promotion_room DROP FOREIGN KEY FK_C465D90454177093');
        $this->addSql('ALTER TABLE room_room_equipment_group DROP FOREIGN KEY FK_6B8FDBB254177093');
        $this->addSql('ALTER TABLE room_supplement DROP FOREIGN KEY FK_35A803D554177093');
        $this->addSql('ALTER TABLE room_gallery DROP FOREIGN KEY FK_AB09C4DA54177093');
        $this->addSql('ALTER TABLE room_room_equipment_group DROP FOREIGN KEY FK_6B8FDBB25100F7A');
        $this->addSql('ALTER TABLE room_equipment DROP FOREIGN KEY FK_4F9135EA5100F7A');
        $this->addSql('ALTER TABLE room_supplement DROP FOREIGN KEY FK_35A803D57793FA21');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B1AB947A4');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE7E3C61F9');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D7E3C61F9');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358F675F31B');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5837E3C61F9');
        $this->addSql('ALTER TABLE equipment_group DROP FOREIGN KEY FK_849965217E3C61F9');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED97E3C61F9');
        $this->addSql('ALTER TABLE hostel DROP FOREIGN KEY FK_38FBB1677E3C61F9');
        $this->addSql('ALTER TABLE login_attempt DROP FOREIGN KEY FK_8C11C1B7E3C61F9');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_6B7BA4B67E3C61F9');
        $this->addSql('ALTER TABLE promotion DROP FOREIGN KEY FK_C11D7DD17E3C61F9');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67E3C61F9');
        $this->addSql('ALTER TABLE room_equipment DROP FOREIGN KEY FK_4F9135EA7E3C61F9');
        $this->addSql('ALTER TABLE room_equipment_group DROP FOREIGN KEY FK_47DCEB777E3C61F9');
        $this->addSql('ALTER TABLE supplement DROP FOREIGN KEY FK_15A73C97E3C61F9');
        $this->addSql('ALTER TABLE taxe DROP FOREIGN KEY FK_56322FE97E3C61F9');
        $this->addSql('DROP TABLE ban');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE email_verification');
        $this->addSql('DROP TABLE emailing');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE equipment_group');
        $this->addSql('DROP TABLE exchange_rate');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE hostel');
        $this->addSql('DROP TABLE hostel_equipment_group');
        $this->addSql('DROP TABLE hostel_gallery');
        $this->addSql('DROP TABLE locale');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE login_attempt');
        $this->addSql('DROP TABLE newsletter_data');
        $this->addSql('DROP TABLE occupant');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE promotion_room');
        $this->addSql('DROP TABLE promotion_action');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_room_equipment_group');
        $this->addSql('DROP TABLE room_supplement');
        $this->addSql('DROP TABLE room_equipment');
        $this->addSql('DROP TABLE room_equipment_group');
        $this->addSql('DROP TABLE room_gallery');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE supplement');
        $this->addSql('DROP TABLE taxe');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
