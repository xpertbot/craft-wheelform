<?php
namespace wheelform\models\tools;

use Exception;
use craft\base\Model;
use craft\errors\UploadFailedException;

class ImportFile extends Model
{
    public $jsonFile;

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            [['jsonFile'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => ['json'],
                'checkExtensionByMimeType' => false, // This is very unsafe, but we need to do it because Yii does not do it correctly for PHP 8.1 https://github.com/yiisoft/yii2/issues/19243
            ]
        ];
    }

    public function getTempPath()
    {

        if ($this->jsonFile->getHasError()) {
            throw new UploadFailedException($this->jsonFile->error);
        }

        // Move the uploaded file to the temp folder
        $tempPath = $this->jsonFile->saveAsTempFile();

        if ($tempPath === false) {
            throw new UploadFailedException(UPLOAD_ERR_CANT_WRITE);
        }

        return $tempPath;
    }
}
