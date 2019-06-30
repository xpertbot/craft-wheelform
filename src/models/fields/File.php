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
            ]
        ];
    }
}
