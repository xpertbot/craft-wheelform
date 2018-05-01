<?php

namespace Wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180430_232743_add_read_column_to_message migration.
 */
class m180430_232743_add_read_column_to_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_messages}}', 'read', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_messages}}', 'read');
    }
}
