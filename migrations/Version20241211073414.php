<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211073414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_groupe (id INT AUTO_INCREMENT NOT NULL, contact_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, INDEX IDX_942BBBA0E7A1254A (contact_id), INDEX IDX_942BBBA07A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_groupe ADD CONSTRAINT FK_942BBBA0E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE contact_groupe ADD CONSTRAINT FK_942BBBA07A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_groupe DROP FOREIGN KEY FK_942BBBA0E7A1254A');
        $this->addSql('ALTER TABLE contact_groupe DROP FOREIGN KEY FK_942BBBA07A45358C');
        $this->addSql('DROP TABLE contact_groupe');
    }
}
