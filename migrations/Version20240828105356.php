<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828105356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site ADD category_site_id INT NOT NULL');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E431655638 FOREIGN KEY (category_site_id) REFERENCES category_site (id)');
        $this->addSql('CREATE INDEX IDX_694309E431655638 ON site (category_site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E431655638');
        $this->addSql('DROP INDEX IDX_694309E431655638 ON site');
        $this->addSql('ALTER TABLE site DROP category_site_id');
    }
}
