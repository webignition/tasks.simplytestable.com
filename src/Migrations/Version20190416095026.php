<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416095026 extends AbstractMigration
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

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB257EFD7106');
        $this->addSql('DROP TABLE time_period');
        $this->addSql('DROP INDEX UNIQ_527EDB257EFD7106 ON task');
        $this->addSql('ALTER TABLE task DROP time_period_id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE time_period (id INT UNSIGNED AUTO_INCREMENT NOT NULL, start_date_time DATETIME NOT NULL, end_date_time DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE task ADD time_period_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257EFD7106 FOREIGN KEY (time_period_id) REFERENCES time_period (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB257EFD7106 ON task (time_period_id)');
    }
}
