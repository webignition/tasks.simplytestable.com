<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190426161743 extends AbstractMigration
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

        $this->addSql('
CREATE TABLE url (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
    uri LONGTEXT NOT NULL COLLATE utf8_unicode_ci, 
    hash VARCHAR(32) NOT NULL, 
    UNIQUE INDEX UNIQ_F47645AED1B862B8 (hash), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE url');
    }
}
