<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180602_051517_AddOrderToField migration.
 */
class m180602_051517_AddOrderToField extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'order', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_form_fields}}', 'order');
        return false;
    }
}
