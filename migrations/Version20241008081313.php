<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008081313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_site ADD id_login VARCHAR(255) NOT NULL, ADD class_site VARCHAR(255) NOT NULL, ADD id_mdp VARCHAR(255) NOT NULL, ADD class_mdp VARCHAR(255) NOT NULL, ADD id_submit VARCHAR(255) NOT NULL, ADD class_submit VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE site ADD id_login VARCHAR(255) NOT NULL, ADD class_login VARCHAR(255) NOT NULL, ADD id_mdp VARCHAR(255) NOT NULL, ADD class_mdp VARCHAR(255) NOT NULL, ADD id_submit VARCHAR(255) NOT NULL, ADD class_submit VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_site DROP id_login, DROP class_site, DROP id_mdp, DROP class_mdp, DROP id_submit, DROP class_submit');
        $this->addSql('ALTER TABLE site DROP id_login, DROP class_login, DROP id_mdp, DROP class_mdp, DROP id_submit, DROP class_submit');
    }
}
