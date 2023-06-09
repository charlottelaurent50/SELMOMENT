<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609063648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD type_id INT DEFAULT NULL, ADD categorie_id INT DEFAULT NULL, ADD statut_id INT DEFAULT NULL, ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_F65593E5C54C8C93 ON annonce (type_id)');
        $this->addSql('CREATE INDEX IDX_F65593E5BCF5E72D ON annonce (categorie_id)');
        $this->addSql('CREATE INDEX IDX_F65593E5F6203804 ON annonce (statut_id)');
        $this->addSql('CREATE INDEX IDX_F65593E5F2C56620 ON annonce (compte_id)');
        $this->addSql('ALTER TABLE image ADD annonce_id INT DEFAULT NULL, ADD evenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F8805AB2F ON image (annonce_id)');
        $this->addSql('CREATE INDEX IDX_C53D045FFD02F13 ON image (evenement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5C54C8C93');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5BCF5E72D');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5F6203804');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5F2C56620');
        $this->addSql('DROP INDEX IDX_F65593E5C54C8C93 ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E5BCF5E72D ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E5F6203804 ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E5F2C56620 ON annonce');
        $this->addSql('ALTER TABLE annonce DROP type_id, DROP categorie_id, DROP statut_id, DROP compte_id');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F8805AB2F');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFD02F13');
        $this->addSql('DROP INDEX IDX_C53D045F8805AB2F ON image');
        $this->addSql('DROP INDEX IDX_C53D045FFD02F13 ON image');
        $this->addSql('ALTER TABLE image DROP annonce_id, DROP evenement_id');
    }
}
