<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601144530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, label VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, cp VARCHAR(20) NOT NULL, city VARCHAR(100) NOT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, price DOUBLE PRECISION NOT NULL, date_release DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_genre (game_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_B1634A77E48FD905 (game_id), INDEX IDX_B1634A774296D31F (genre_id), PRIMARY KEY(game_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_plateform (game_id INT NOT NULL, plateform_id INT NOT NULL, INDEX IDX_DC247165E48FD905 (game_id), INDEX IDX_DC247165CCAA542F (plateform_id), PRIMARY KEY(game_id, plateform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plateform (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, label VARCHAR(50) NOT NULL, logo LONGTEXT NOT NULL, INDEX IDX_E82B06712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, address_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_paid TINYINT(1) NOT NULL, method VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, stripe_session_id VARCHAR(255) DEFAULT NULL, paypal_order_id VARCHAR(255) DEFAULT NULL, delivery VARCHAR(255) NOT NULL, user_full_name VARCHAR(255) NOT NULL, INDEX IDX_6117D13BA76ED395 (user_id), INDEX IDX_6117D13BF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recap_details (id INT AUTO_INCREMENT NOT NULL, order_product_id INT NOT NULL, quantity INT NOT NULL, game_label VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, total_recap VARCHAR(255) NOT NULL, platform_label VARCHAR(255) NOT NULL, game_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_1D1FD69F65E9B0F (order_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, purchase_id INT DEFAULT NULL, plateform_id INT NOT NULL, license_key VARCHAR(255) NOT NULL, date_availability DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_4B365660E48FD905 (game_id), INDEX IDX_4B365660558FBEB9 (purchase_id), INDEX IDX_4B365660CCAA542F (plateform_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, avatar LONGTEXT DEFAULT NULL, is_verified TINYINT(1) NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip_address VARCHAR(255) NOT NULL, is_banned TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_genre ADD CONSTRAINT FK_B1634A77E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_genre ADD CONSTRAINT FK_B1634A774296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_plateform ADD CONSTRAINT FK_DC247165E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_plateform ADD CONSTRAINT FK_DC247165CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plateform ADD CONSTRAINT FK_E82B06712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE recap_details ADD CONSTRAINT FK_1D1FD69F65E9B0F FOREIGN KEY (order_product_id) REFERENCES purchase (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE game_genre DROP FOREIGN KEY FK_B1634A77E48FD905');
        $this->addSql('ALTER TABLE game_genre DROP FOREIGN KEY FK_B1634A774296D31F');
        $this->addSql('ALTER TABLE game_plateform DROP FOREIGN KEY FK_DC247165E48FD905');
        $this->addSql('ALTER TABLE game_plateform DROP FOREIGN KEY FK_DC247165CCAA542F');
        $this->addSql('ALTER TABLE plateform DROP FOREIGN KEY FK_E82B06712469DE2');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BA76ED395');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF5B7AF75');
        $this->addSql('ALTER TABLE recap_details DROP FOREIGN KEY FK_1D1FD69F65E9B0F');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660E48FD905');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660558FBEB9');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660CCAA542F');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_genre');
        $this->addSql('DROP TABLE game_plateform');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE plateform');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE recap_details');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
