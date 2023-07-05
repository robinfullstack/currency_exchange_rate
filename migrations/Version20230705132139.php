<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705132139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update rate field in exchange_rate table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exchange_rate MODIFY rate NUMERIC(10, 2)');
        $this->addSql('UPDATE exchange_rate SET rate = ROUND(rate, 2)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exchange_rate MODIFY rate FLOAT');
    }
}
