<?php

namespace wheelform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190405_025303_add_class_to_form_field migration.
 */
class m190405_025303_add_class_to_form_field extends Migration
{
   /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%wheelform_form_fields}}', 'class', $this->text());
        $this->addColumn('{{%wheelform_form_fields}}', 'config', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%wheelform_form_fields}}', 'class');
        $this->dropColumn('{{%wheelform_form_fields}}', 'config');
    }
}
