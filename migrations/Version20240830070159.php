<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240830070159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission ADD site_id INT NOT NULL, ADD is_authorized TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_E04992AAF6BD1646 ON permission (site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAF6BD1646');
        $this->addSql('DROP INDEX IDX_E04992AAF6BD1646 ON permission');
        $this->addSql('ALTER TABLE permission DROP site_id, DROP is_authorized');
    }
}
