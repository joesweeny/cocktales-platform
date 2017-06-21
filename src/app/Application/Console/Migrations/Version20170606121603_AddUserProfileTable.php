<?php

namespace Cocktales\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170606121603_AddUserProfileTable extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setup();

        $table = $schema->createTable('user_profile');
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('username', Type::STRING)->setNotnull(false);
        $table->addColumn('first_name', Type::STRING)->setNotnull(false);
        $table->addColumn('last_name', Type::STRING)->setNotnull(false);
        $table->addColumn('location', Type::STRING)->setNotnull(false);
        $table->addColumn('slogan', Type::STRING)->setNotnull(false);
        $table->addColumn('created_at', Type::DATETIME);
        $table->addColumn('updated_at', Type::DATETIME);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->setup();
        $schema->dropTable('user_profile');
    }

    private function setup()
    {
        $this->schemaManager = $this->version->getConfiguration()
            ->getConnection()
            ->getSchemaManager();
    }
}
