<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829084535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission ADD type_permission_id INT NOT NULL, DROP type');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA8E34BBFF FOREIGN KEY (type_permission_id) REFERENCES type_permission (id)');
        $this->addSql('CREATE INDEX IDX_E04992AA8E34BBFF ON permission (type_permission_id)');
        $this->addSql('ALTER TABLE type_permission DROP permission_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA8E34BBFF');
        $this->addSql('DROP INDEX IDX_E04992AA8E34BBFF ON permission');
        $this->addSql('ALTER TABLE permission ADD type VARCHAR(255) NOT NULL, DROP type_permission_id');
        $this->addSql('ALTER TABLE type_permission ADD permission_id INT NOT NULL');
    }
}
