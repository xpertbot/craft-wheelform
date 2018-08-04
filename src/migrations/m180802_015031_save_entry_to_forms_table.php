<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180802_015031_save_entry_to_forms_table migration.
 */
class m180802_015031_save_entry_to_forms_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->addColumn('{{%wheelform_forms}}', 'save_entry', $this->tinyInteger()->notNull()->defaultValue(1));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_forms}}', 'save_entry');
    }
}
