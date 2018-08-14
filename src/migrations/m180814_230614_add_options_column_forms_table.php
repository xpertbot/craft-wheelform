<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180814_230614_add_options_column_forms_table migration.
 */
class m180814_230614_add_options_column_forms_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_forms}}', 'options', $this->text());
        $this->addColumn('{{%wheelform_messages}}', 'values', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_forms}}', 'options');
        $this->dropColumn('{{%wheelform_messages}}', 'values');
    }
}
