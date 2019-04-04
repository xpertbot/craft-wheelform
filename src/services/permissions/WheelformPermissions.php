<?php
namespace wheelform\services\permissions;

use Craft;
use craft\base\Component;

use wheelform\db\Form;

class WheelformPermissions extends Component
{
    public static function getAllPermissions()
    {
        $forms = Form::find()->where(['active' => 1])->all();
        $permissions = [
            'wheelform_new_form' => [
                'label' => Craft::t('wheelform', 'Create new form'),
            ]
        ];
        foreach($forms as $form) {
            $permissions['wheelform_edit_form_'.$form->id] = [
                'label' => Craft::t('wheelform', 'Edit') . ' - ' . $form->name,
                'nested' => [
                    'wheelform_view_entries_' . $form->id => [
                        'label' =>  $form->name . ' - ' .Craft::t('wheelform', 'Entries'),
                    ],
                    'wheelform_change_settings_' . $form->id => [
                        'label' => $form->name . ' - ' . Craft::t('wheelform', 'Settings'),
                    ]
                ]
            ];
        }
        return $permissions;
    }
}
