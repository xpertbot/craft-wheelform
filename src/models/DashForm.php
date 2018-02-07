<?php
/**
 * Wheel Form plugin for Craft CMS 3.x
 *
 * Free Form Builder with Database Integration
 *
 * @link      https://wheelinteractive.com
 * @copyright Copyright (c) 2018 Wheel Interactive
 */

namespace Dashform\models;

use Dashform\DashForm;

use Craft;
use craft\base\Model;

/**
 * @author    Wheel Interactive
 * @package   DashForm
 * @since     0.1
 */
class DashForm extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
