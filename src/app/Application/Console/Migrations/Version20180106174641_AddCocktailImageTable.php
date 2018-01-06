<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180106174641_AddCocktailImageTable extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('cocktail_image');
        $table->addColumn('cocktail_id', Type::BINARY)->setLength(16);
        $table->addColumn('filename', Type::STRING);
        $table->addColumn('created_at', Type::INTEGER);
        $table->setPrimaryKey(['cocktail_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();
        $schema->dropTable('cocktail_image');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
