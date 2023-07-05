<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Types\Types;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704204750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exchange_rate table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('exchange_rate');
        $table->addColumn('id', Types::INTEGER)
            ->setAutoincrement(true)
            ->setNotnull(true);
        $table->addColumn('rate', Types::FLOAT)
        ->setColumnOption('precision', 10)
        ->setColumnOption('scale', 2);
        $table->addColumn('base_currency', Types::STRING, ['length' => 3]);
        $table->addColumn('target_currency', Types::STRING, ['length' => 3]);
        $table->addColumn('created_at', Types::DATETIME_MUTABLE);

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('exchange_rate');
    }
}