<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705214458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce CHANGE motif_desactivage motif_archivage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE compte CHANGE motif_archivage motif_desactivage VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce CHANGE motif_archivage motif_desactivage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE compte CHANGE motif_desactivage motif_archivage VARCHAR(255) DEFAULT NULL');
    }
}
