<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829065320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, site_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E04992AAA76ED395 (user_id), INDEX IDX_E04992AAF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
    //     $this->addSql('ALTER TABLE category_site CHANGE description description TINYTEXT NOT NULL');
    //     $this->addSql('ALTER TABLE site DROP category');
    //     $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E431655638 FOREIGN KEY (category_site_id) REFERENCES category_site (id)');
    //     $this->addSql('CREATE INDEX IDX_694309E431655638 ON site (category_site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAA76ED395');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAF6BD1646');
        $this->addSql('DROP TABLE permission');
        // $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E431655638');
        // $this->addSql('DROP INDEX IDX_694309E431655638 ON site');
        // $this->addSql('ALTER TABLE site ADD category VARCHAR(255) NOT NULL');
        // $this->addSql('ALTER TABLE category_site CHANGE description description VARCHAR(255) NOT NULL');
    }
}
