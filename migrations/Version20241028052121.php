<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028052121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, user_id INT NOT NULL, designation VARCHAR(20) DEFAULT NULL, rccm VARCHAR(50) DEFAULT NULL, idnat VARCHAR(40) DEFAULT NULL, adresse VARCHAR(50) DEFAULT NULL, expediteur VARCHAR(50) DEFAULT NULL, INDEX IDX_E6E132B4C54C8C93 (type_id), INDEX IDX_E6E132B4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4C54C8C93 FOREIGN KEY (type_id) REFERENCES type_organisation (id)');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE type_organisation ADD designation VARCHAR(20) DEFAULT NULL, ADD is_active TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B4C54C8C93');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B4A76ED395');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('ALTER TABLE type_organisation DROP designation, DROP is_active');
    }
}
