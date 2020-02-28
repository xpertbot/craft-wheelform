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
                'label' => Craft::t('wheelform', 'Create new forms'),
            ],
            'wheelform_manage_all_forms' => [
                'label' => Craft::t('wheelform', 'Manage all forms'),
                'nested' => [
                    'wheelform_view_all_forms_entries' => [
                        'label' => Craft::t('wheelform', 'View all forms entries'),
                    ],
                    'wheelform_change_all_forms_settings' => [
                        'label' => Craft::t('wheelform', 'Change all forms settings'),
                    ],
                ]
            ],
        ];
        foreach($forms as $form) {
            $permissions['wheelform_edit_form_'.$form->id] = [
                'label' => Craft::t('wheelform', 'Manage') . ' - ' . $form->name,
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
