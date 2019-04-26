<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190426162243 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE task ADD url_id INT UNSIGNED DEFAULT NULL, DROP url');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2581CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id)');
        $this->addSql('CREATE INDEX IDX_527EDB2581CFDAE7 ON task (url_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2581CFDAE7');
        $this->addSql('DROP INDEX IDX_527EDB2581CFDAE7 ON task');
        $this->addSql('ALTER TABLE task ADD url LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP url_id');
    }
}
