<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Services\StateMigrator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190405151642 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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

        $this->addSql('CREATE TABLE state (
                                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                                name VARCHAR(255) NOT NULL, 
                                UNIQUE INDEX UNIQ_A393D2FB5E237E06 (name), 
                                PRIMARY KEY(id)
                            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE state');
    }

    public function postUp(Schema $schema): void
    {
        parent::postUp($schema);

        $migrator = $this->container->get(StateMigrator::class);
        $migrator->migrate();
    }
}
