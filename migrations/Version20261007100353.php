<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261007100353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sms_message ADD provider_message_id VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_sms_message_status_created_at ON sms_message (status, created_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_sms_message_status_created_at');
        $this->addSql('ALTER TABLE sms_message DROP provider_message_id');
    }
}
