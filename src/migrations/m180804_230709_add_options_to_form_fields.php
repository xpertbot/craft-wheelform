<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m180804_230709_add_options_to_form_fields migration.
 */
class m180804_230709_add_options_to_form_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'options', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_form_fields}}', 'options');
    }
}
