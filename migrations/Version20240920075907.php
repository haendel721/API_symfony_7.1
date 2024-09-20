<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920075907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_site DROP FOREIGN KEY FK_AF3182A1F6BD1646');
        $this->addSql('DROP INDEX UNIQ_AF3182A1F6BD1646 ON login_site');
        $this->addSql('ALTER TABLE login_site DROP site_id');
        $this->addSql('ALTER TABLE site ADD login_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E45CB2E05D FOREIGN KEY (login_id) REFERENCES login_site (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E45CB2E05D ON site (login_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_site ADD site_id INT NOT NULL');
        $this->addSql('ALTER TABLE login_site ADD CONSTRAINT FK_AF3182A1F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AF3182A1F6BD1646 ON login_site (site_id)');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E45CB2E05D');
        $this->addSql('DROP INDEX UNIQ_694309E45CB2E05D ON site');
        $this->addSql('ALTER TABLE site DROP login_id');
    }
}
