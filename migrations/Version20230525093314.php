<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525093314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase ADD is_paid TINYINT(1) NOT NULL, ADD method VARCHAR(255) NOT NULL, ADD reference VARCHAR(255) NOT NULL, ADD stripe_session_id VARCHAR(255) DEFAULT NULL, ADD paypal_order_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE create_at create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP is_paid, DROP method, DROP reference, DROP stripe_session_id, DROP paypal_order_id');
        $this->addSql('ALTER TABLE user CHANGE create_at create_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
