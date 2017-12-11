<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171211200342_AddSessionTokenTable extends AbstractMigration
{
    private $schemaManager;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setUp();

        $table = $schema->createTable('session_token');
        $table->addColumn('token', Type::BINARY)->setLength(16);
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('created_at', Type::INTEGER)->setNotnull(false);
        $table->addColumn('expiry', Type::INTEGER)->setNotnull(false);
        $table->setPrimaryKey(['token']);
        $table->addIndex(['user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setUp();

        $schema->dropTable('session_token');
    }

    private function setUp()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
