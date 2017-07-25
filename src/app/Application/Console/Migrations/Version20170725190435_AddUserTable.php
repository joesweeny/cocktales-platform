<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170725190435_AddUserTable extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setup();

        $table = $schema->createTable('user');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('email', Type::STRING)->setNotnull(false);
        $table->addColumn('password', Type::STRING)->setNotnull(false);
        $table->addColumn('created_at', Type::DATETIME);
        $table->addColumn('updated_at', Type::DATETIME);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setup();
        $schema->dropTable('user');
    }

    private function setup()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
