<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190409163903 extends AbstractMigration
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

        $this->addSql('CREATE TABLE task (
                                id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
                                state_id INT UNSIGNED NOT NULL, 
                                type_id INT UNSIGNED NOT NULL, 
                                time_period_id INT UNSIGNED DEFAULT NULL, 
                                output_id INT UNSIGNED DEFAULT NULL, 
                                identifier VARCHAR(255) DEFAULT NULL COLLATE latin1_bin, 
                                job_identifier VARCHAR(255) DEFAULT NULL COLLATE latin1_bin, 
                                url LONGTEXT NOT NULL COLLATE utf8_unicode_ci, 
                                parameters LONGTEXT NOT NULL, 
                                UNIQUE INDEX UNIQ_527EDB25772E836A (identifier), 
                                UNIQUE INDEX UNIQ_527EDB251D591377 (job_identifier), 
                                INDEX IDX_527EDB255D83CC1 (state_id), 
                                INDEX IDX_527EDB25C54C8C93 (type_id), 
                                UNIQUE INDEX UNIQ_527EDB257EFD7106 (time_period_id), 
                                INDEX IDX_527EDB25DE097880 (output_id), 
                                PRIMARY KEY(id)
                            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB255D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C54C8C93 FOREIGN KEY (type_id) REFERENCES task_type (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257EFD7106 FOREIGN KEY (time_period_id) REFERENCES time_period (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE097880 FOREIGN KEY (output_id) REFERENCES output (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE task');
    }
}
