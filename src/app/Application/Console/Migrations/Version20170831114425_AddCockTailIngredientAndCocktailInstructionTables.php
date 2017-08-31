<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170831114425_AddCockTailIngredientAndCocktailInstructionTables extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('cocktail_ingredient');
        $table->addColumn('cocktail_id', Type::BINARY)->setLength(16);
        $table->addColumn('ingredient_id', Type::BINARY)->setLength(16);
        $table->addColumn('order_number', Type::INTEGER);
        $table->addColumn('quantity', Type::INTEGER);
        $table->addColumn('measurement', Type::STRING);

        $table = $schema->createTable('cocktail_instruction');
        $table->addColumn('cocktail_id', Type::BINARY)->setLength(16);
        $table->addColumn('instruction_id', Type::INTEGER);
        $table->addColumn('text', Type::TEXT);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();
        $schema->dropTable('cocktail_ingredient');
        $schema->dropTable('cocktail_instruction');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
