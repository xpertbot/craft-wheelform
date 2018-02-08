<?php
 namespace Wheelform\Migrations;

use Craft;
use craft\db\Migration;

class Install extends Migration
{

    public $driver;

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%wheelform_forms}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%wheelform_forms}}',
                [
                    'id' => $this->primaryKey(),
                    'site_id' => $this->integer()->notNull(),
                    'name' => $this->string(255)->notNull(),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                ]
            );

            $this->createTable(
                '{{%wheelform_messages}}',
                [
                    'id' => $this->primaryKey(),
                    'name' => $this->string(100)->notNull(),
                    'email' => $this->string(100)->notNull(),
                    'message' => $this->text()->notNull(),
                    'form_id' => $this->integer()->notNull(),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%wheelform_forms}}',
                'name',
                true
            ),
            '{{%wheelform_forms}}',
            'name',
            true
        );
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_forms}}', 'site_id'),
            '{{%wheelform_forms}}',
            'site_id',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_messages}}', 'form_id'),
            '{{%wheelform_messages}}',
            'form_id',
            '{{%wheelform_forms}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    protected function insertDefaultData()
    {
        $this->insert(
            '{{%wheelform_forms}}',
            ['name' => "Contact Form"]
        );
    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%wheelform_forms}}');
        $this->dropTableIfExists('{{%wheelform_messages}}');
    }
}
