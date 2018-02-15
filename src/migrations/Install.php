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
                    'form_name' => $this->string()->notNull(),
                    'to_email' => $this->string()->notNull(),
                    'fields' => $this->text()->notNull(),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                ]
            );

            $this->createTable(
                '{{%wheelform_messages}}',
                [
                    'id' => $this->primaryKey(),
                    'form_id' => $this->integer()->notNull(),
                    'values' => $this->text()->notNull(),
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
        return true;
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
            [
                'site_id' => Craft::$app->sites->currentSite->id,
                'form_name' => 'Contact Form',
                "to_email" => "user@example.com",
                'fields' => json_encode([
                    [
                    "type" => 'email',
                    "name" => "email",
                    "required" => true,
                    ],
                    [
                    "type" => 'text',
                    "name" => "user_name",
                    "required" => true,
                    ],
                    [
                    "type" => 'text',
                    "name" => "message",
                    "required" => true,
                    ],

                ])
            ]
        );
    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%wheelform_messages}}');
        $this->dropTableIfExists('{{%wheelform_forms}}');
    }
}
