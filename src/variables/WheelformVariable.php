<?php

namespace wheelform\variables;

use wheelform\models\Form;
use wheelform\Plugin;

class WheelformVariable
{
    /**
     * Returns the settings for a particular form.
     *
     * @param int|string $form_id
     * @return mixed
     */
    public function getFormSettings($form_id)
    {
        return Form::find()->where(['id' => $form_id])->with('fields')->one();
    }

    /**
     * Returns the plugin's settings.
     *
     * @return mixed
     */
    public function getPluginSettings()
    {
        return Plugin::getInstance()->getSettings();
    }
}