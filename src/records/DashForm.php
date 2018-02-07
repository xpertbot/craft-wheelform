<?php
/**
 * Wheel Form plugin for Craft CMS 3.x
 *
 * Free Form Builder with Database Integration
 *
 * @link      https://wheelinteractive.com
 * @copyright Copyright (c) 2018 Wheel Interactive
 */

namespace Dashform\records;

use Dashform\DashForm;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Wheel Interactive
 * @package   DashForm
 * @since     0.1
 */
class DashForm extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dashform_dashform}}';
    }
}
