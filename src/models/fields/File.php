<?php
namespace wheelform\models\fields;

class File extends BaseFieldType
{
    public $name = "File";

    public $type = "file";

    public function getConfig()
    {
        return [
            [
                'name' => 'extensions',
                'type' => 'text',
                'label' => 'File Extension Restrictions',
                'description' => 'Comma separated file extensions to be allowed. Leave Blank for all.',
                'value' => '',
            ],
            [
                'name' => 'display_required_attribute',
                'type' => 'boolean',
                'label' => 'Display Required Attribute',
                'value' => false,
                'condition' => 'required', // using lodash _.get we can call nested object e.g. options.placeholder
                'display_side' => 'left',
            ]
        ];
    }
}
