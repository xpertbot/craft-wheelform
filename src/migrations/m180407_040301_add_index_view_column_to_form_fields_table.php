<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180407_040301_add_index_view_column_to_form_fields_table migration.
 */
class m180407_040301_add_index_view_column_to_form_fields_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'index_view', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_form_fields}}', 'index_view');
    }
}
