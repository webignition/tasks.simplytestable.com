<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190426174355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DE097880');
        $this->addSql('DROP INDEX IDX_527EDB25DE097880 ON task');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE097880 FOREIGN KEY (output_id) REFERENCES output (id)'
        );
        $this->addSql('CREATE INDEX IDX_527EDB25DE097880 ON task (output_id)');
    }
}
