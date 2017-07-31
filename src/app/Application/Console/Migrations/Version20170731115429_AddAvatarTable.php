<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170731115429_AddAvatarTable extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('avatar');
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('thumbnail', Type::STRING);
        $table->addColumn('standard', Type::STRING);
        $table->addColumn('created_at', Type::INTEGER);
        $table->addColumn('updated_at', Type::INTEGER);
        $table->addIndex(['user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();
        $schema->dropTable('avatar');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
