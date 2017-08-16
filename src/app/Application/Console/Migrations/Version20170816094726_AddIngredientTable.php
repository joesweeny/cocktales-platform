<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170816094726_AddIngredientTable extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('ingredient');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('name', Type::STRING);
        $table->addColumn('category', Type::STRING);
        $table->addColumn('type', Type::STRING);
        $table->addColumn('created_at', Type::INTEGER);
        $table->addColumn('updated_at', Type::INTEGER);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();
        $schema->dropTable('ingredient');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
