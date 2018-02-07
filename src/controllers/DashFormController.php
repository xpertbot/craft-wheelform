<?php
/**
 * Wheel Form plugin for Craft CMS 3.x
 *
 * Free Form Builder with Database Integration
 *
 * @link      https://wheelinteractive.com
 * @copyright Copyright (c) 2018 Wheel Interactive
 */

namespace Dashform\controllers;

use Dashform\DashForm;

use Craft;
use craft\web\Controller;

/**
 * @author    Wheel Interactive
 * @package   DashForm
 * @since     0.1
 */
class DashFormController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DashFormController actionIndex() method';

        return $result;
    }

    /**
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the DashFormController actionDoSomething() method';

        return $result;
    }
}
