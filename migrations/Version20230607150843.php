<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230607150843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating ADD platform_id INT NOT NULL');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622FFE6496F FOREIGN KEY (platform_id) REFERENCES plateform (id)');
        $this->addSql('CREATE INDEX IDX_D8892622FFE6496F ON rating (platform_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622FFE6496F');
        $this->addSql('DROP INDEX IDX_D8892622FFE6496F ON rating');
        $this->addSql('ALTER TABLE rating DROP platform_id');
    }
}
