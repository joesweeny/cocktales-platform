<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170908113936_AddCocktailTable extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('cocktail');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('name', Type::STRING);
        $table->addColumn('origin', Type::TEXT);
        $table->addColumn('created_at', Type::INTEGER);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();
        $schema->dropTable('cocktail');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
