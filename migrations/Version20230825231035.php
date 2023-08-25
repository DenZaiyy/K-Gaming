<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825231035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recap_details ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE recap_details ADD CONSTRAINT FK_1D1FD6912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_1D1FD6912469DE2 ON recap_details (category_id)');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660558FBEB9');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recap_details DROP FOREIGN KEY FK_1D1FD6912469DE2');
        $this->addSql('DROP INDEX IDX_1D1FD6912469DE2 ON recap_details');
        $this->addSql('ALTER TABLE recap_details DROP category_id');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660558FBEB9');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
