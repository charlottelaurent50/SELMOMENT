<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619143711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domaine (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD domaine_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E54272FC9F FOREIGN KEY (domaine_id) REFERENCES domaine (id)');
        $this->addSql('CREATE INDEX IDX_F65593E54272FC9F ON annonce (domaine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E54272FC9F');
        $this->addSql('DROP TABLE domaine');
        $this->addSql('DROP INDEX IDX_F65593E54272FC9F ON annonce');
        $this->addSql('ALTER TABLE annonce DROP domaine_id');
    }
}
