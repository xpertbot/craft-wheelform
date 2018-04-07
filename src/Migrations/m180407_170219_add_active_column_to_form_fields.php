<?php

namespace Wheelform\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

/**
 * m180407_170219_add_active_column_to_form_fields migration.
 */
class m180407_170219_add_active_column_to_form_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'active', $this->boolean()->notNull()->defaultValue(1));
        $this->createIndex(null, '{{%wheelform_form_fields}}', ['active']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        MigrationHelper::dropIndex('{{%wheelform_form_fields}}', ['active'], false, $this);
        $this->dropColumn('{{%wheelform_form_fields}}', 'active');
    }
}
