<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523090825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, label VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, cp VARCHAR(20) NOT NULL, city VARCHAR(100) NOT NULL, INDEX IDX_D4E6F81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchase ADD address_id INT NOT NULL, DROP firstname, DROP lastname, DROP billing_address, DROP billing_cp, DROP billing_city');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BF5B7AF75 ON purchase (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF5B7AF75');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81A76ED395');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP INDEX IDX_6117D13BF5B7AF75 ON purchase');
        $this->addSql('ALTER TABLE purchase ADD firstname VARCHAR(100) NOT NULL, ADD lastname VARCHAR(100) NOT NULL, ADD billing_address VARCHAR(255) NOT NULL, ADD billing_cp VARCHAR(20) NOT NULL, ADD billing_city VARCHAR(50) NOT NULL, DROP address_id');
    }
}
