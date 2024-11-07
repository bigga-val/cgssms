<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106181342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recharge ADD utilisateur_id INT DEFAULT NULL, ADD clientid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recharge ADD CONSTRAINT FK_B6702F51FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recharge ADD CONSTRAINT FK_B6702F51F3FD2D2E FOREIGN KEY (clientid_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6702F51FB88E14F ON recharge (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_B6702F51F3FD2D2E ON recharge (clientid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recharge DROP FOREIGN KEY FK_B6702F51FB88E14F');
        $this->addSql('ALTER TABLE recharge DROP FOREIGN KEY FK_B6702F51F3FD2D2E');
        $this->addSql('DROP INDEX IDX_B6702F51FB88E14F ON recharge');
        $this->addSql('DROP INDEX IDX_B6702F51F3FD2D2E ON recharge');
        $this->addSql('ALTER TABLE recharge DROP utilisateur_id, DROP clientid_id');
    }
}
