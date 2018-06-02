<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180602_053339_AddLabelToField migration.
 */
class m180602_053339_AddLabelToField extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'label', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_form_fields}}', 'label');
        return false;
    }
}
