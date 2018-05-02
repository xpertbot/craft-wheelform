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
            $this->createTable(
                '{{%wheelform_forms}}',
                [
                    'id' => $this->primaryKey(),
                    'site_id' => $this->integer()->notNull(),
                    'name' => $this->string()->notNull(),
                    'to_email' => $this->string()->notNull(),
                    'active' => $this->tinyInteger()->notNull()->defaultValue(1),
                    'send_email' => $this->tinyInteger()->notNull()->defaultValue(0),
                    'recaptcha' => $this->tinyInteger()->notNull()->defaultValue(0),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime(),
                    'uid' => $this->uid(),
                ]
            );

            $this->createTable(
                '{{%wheelform_form_fields}}',
                [
                    'id' => $this->primaryKey(),
                    'form_id' => $this->integer(),
                    'name' => $this->string()->notNull(),
                    'type' => $this->string()->notNull(),
                    'required' => $this->tinyInteger()->notNull()->defaultValue(0),
                    'index_view' => $this->tinyInteger()->notNull()->defaultValue(0),
                    'active' => $this->tinyInteger()->notNull()->defaultValue(1),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime(),
                    'uid' => $this->uid(),
                ]
            );

            $this->createTable(
                '{{%wheelform_messages}}',
                [
                    'id' => $this->primaryKey(),
                    'form_id' => $this->integer()->notNull(),
                    'read' => $this->tinyInteger()->notNull()->defaultValue(0),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime(),
                    'uid' => $this->uid(),
                ]
            );

            $this->createTable(
                '{{%wheelform_message_values}}',
                [
                    'id' => $this->primaryKey(),
                    'message_id' => $this->integer()->notNull(),
                    'field_id' => $this->integer()->notNull(),
                    'value' => $this->text(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime(),
                    'uid' => $this->uid(),
                ]
            );

            $tablesCreated = true;
        }

        return $tablesCreated;
    }

    protected function createIndexes()
    {
        $this->createIndex(null, '{{%wheelform_form_fields}}', ['active']);
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
            NULL
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_form_fields}}', 'form_id'),
            '{{%wheelform_form_fields}}',
            'form_id',
            '{{%wheelform_forms}}',
            'id',
            'CASCADE',
            NULL
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_messages}}', 'form_id'),
            '{{%wheelform_messages}}',
            'form_id',
            '{{%wheelform_forms}}',
            'id',
            'CASCADE',
            NULL
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_message_values}}', 'message_id'),
            '{{%wheelform_message_values}}',
            'message_id',
            '{{%wheelform_messages}}',
            'id',
            'CASCADE',
            NULL
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%wheelform_message_values}}', 'field_id'),
            '{{%wheelform_message_values}}',
            'field_id',
            '{{%wheelform_form_fields}}',
            'id',
            'CASCADE',
            NULL
        );

    }

    protected function insertDefaultData()
    {
        $this->insert(
            '{{%wheelform_forms}}',
            [
                'site_id' => Craft::$app->sites->currentSite->id,
                'name' => 'Contact Form',
                "to_email" => "user@example.com",
                'active' => 1,
                'send_email' => 1,
                'recaptcha' => 0,
            ]
        );
        $this->insert(
            '{{%wheelform_form_fields}}',
            [
            "form_id" => 1,
            "type" => 'email',
            "name" => "email",
            "required" => 1,
            ]
        );
        $this->insert(
            '{{%wheelform_form_fields}}',
            [
            "form_id" => 1,
            "type" => 'text',
            "name" => "name",
            "required" => 0,
            ]
        );
        $this->insert(
            '{{%wheelform_form_fields}}',
            [
            "form_id" => 1,
            "type" => 'text',
            "name" => "message",
            "required" => 1,
            ]
        );
    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%wheelform_message_values}}');
        $this->dropTableIfExists('{{%wheelform_messages}}');
        $this->dropTableIfExists('{{%wheelform_form_fields}}');
        $this->dropTableIfExists('{{%wheelform_forms}}');
    }
}
